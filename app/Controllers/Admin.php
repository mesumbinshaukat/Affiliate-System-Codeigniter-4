<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\ListModel;
use App\Models\CategoryModel;
use App\Models\ProductModel;
use App\Models\ClickModel;
use App\Models\AffiliateSourceModel;
use App\Models\SettingModel;

class Admin extends BaseController
{
    public function index()
    {
        $redirect = $this->requireAdmin();
        if ($redirect) return $redirect;

        $userModel = new UserModel();
        $listModel = new ListModel();
        $productModel = new ProductModel();
        $clickModel = new ClickModel();

        $this->data['totalUsers'] = $userModel->countAll();
        $this->data['totalLists'] = $listModel->countAll();
        $this->data['totalProducts'] = $productModel->countAll();
        $this->data['totalClicks'] = $clickModel->countAll();

        $this->data['recentUsers'] = $userModel->orderBy('created_at', 'DESC')->findAll(5);
        $this->data['recentLists'] = $listModel->select('lists.*, users.username')
            ->join('users', 'users.id = lists.user_id')
            ->orderBy('lists.created_at', 'DESC')
            ->findAll(5);

        return view('admin/index', $this->data);
    }

    // User Management
    public function users()
    {
        $redirect = $this->requireAdmin();
        if ($redirect) return $redirect;

        $userModel = new UserModel();
        $this->data['users'] = $userModel->orderBy('created_at', 'DESC')->findAll();

        return view('admin/users', $this->data);
    }

    public function editUser($userId)
    {
        $redirect = $this->requireAdmin();
        if ($redirect) return $redirect;

        $userModel = new UserModel();
        $user = $userModel->find($userId);

        if (!$user) {
            return redirect()->to('/admin/users')->with('error', 'User not found');
        }

        if ($this->request->getMethod() === 'post') {
            $data = [
                'username' => $this->request->getPost('username'),
                'email' => $this->request->getPost('email'),
                'first_name' => $this->request->getPost('first_name'),
                'last_name' => $this->request->getPost('last_name'),
                'role' => $this->request->getPost('role'),
                'status' => $this->request->getPost('status'),
            ];

            // Only update password if provided
            $password = $this->request->getPost('password');
            if (!empty($password)) {
                $data['password'] = $password;
            }

            if ($userModel->update($userId, $data)) {
                return redirect()->back()->with('success', 'User updated successfully');
            }

            $errors = $userModel->errors();
            return redirect()->back()->with('error', implode(', ', $errors))->withInput();
        }

        $this->data['editUser'] = $user;
        return view('admin/edit_user', $this->data);
    }

    public function deleteUser($userId)
    {
        $redirect = $this->requireAdmin();
        if ($redirect) return $redirect;

        // Prevent deleting own account
        if ($userId == $this->session->get('user_id')) {
            return redirect()->to('/admin/users')->with('error', 'Cannot delete your own account');
        }

        $userModel = new UserModel();
        if ($userModel->delete($userId)) {
            return redirect()->to('/admin/users')->with('success', 'User deleted successfully');
        }

        return redirect()->back()->with('error', 'Failed to delete user');
    }

    // List Management
    public function lists()
    {
        $redirect = $this->requireAdmin();
        if ($redirect) return $redirect;

        $listModel = new ListModel();
        $this->data['lists'] = $listModel->select('lists.*, users.username, categories.name as category_name')
            ->join('users', 'users.id = lists.user_id')
            ->join('categories', 'categories.id = lists.category_id', 'left')
            ->orderBy('lists.created_at', 'DESC')
            ->findAll();

        return view('admin/lists', $this->data);
    }

    public function toggleFeatured($listId)
    {
        $redirect = $this->requireAdmin();
        if ($redirect) return $redirect;

        $listModel = new ListModel();
        $list = $listModel->find($listId);

        if ($list) {
            $listModel->update($listId, [
                'is_featured' => $list['is_featured'] ? 0 : 1,
            ]);
        }

        return redirect()->back()->with('success', 'List updated successfully');
    }

    public function deleteListAdmin($listId)
    {
        $redirect = $this->requireAdmin();
        if ($redirect) return $redirect;

        $listModel = new ListModel();
        if ($listModel->delete($listId)) {
            return redirect()->to('/admin/lists')->with('success', 'List deleted successfully');
        }

        return redirect()->back()->with('error', 'Failed to delete list');
    }

