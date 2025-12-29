<?php

namespace App\Controllers;

use App\Models\ListModel;
use App\Models\ListProductModel;

class Lists extends BaseController
{
    public function view($slug)
    {
        $listModel = new ListModel();
        $listProductModel = new ListProductModel();

        $list = $listModel->getListWithDetails($slug);

        if (!$list) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        // Check if list is private or draft
        if ($list['status'] !== 'published') {
            // Only owner can view
            if (!$this->isLoggedIn() || $this->session->get('user_id') != $list['user_id']) {
                throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
            }
        }

        // Increment views
        $listModel->incrementViews($list['id']);

        $this->data['list'] = $list;
        $this->data['products'] = $listProductModel->getListProducts($list['id']);

        return view('lists/view', $this->data);
    }

    public function share($slug)
    {
        $listModel = new ListModel();
        $list = $listModel->getListWithDetails($slug);

        if (!$list || $list['status'] !== 'published') {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $this->data['list'] = $list;
        $this->data['shareUrl'] = base_url('list/' . $slug);

        return view('lists/share', $this->data);
    }

    public function claimProduct()
    {
        if (strtolower($this->request->getMethod()) !== 'post') {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid request method',
            ])->setStatusCode(405);
        }

        $json = $this->request->getJSON(true);
        $listProductId = $json['list_product_id'] ?? null;
        $listId = $json['list_id'] ?? null;

        if (!$listProductId || !$listId) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Ontbrekende parameters',
            ])->setStatusCode(400);
        }

        $listModel = new ListModel();
        $list = $listModel->find($listId);

        if (!$list || $list['status'] !== 'published') {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Lijst niet gevonden',
            ])->setStatusCode(404);
        }

        if (empty($list['is_crossable'])) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Deze lijst staat niet toe dat items als gekocht gemarkeerd worden',
            ])->setStatusCode(403);
        }

        $listProductModel = new ListProductModel();
        $listProduct = $listProductModel->find($listProductId);

        if (!$listProduct || $listProduct['list_id'] != $listId) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Product niet gevonden in deze lijst',
            ])->setStatusCode(404);
        }

        if (!empty($listProduct['claimed_at'])) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Dit item is al als gekocht gemarkeerd',
            ])->setStatusCode(409);
        }

        // Generate anonymous subId for tracking
        $subId = 'manual_' . $listId . '_' . $listProductId . '_' . time();

        if ($listProductModel->claimProduct($listProductId, $subId)) {
            // Log the claim for analytics
            if (function_exists('log_message')) {
                log_message('info', sprintf(
                    'Product claimed manually: list_id=%d, list_product_id=%d, subId=%s',
                    $listId,
                    $listProductId,
                    $subId
                ));
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Item succesvol gemarkeerd als gekocht',
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Er is een fout opgetreden. Probeer het opnieuw.',
        ])->setStatusCode(500);
    }
}
