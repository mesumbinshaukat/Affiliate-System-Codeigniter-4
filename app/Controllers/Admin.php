<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\ListModel;
use App\Models\CategoryModel;
use App\Models\ProductModel;
use App\Models\ClickModel;
use App\Models\AffiliateSourceModel;
use App\Models\SettingModel;
use App\Models\DrawingModel;
use App\Models\DrawingParticipantModel;
use App\Models\SalesModel;

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
            return redirect()->to('index.php/admin/users')->with('error', 'Gebruiker niet gevonden');
        }

        if (strtolower($this->request->getMethod()) === 'post') {
            // Get form data
            $username = $this->request->getPost('username');
            $email = $this->request->getPost('email');
            $firstName = $this->request->getPost('first_name');
            $lastName = $this->request->getPost('last_name');
            $role = $this->request->getPost('role');
            $status = $this->request->getPost('status');
            $password = $this->request->getPost('password');

            // Build data array with only changed fields
            $data = [];
            
            if ($username !== $user['username']) {
                $data['username'] = $username;
            }
            if ($email !== $user['email']) {
                $data['email'] = $email;
            }
            if ($firstName !== $user['first_name']) {
                $data['first_name'] = $firstName;
            }
            if ($lastName !== $user['last_name']) {
                $data['last_name'] = $lastName;
            }
            if ($role !== $user['role']) {
                $data['role'] = $role;
            }
            if ($status !== $user['status']) {
                $data['status'] = $status;
            }
            
            // Only add password if provided
            if (!empty($password)) {
                $data['password'] = $password;
            }

            // If no changes, return success message
            if (empty($data)) {
                return redirect()->back()->with('success', 'Geen wijzigingen aangebracht');
            }

            // Set validation rules only for fields being updated
            $validationRules = [];
            if (isset($data['username'])) {
                $validationRules['username'] = 'required|min_length[3]|max_length[100]|is_unique[users.username,id,' . $userId . ']';
            }
            if (isset($data['email'])) {
                $validationRules['email'] = 'required|valid_email|is_unique[users.email,id,' . $userId . ']';
            }
            if (isset($data['password'])) {
                $validationRules['password'] = 'required|min_length[8]';
            }
            if (isset($data['first_name'])) {
                $validationRules['first_name'] = 'required|min_length[2]|max_length[100]';
            }
            if (isset($data['last_name'])) {
                $validationRules['last_name'] = 'required|min_length[2]|max_length[100]';
            }

            // Validate only changed fields
            if (!empty($validationRules)) {
                if (!$this->validate($validationRules)) {
                    return redirect()->back()->with('errors', $this->validator->getErrors())->withInput();
                }
            }

            // Update user
            if ($userModel->update($userId, $data)) {
                return redirect()->back()->with('success', 'Gebruiker succesvol bijgewerkt');
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
            return redirect()->to('index.php/admin/users')->with('error', 'Je kunt je eigen account niet verwijderen');
        }

        $userModel = new UserModel();
        if ($userModel->delete($userId)) {
            return redirect()->to('index.php/admin/users')->with('success', 'Gebruiker succesvol verwijderd');
        }

        return redirect()->back()->with('error', 'Gebruiker verwijderen mislukt');
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

        return redirect()->back()->with('success', 'Lijst succesvol bijgewerkt');
    }

    public function deleteListAdmin($listId)
    {
        $redirect = $this->requireAdmin();
        if ($redirect) return $redirect;

        $listModel = new ListModel();
        if ($listModel->delete($listId)) {
            return redirect()->to('/admin/lists')->with('success', 'Lijst succesvol verwijderd');
        }

        return redirect()->back()->with('error', 'Lijst verwijderen mislukt');
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

        if (strtolower($this->request->getMethod()) === 'post') {
            $categoryModel = new CategoryModel();

            $data = [
                'name' => $this->request->getPost('name'),
                'slug' => url_title($this->request->getPost('name'), '-', true),
                'description' => $this->request->getPost('description'),
                'icon' => $this->request->getPost('icon'),
                'status' => $this->request->getPost('status'),
            ];

            if ($categoryModel->insert($data)) {
                return redirect()->to('/admin/categories')->with('success', 'Categorie succesvol aangemaakt');
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
            return redirect()->to('/admin/categories')->with('error', 'Categorie niet gevonden');
        }

        if (strtolower($this->request->getMethod()) === 'post') {
            $data = [
                'name' => $this->request->getPost('name'),
                'slug' => url_title($this->request->getPost('name'), '-', true),
                'description' => $this->request->getPost('description'),
                'icon' => $this->request->getPost('icon'),
                'status' => $this->request->getPost('status'),
            ];

            if ($categoryModel->update($categoryId, $data)) {
                return redirect()->back()->with('success', 'Categorie succesvol bijgewerkt');
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
            return redirect()->to('/admin/categories')->with('success', 'Categorie succesvol verwijderd');
        }

        return redirect()->back()->with('error', 'Categorie verwijderen mislukt');
    }

    // Analytics
    public function analytics()
    {
        $redirect = $this->requireAdmin();
        if ($redirect) return $redirect;

        $clickModel = new ClickModel();
        $productModel = new ProductModel();
        $listModel = new ListModel();
        $salesModel = new SalesModel();

        $this->data['clickStats'] = $clickModel->getClickStats();
        $this->data['topProducts'] = $productModel->getTopProducts(10);
        $this->data['topLists'] = $listModel->select('lists.*, users.username, COUNT(clicks.id) as click_count')
            ->join('users', 'users.id = lists.user_id')
            ->join('list_products', 'list_products.list_id = lists.id', 'left')
            ->join('clicks', 'clicks.product_id = list_products.product_id', 'left')
            ->groupBy('lists.id')
            ->orderBy('click_count', 'DESC')
            ->findAll(10);

        // Add sales analytics
        $this->data['salesStats'] = $salesModel->getGlobalStatistics();
        $this->data['salesByUser'] = $salesModel->getSalesByUser();
        $this->data['allSales'] = $salesModel->getAllSalesWithDetails();

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

        return redirect()->back()->with('success', 'Bron succesvol bijgewerkt');
    }

    // Settings
    public function settings()
    {
        $redirect = $this->requireAdmin();
        if ($redirect) return $redirect;

        $settingModel = new SettingModel();

        if (strtolower($this->request->getMethod()) === 'post') {
            $settings = $this->request->getPost('settings');

            foreach ($settings as $key => $value) {
                $settingModel->setSetting($key, $value);
            }

            return redirect()->back()->with('success', 'Instellingen succesvol bijgewerkt');
        }

        $this->data['settings'] = $settingModel->getAllSettings();

        return view('admin/settings', $this->data);
    }

    // Drawing Management
    public function drawings()
    {
        $redirect = $this->requireAdmin();
        if ($redirect) return $redirect;

        $drawingModel = new DrawingModel();
        $this->data['drawings'] = $drawingModel->getAllDrawingsWithStats();

        return view('admin/drawings', $this->data);
    }

    public function drawingDetails($drawingId)
    {
        $redirect = $this->requireAdmin();
        if ($redirect) return $redirect;

        $drawingModel = new DrawingModel();
        $drawing = $drawingModel->getDrawingStats($drawingId);

        if (!$drawing) {
            return redirect()->to('admin/drawings')->with('error', 'Trekking niet gevonden');
        }

        $participants = $drawingModel->getDrawingParticipants($drawingId);

        $this->data['drawing'] = $drawing;
        $this->data['participants'] = $participants;

        return view('admin/drawing_details', $this->data);
    }

    public function deleteDrawing($drawingId)
    {
        $redirect = $this->requireAdmin();
        if ($redirect) return $redirect;

        $drawingModel = new DrawingModel();
        $participantModel = new DrawingParticipantModel();

        $drawing = $drawingModel->find($drawingId);

        if (!$drawing) {
            return redirect()->to('admin/drawings')->with('error', 'Drawing not found');
        }

        // Delete all participants first
        $participantModel->where('drawing_id', $drawingId)->delete();

        // Delete the drawing
        if ($drawingModel->delete($drawingId)) {
            return redirect()->to('admin/drawings')->with('success', 'Trekking succesvol verwijderd');
        }

        return redirect()->back()->with('error', 'Trekking verwijderen mislukt');
    }
}
