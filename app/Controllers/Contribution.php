<?php

namespace App\Controllers;

use App\Models\ContributionModel;
use App\Models\ListProductModel;
use App\Models\ListModel;

class Contribution extends BaseController
{
    protected $session;

    public function __construct()
    {
        $this->session = \Config\Services::session();
    }

    /**
     * Add a contribution to a group gift
     */
    public function add()
    {
        if (strtolower($this->request->getMethod()) !== 'post') {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid request method',
            ]);
        }

        $listProductId = $this->request->getPost('list_product_id');
        $contributorName = trim($this->request->getPost('contributor_name'));
        $contributorEmail = trim($this->request->getPost('contributor_email'));
        $amount = $this->request->getPost('amount');
        // Normalize decimal separator (handle both comma and dot)
        $amount = str_replace(',', '.', $amount);
        $amount = (float) $amount;
        $message = trim($this->request->getPost('message'));
        $isAnonymous = (bool) $this->request->getPost('is_anonymous');

        // Validate required fields
        if (empty($listProductId) || empty($contributorName) || empty($amount)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Naam en bedrag zijn verplicht',
            ]);
        }

        // Validate amount
        $amount = (float) $amount;
        if ($amount <= 0) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Bedrag moet groter zijn dan €0',
            ]);
        }

        // Verify list product exists and is a group gift
        $listProductModel = new ListProductModel();
        $listProduct = $listProductModel->select('list_products.*, products.title, products.price')
            ->join('products', 'products.id = list_products.product_id')
            ->find($listProductId);

        if (!$listProduct) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Product niet gevonden',
            ]);
        }

        if (!$listProduct['is_group_gift']) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Dit product accepteert geen groepsbijdragen',
            ]);
        }

        // Check if funding goal is already reached
        $contributionModel = new ContributionModel();
        $targetAmount = (float) $listProduct['target_amount'];
        $currentAmount = $contributionModel->getTotalContributed($listProductId);

        if ($currentAmount >= $targetAmount) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Het doel is al bereikt voor dit product',
            ]);
        }

        // Don't allow contribution that significantly exceeds target
        $remaining = max($targetAmount - $currentAmount, 0);
        if ($amount > ($remaining + 0.001)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => sprintf('Maximaal €%s kan nog worden bijgedragen', number_format($remaining, 2, ',', '')),
            ]);
        }

        // Add contribution
        $contributionData = [
            'list_product_id' => $listProductId,
            'contributor_name' => $contributorName,
            'contributor_email' => !empty($contributorEmail) ? $contributorEmail : null,
            'amount' => $amount,
            'message' => !empty($message) ? $message : null,
            'is_anonymous' => $isAnonymous ? 1 : 0,
            'status' => 'completed',
        ];

        $contributionId = $contributionModel->insert($contributionData);

        if ($contributionId) {
            // Get updated stats
            $newTotal = $contributionModel->getTotalContributed($listProductId);
            $percentage = $contributionModel->getContributionPercentage($listProductId, $targetAmount);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Bedankt voor je bijdrage!',
                'contribution_id' => $contributionId,
                'new_total' => $newTotal,
                'percentage' => $percentage,
                'is_complete' => $newTotal >= $targetAmount,
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Fout bij het toevoegen van bijdrage',
        ]);
    }

    /**
     * Get contributions for a specific product
     */
    public function getProductContributions($listProductId)
    {
        $contributionModel = new ContributionModel();
        $contributions = $contributionModel->getProductContributions($listProductId);

        // Filter out names for anonymous contributors
        $contributions = array_map(function ($contribution) {
            if ($contribution['is_anonymous']) {
                $contribution['contributor_name'] = 'Anoniem';
            }
            return $contribution;
        }, $contributions);

        return $this->response->setJSON([
            'success' => true,
            'contributions' => $contributions,
        ]);
    }

    /**
     * Toggle group gift status for a product
     */
    public function toggleGroupGift()
    {
        if (!$this->session->has('user_id')) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Please login to continue',
            ]);
        }

        if (strtolower($this->request->getMethod()) !== 'post') {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid request method',
            ]);
        }

        $listProductId = $this->request->getPost('list_product_id');
        $isGroupGift = (bool) $this->request->getPost('is_group_gift');
        $targetAmount = $this->request->getPost('target_amount');

        if (empty($listProductId)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Product ID is verplicht',
            ]);
        }

        // Verify ownership
        $listProductModel = new ListProductModel();
        $listModel = new ListModel();
        
        $listProduct = $listProductModel->find($listProductId);
        if (!$listProduct) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Product niet gevonden',
            ]);
        }

        $userId = $this->session->get('user_id');
        if (!$listModel->canUserEdit($listProduct['list_id'], $userId)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Geen toegang',
            ]);
        }

        // Validate target amount if enabling group gift
        if ($isGroupGift) {
            $targetAmount = (float) $targetAmount;
            if ($targetAmount <= 0) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Doelbedrag moet groter zijn dan €0',
                ]);
            }
        } else {
            $targetAmount = null;
        }

        // Update product
        $updateData = [
            'is_group_gift' => $isGroupGift ? 1 : 0,
            'target_amount' => $targetAmount,
        ];

        if ($listProductModel->update($listProductId, $updateData)) {
            return $this->response->setJSON([
                'success' => true,
                'message' => $isGroupGift ? 'Groepscadeau ingeschakeld' : 'Groepscadeau uitgeschakeld',
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Fout bij het bijwerken',
        ]);
    }
}
