<?php

namespace App\Controllers;

use App\Models\ListModel;
use App\Models\CategoryModel;
use App\Models\ProductModel;
use App\Models\ListProductModel;
use App\Models\ClickModel;
use App\Libraries\BolComAPI;

class Dashboard extends BaseController
{
    public function __construct()
    {
        helper('text');
    }

    public function index()
    {
        $redirect = $this->requireLogin();
        if ($redirect) return $redirect;

        $listModel = new ListModel();
        $clickModel = new ClickModel();

        $userId = $this->session->get('user_id');

        $this->data['lists'] = $listModel->getUserLists($userId, true);
        $this->data['totalLists'] = count($this->data['lists']);
        $this->data['totalClicks'] = $clickModel->where('user_id', $userId)->countAllResults();

        return view('dashboard/index', $this->data);
    }

    public function lists()
    {
        $redirect = $this->requireLogin();
        if ($redirect) return $redirect;

        $listModel = new ListModel();
        $userId = $this->session->get('user_id');

        $this->data['lists'] = $listModel->getUserLists($userId, true);

        return view('dashboard/lists', $this->data);
    }

    public function createList()
    {
        $redirect = $this->requireLogin();
        if ($redirect) return $redirect;

        $categoryModel = new CategoryModel();
        $this->data['categories'] = $categoryModel->getActiveCategories();

        if ($this->request->getMethod() === 'post') {
            $listModel = new ListModel();

            $title = $this->request->getPost('title');
            $slug = url_title($title, '-', true);

            // Check if slug exists
            $existingSlug = $listModel->where('slug', $slug)->first();
            if ($existingSlug) {
                $slug = $slug . '-' . time();
            }

            $data = [
                'user_id' => $this->session->get('user_id'),
                'category_id' => $this->request->getPost('category_id'),
                'title' => $title,
                'slug' => $slug,
                'description' => $this->request->getPost('description'),
                'status' => $this->request->getPost('status') ?: 'draft',
            ];

            if ($listModel->insert($data)) {
                $listId = $listModel->getInsertID();
                return redirect()->to('/dashboard/list/edit/' . $listId)->with('success', 'List created successfully');
            }

            $errors = $listModel->errors();
            return redirect()->back()->with('error', implode(', ', $errors))->withInput();
        }

        return view('dashboard/create_list', $this->data);
    }

    public function editList($listId)
    {
        $redirect = $this->requireLogin();
        if ($redirect) return $redirect;

        $listModel = new ListModel();
        $categoryModel = new CategoryModel();
        $listProductModel = new ListProductModel();

        $list = $listModel->find($listId);

        if (!$list || $list['user_id'] != $this->session->get('user_id')) {
            return redirect()->to('/dashboard')->with('error', 'List not found');
        }

        $this->data['list'] = $list;
        $this->data['categories'] = $categoryModel->getActiveCategories();
        $this->data['products'] = $listProductModel->getListProducts($listId);

        if ($this->request->getMethod() === 'post') {
            $title = $this->request->getPost('title');
            $slug = url_title($title, '-', true);

            // Check if slug exists (excluding current list)
            $existingSlug = $listModel->where('slug', $slug)
                ->where('id !=', $listId)
                ->first();
            if ($existingSlug) {
                $slug = $slug . '-' . time();
            }

            $data = [
                'category_id' => $this->request->getPost('category_id'),
                'title' => $title,
                'slug' => $slug,
                'description' => $this->request->getPost('description'),
                'status' => $this->request->getPost('status'),
            ];

            if ($listModel->update($listId, $data)) {
                return redirect()->back()->with('success', 'List updated successfully');
            }

            $errors = $listModel->errors();
            return redirect()->back()->with('error', implode(', ', $errors))->withInput();
        }

        return view('dashboard/edit_list', $this->data);
    }

    public function deleteList($listId)
    {
        $redirect = $this->requireLogin();
        if ($redirect) return $redirect;

        $listModel = new ListModel();
        $list = $listModel->find($listId);

        if (!$list || $list['user_id'] != $this->session->get('user_id')) {
            return redirect()->to('/dashboard')->with('error', 'List not found');
        }

        if ($listModel->delete($listId)) {
            return redirect()->to('/dashboard/lists')->with('success', 'List deleted successfully');
        }

        return redirect()->back()->with('error', 'Failed to delete list');
    }

    public function searchProducts()
    {
        $redirect = $this->requireLogin();
        if ($redirect) return $redirect;

        $query = $this->request->getGet('q');
        $results = [];

        if ($query) {
            $bolApi = new BolComAPI();
            $response = $bolApi->searchProducts($query, 20);
            
            if ($response['success']) {
                $results = $response['products'];
            }
        }

        return $this->response->setJSON([
            'success' => true,
            'products' => $results,
        ]);
    }

    public function addProduct()
    {
        $redirect = $this->requireLogin();
        if ($redirect) return $redirect;

        if ($this->request->getMethod() === 'post') {
            $listId = $this->request->getPost('list_id');
            $productData = $this->request->getPost('product');

            // Verify list ownership
            $listModel = new ListModel();
            $list = $listModel->find($listId);

            if (!$list || $list['user_id'] != $this->session->get('user_id')) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'List not found',
                ]);
            }

            // Check if product exists
            $productModel = new ProductModel();
            $product = null;

            if (!empty($productData['external_id'])) {
                $product = $productModel->findByExternalId($productData['external_id'], $productData['source']);
            }

            // Create product if not exists
            if (!$product) {
                $productModel->insert($productData);
                $productId = $productModel->getInsertID();
            } else {
                $productId = $product['id'];
            }

            // Add to list
            $listProductModel = new ListProductModel();
            
            // Get next position
            $maxPosition = $listProductModel->where('list_id', $listId)
                ->selectMax('position')
                ->first();
            $position = ($maxPosition['position'] ?? 0) + 1;

            $listProductModel->addProductToList($listId, $productId, $position);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Product added successfully',
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Invalid request',
        ]);
    }

    public function removeProduct()
    {
        $redirect = $this->requireLogin();
        if ($redirect) return $redirect;

        if ($this->request->getMethod() === 'post') {
            $listId = $this->request->getPost('list_id');
            $productId = $this->request->getPost('product_id');

            // Verify list ownership
            $listModel = new ListModel();
            $list = $listModel->find($listId);

            if (!$list || $list['user_id'] != $this->session->get('user_id')) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'List not found',
                ]);
            }

            $listProductModel = new ListProductModel();
            $listProductModel->removeProductFromList($listId, $productId);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Product removed successfully',
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Invalid request',
        ]);
    }

    public function updateProductPositions()
    {
        $redirect = $this->requireLogin();
        if ($redirect) return $redirect;

        if ($this->request->getMethod() === 'post') {
            $listId = $this->request->getPost('list_id');
            $positions = $this->request->getPost('positions');

            // Verify list ownership
            $listModel = new ListModel();
            $list = $listModel->find($listId);

            if (!$list || $list['user_id'] != $this->session->get('user_id')) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'List not found',
                ]);
            }

            $listProductModel = new ListProductModel();
            $listProductModel->updatePositions($listId, $positions);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Positions updated successfully',
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Invalid request',
        ]);
    }

    public function analytics()
    {
        $redirect = $this->requireLogin();
        if ($redirect) return $redirect;

        $clickModel = new ClickModel();
        $userId = $this->session->get('user_id');

        $this->data['clicks'] = $clickModel->getUserClicks($userId);
        $this->data['totalClicks'] = count($this->data['clicks']);

        return view('dashboard/analytics', $this->data);
    }
}
