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

        if (strtolower($this->request->getMethod()) === 'post') {
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
                'status' => 'published',
            ];

            if ($listModel->insert($data)) {
                $listId = $listModel->getInsertID();
                // If first_list flag is set, redirect to products tab
                $isFirstList = $this->request->getGet('first_list') === 'true';
                $redirectUrl = '/dashboard/list/edit/' . $listId;
                if ($isFirstList) {
                    $redirectUrl .= '?tab=products';
                }
                return redirect()->to($redirectUrl)->with('success', 'List created successfully');
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
        
        // Get user age for filtering
        $userAge = null;
        $user = $this->session->get('user_id');
        if ($user) {
            $userModel = new \App\Models\UserModel();
            $userData = $userModel->find($user);
            if ($userData && $userData['date_of_birth']) {
                $birthDate = new \DateTime($userData['date_of_birth']);
                $today = new \DateTime();
                $userAge = $today->diff($birthDate)->y;
            }
        }
        
        // Get products from other lists in the same category (excluding products already in current list)
        $productModel = new \App\Models\ProductModel();
        $listProductModel = new ListProductModel();
        
        // Get IDs of products already in current list
        $currentListProducts = $listProductModel->where('list_id', $listId)->findAll();
        $currentProductIds = array_column($currentListProducts, 'product_id');
        
        // Get products from other lists in same category
        $suggestedProducts = [];
        if ($list['category_id']) {
            // Get category info including age restrictions
            $category = $categoryModel->find($list['category_id']);
            
            $otherLists = $listModel->select('lists.id')
                ->where('lists.category_id', $list['category_id'])
                ->where('lists.id !=', $listId)
                ->where('lists.status', 'published')
                ->orderBy('lists.views', 'DESC')
                ->limit(10)
                ->findAll();
            
            $otherListIds = array_column($otherLists, 'id');
            
            if (!empty($otherListIds)) {
                $suggestedProducts = $listProductModel->select('products.*')
                    ->join('products', 'products.id = list_products.product_id')
                    ->whereIn('list_products.list_id', $otherListIds)
                    ->groupBy('products.id')
                    ->orderBy('COUNT(list_products.id)', 'DESC')
                    ->limit(12)
                    ->findAll();
                
                // Filter out products already in current list
                $suggestedProducts = array_filter($suggestedProducts, function($product) use ($currentProductIds) {
                    return !in_array($product['id'], $currentProductIds);
                });
                
                // Filter by user age if category has age restrictions
                if ($userAge !== null && $category) {
                    $suggestedProducts = array_filter($suggestedProducts, function($product) use ($userAge, $category) {
                        $meetsMinAge = $category['min_age'] === null || $userAge >= $category['min_age'];
                        $meetsMaxAge = $category['max_age'] === null || $userAge <= $category['max_age'];
                        return $meetsMinAge && $meetsMaxAge;
                    });
                }
            }
        }
        
        $this->data['suggestedProducts'] = $suggestedProducts;
        $this->data['userAge'] = $userAge;

        if (strtolower($this->request->getMethod()) === 'post') {
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
        $limit = (int)($this->request->getGet('limit') ?? 10);
        $offset = (int)($this->request->getGet('offset') ?? 0);
        
        // Validate and sanitize parameters
        $limit = max(1, min($limit, 50)); // Between 1 and 50
        $offset = max(0, $offset);
        
        $results = [];
        $total = 0;

        if ($query) {
            $bolApi = new BolComAPI();
            $response = $bolApi->searchProducts($query, $limit, $offset);
            
            if ($response['success']) {
                $results = $response['products'];
                $total = $response['total'] ?? count($results);
            }
        }

        return $this->response->setJSON([
            'success' => true,
            'products' => $results,
            'total' => $total,
            'limit' => $limit,
            'offset' => $offset,
        ]);
    }

    public function addProduct()
    {
        if (!$this->isLoggedIn()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Please login to continue',
            ]);
        }

        if (strtolower($this->request->getMethod()) === 'post') {
            $listId = $this->request->getPost('list_id');
            $productId = $this->request->getPost('product_id');
            $productData = $this->request->getPost('product');

            // Validate required fields
            if (empty($listId) || (empty($productId) && empty($productData))) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Missing required fields',
                ]);
            }

            // Verify list ownership
            $listModel = new ListModel();
            $list = $listModel->find($listId);

            if (!$list || $list['user_id'] != $this->session->get('user_id')) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'List not found or access denied',
                ]);
            }

            $productModel = new ProductModel();
            
            // If product_id is provided directly, use it (for suggested products)
            if (!empty($productId)) {
                // Verify product exists
                $product = $productModel->find($productId);
                if (!$product) {
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => 'Product not found',
                    ]);
                }
            } else {
                // Handle new product from search
                $product = null;

                if (!empty($productData['external_id'])) {
                    $product = $productModel->findByExternalId($productData['external_id'], $productData['source']);
                }

                // Create product if not exists
                if (!$product) {
                    // Validate product data
                    if (empty($productData['title']) || empty($productData['affiliate_url'])) {
                        return $this->response->setJSON([
                            'success' => false,
                            'message' => 'Product title and affiliate URL are required',
                        ]);
                    }
                    
                    $productModel->insert($productData);
                    $productId = $productModel->getInsertID();
                } else {
                    $productId = $product['id'];
                }
            }

            // Check if product is already in this list (prevent duplicates)
            $listProductModel = new ListProductModel();
            $existingLink = $listProductModel->where('list_id', $listId)
                ->where('product_id', $productId)
                ->first();
            
            if ($existingLink) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Product already exists in this list',
                ]);
            }

            // Get next position
            $maxPosition = $listProductModel->where('list_id', $listId)
                ->selectMax('position')
                ->first();
            $position = ($maxPosition['position'] ?? 0) + 1;

            // Add to list
            $listProductModel->addProductToList($listId, $productId, $position);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Product added successfully',
                'product_id' => $productId,
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Invalid request method',
        ]);
    }

    public function getListProducts($listId)
    {
        if (!$this->isLoggedIn()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Please login to continue',
            ]);
        }

        // Verify list ownership
        $listModel = new ListModel();
        $list = $listModel->find($listId);

        if (!$list || $list['user_id'] != $this->session->get('user_id')) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'List not found or access denied',
            ]);
        }

        // Get products for this list
        $listProductModel = new ListProductModel();
        $products = $listProductModel->getListProducts($listId);

        return $this->response->setJSON([
            'success' => true,
            'products' => $products,
        ]);
    }

    public function removeProduct()
    {
        if (!$this->isLoggedIn()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Please login to continue',
            ]);
        }

        if (strtolower($this->request->getMethod()) === 'post') {
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
        if (!$this->isLoggedIn()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Please login to continue',
            ]);
        }

        if (strtolower($this->request->getMethod()) === 'post') {
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
