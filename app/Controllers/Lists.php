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
}
