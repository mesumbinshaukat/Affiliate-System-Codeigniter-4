<?php

namespace App\Controllers;

use App\Models\ListModel;
use App\Models\ListProductModel;
use App\Libraries\ListProtection;

class Lists extends BaseController
{
    public function view($slug)
    {
        $listModel = new ListModel();
        $listProductModel = new ListProductModel();
        $listSectionModel = new \App\Models\ListSectionModel();

        $list = $listModel->getListWithDetails($slug);

        if (!$list) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $isOwner = $this->isLoggedIn() && $this->session->get('user_id') == $list['user_id'];
        $accessKey = 'list_access_' . $list['id'];
        $hasAccess = $isOwner || (bool) $this->session->get($accessKey);
        $requiresAccess = ($list['protection_type'] ?? 'none') !== 'none' && !$hasAccess;

        // Check if list is private or draft
        if ($list['status'] !== 'published') {
            // Only owner can view
            if (!$isOwner) {
                throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
            }
        }

        if ($requiresAccess) {
            $this->data['list'] = $list;
            $this->data['requiresAccess'] = true;
            $this->data['accessError'] = session('access_error');
            $this->data['isOwner'] = $isOwner;
            $this->data['accessSlug'] = $slug;

            return view('lists/view', $this->data);
        }

        // Increment views only when access is granted
        $listModel->incrementViews($list['id']);

        $protectionShareNote = $this->buildProtectionShareNote($list, $isOwner, $hasAccess);

        $this->data['list'] = $list;
        $this->data['products'] = $listProductModel->getListProductsGroupedBySection($list['id']);
        $this->data['sections'] = $listSectionModel->getListSections($list['id']);
        $this->data['requiresAccess'] = false;
        $this->data['isOwner'] = $isOwner;
        $this->data['protectionShareNote'] = $protectionShareNote;

        return view('lists/view', $this->data);
    }

    public function attemptAccess($slug)
    {
        if (strtolower($this->request->getMethod()) !== 'post') {
            return redirect()->to('/list/' . $slug);
        }

        $listModel = new ListModel();
        $list = $listModel->getListWithDetails($slug);

        if (!$list || ($list['protection_type'] ?? 'none') === 'none') {
            return redirect()->to('/list/' . $slug);
        }

        $isOwner = $this->isLoggedIn() && $this->session->get('user_id') == $list['user_id'];
        if ($isOwner) {
            $this->session->set('list_access_' . $list['id'], true);
            return redirect()->to('/list/' . $slug);
        }

        $type = $list['protection_type'];
        $failedMessage = 'Toegang geweigerd. Controleer uw antwoord en probeer het opnieuw.';

        if ($type === 'password') {
            $input = trim((string) $this->request->getPost('protection_password'));
            if ($input !== '' && ListProtection::verify($input, $list['protection_password'])) {
                $this->session->set('list_access_' . $list['id'], true);
                return redirect()->to('/list/' . $slug);
            }
        } elseif ($type === 'question') {
            $input = trim((string) $this->request->getPost('protection_answer'));
            if ($input !== '' && ListProtection::verify($input, $list['protection_answer'])) {
                $this->session->set('list_access_' . $list['id'], true);
                return redirect()->to('/list/' . $slug);
            }
        }

        return redirect()->back()->with('access_error', $failedMessage)->withInput();
    }

    private function buildProtectionShareNote(array $list, bool $isOwner, bool $hasAccess): ?string
    {
        if (($list['protection_type'] ?? 'none') === 'none') {
            return null;
        }

        if (!$isOwner && !$hasAccess) {
            return null;
        }

        if ($list['protection_type'] === 'password') {
            $password = ListProtection::decrypt($list['protection_password'] ?? null);
            if ($password) {
                return 'Wachtwoord voor toegang: ' . $password;
            }
        }

        if ($list['protection_type'] === 'question') {
            $answer = ListProtection::decrypt($list['protection_answer'] ?? null);
            if ($list['protection_question'] && $answer) {
                return 'Beveiligingsvraag: ' . $list['protection_question'] . ' | Antwoord: ' . $answer;
            }
        }

        return null;
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

    public function unclaimProduct()
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

        if (empty($listProduct['claimed_at'])) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Dit item staat niet als gekocht gemarkeerd',
            ])->setStatusCode(409);
        }

        if ($listProductModel->unclaimProduct($listProductId)) {
            if (function_exists('log_message')) {
                log_message('info', sprintf(
                    'Product unclaimed manually: list_id=%d, list_product_id=%d',
                    $listId,
                    $listProductId
                ));
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Item staat weer open voor andere kopers',
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Er is een fout opgetreden. Probeer het opnieuw.',
        ])->setStatusCode(500);
    }
}