    // Category Management
    public function categories()
    {
        $redirect = $this->requireAdmin();
        if ($redirect) return $redirect;

        $categoryModel = new CategoryModel();
        $this->data['categories'] = $categoryModel->getCategoryWithListCount();

        return view('admin/categories', $this->data);
    }

    public function createCategory()
    {
        $redirect = $this->requireAdmin();
        if ($redirect) return $redirect;

        if ($this->request->getMethod() === 'post') {
            $categoryModel = new CategoryModel();

            $data = [
                'name' => $this->request->getPost('name'),
                'slug' => url_title($this->request->getPost('name'), '-', true),
                'description' => $this->request->getPost('description'),
                'icon' => $this->request->getPost('icon'),
                'status' => $this->request->getPost('status'),
            ];

            if ($categoryModel->insert($data)) {
                return redirect()->to('/admin/categories')->with('success', 'Category created successfully');
            }

            $errors = $categoryModel->errors();
            return redirect()->back()->with('error', implode(', ', $errors))->withInput();
        }

        return view('admin/create_category', $this->data);
    }

    public function editCategory($categoryId)
    {
        $redirect = $this->requireAdmin();
        if ($redirect) return $redirect;

        $categoryModel = new CategoryModel();
        $category = $categoryModel->find($categoryId);

        if (!$category) {
            return redirect()->to('/admin/categories')->with('error', 'Category not found');
        }

        if ($this->request->getMethod() === 'post') {
            $data = [
                'name' => $this->request->getPost('name'),
                'slug' => url_title($this->request->getPost('name'), '-', true),
                'description' => $this->request->getPost('description'),
                'icon' => $this->request->getPost('icon'),
                'status' => $this->request->getPost('status'),
            ];

            if ($categoryModel->update($categoryId, $data)) {
                return redirect()->back()->with('success', 'Category updated successfully');
            }

            $errors = $categoryModel->errors();
            return redirect()->back()->with('error', implode(', ', $errors))->withInput();
        }

        $this->data['category'] = $category;
        return view('admin/edit_category', $this->data);
    }

    public function deleteCategory($categoryId)
    {
        $redirect = $this->requireAdmin();
        if ($redirect) return $redirect;

        $categoryModel = new CategoryModel();
        if ($categoryModel->delete($categoryId)) {
            return redirect()->to('/admin/categories')->with('success', 'Category deleted successfully');
        }

        return redirect()->back()->with('error', 'Failed to delete category');
    }

    // Analytics
    public function analytics()
    {
        $redirect = $this->requireAdmin();
        if ($redirect) return $redirect;

        $clickModel = new ClickModel();
        $productModel = new ProductModel();
        $listModel = new ListModel();

        $this->data['clickStats'] = $clickModel->getClickStats();
        $this->data['topProducts'] = $productModel->getTopProducts(10);
        $this->data['topLists'] = $listModel->select('lists.*, users.username, COUNT(clicks.id) as click_count')
            ->join('users', 'users.id = lists.user_id')
            ->join('list_products', 'list_products.list_id = lists.id', 'left')
            ->join('clicks', 'clicks.product_id = list_products.product_id', 'left')
            ->groupBy('lists.id')
            ->orderBy('click_count', 'DESC')
            ->findAll(10);

        return view('admin/analytics', $this->data);
    }

    // Affiliate Sources
    public function affiliateSources()
    {
        $redirect = $this->requireAdmin();
        if ($redirect) return $redirect;

        $affiliateSourceModel = new AffiliateSourceModel();
        $this->data['sources'] = $affiliateSourceModel->findAll();

        return view('admin/affiliate_sources', $this->data);
    }

    public function toggleSource($sourceId)
    {
        $redirect = $this->requireAdmin();
        if ($redirect) return $redirect;

        $affiliateSourceModel = new AffiliateSourceModel();
        $source = $affiliateSourceModel->find($sourceId);

        if ($source) {
            $affiliateSourceModel->update($sourceId, [
                'status' => $source['status'] === 'active' ? 'inactive' : 'active',
            ]);
        }

        return redirect()->back()->with('success', 'Source updated successfully');
    }

    // Settings
    public function settings()
    {
        $redirect = $this->requireAdmin();
        if ($redirect) return $redirect;

        $settingModel = new SettingModel();

        if ($this->request->getMethod() === 'post') {
            $settings = $this->request->getPost('settings');

            foreach ($settings as $key => $value) {
                $settingModel->setSetting($key, $value);
            }

            return redirect()->back()->with('success', 'Settings updated successfully');
        }

        $this->data['settings'] = $settingModel->getAllSettings();

        return view('admin/settings', $this->data);
    }
}
