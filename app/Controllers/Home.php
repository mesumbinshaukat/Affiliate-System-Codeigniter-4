<?php

namespace App\Controllers;

use App\Models\ListModel;
use App\Models\CategoryModel;

class Home extends BaseController
{
    public function index()
    {
        // Homepage is now just informational - no public lists
        $userModel = new \App\Models\UserModel();
        $listModel = new ListModel();
        
        // Get stats for display
        $this->data['totalUsers'] = $userModel->countAll();
        $this->data['totalLists'] = $listModel->countAll();
        
        // Get total views from all lists using raw query
        $db = \Config\Database::connect();
        $result = $db->query('SELECT SUM(views) as total_views FROM lists')->getRow();
        $this->data['totalViews'] = $result->total_views ?? 0;
        
        return view('home/index', $this->data);
    }
    
    public function find($username = null)
    {
        if (!$username) {
            $username = $this->request->getGet('username') ?? $this->request->getPost('username');
        }
        
        if (!$username) {
            return redirect()->to('/')->with('error', 'Please enter a username to find a list');
        }
        
        $userModel = new \App\Models\UserModel();
        $listModel = new ListModel();
        
        // Find user by username
        $user = $userModel->where('username', $username)->first();
        
        if (!$user) {
            return redirect()->to('/')->with('error', 'User not found');
        }
        
        // Get user's published lists
        $lists = $listModel->where('user_id', $user['id'])
            ->where('status', 'published')
            ->findAll();
        
        $this->data['user'] = $user;
        $this->data['lists'] = $lists;
        
        return view('home/user_lists', $this->data);
    }

    public function category($slug)
    {
        $categoryModel = new CategoryModel();
        $listModel = new ListModel();

        $category = $categoryModel->where('slug', $slug)->first();

        if (!$category) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $this->data['category'] = $category;
        $this->data['lists'] = $listModel->getPublishedLists(null, $category['id']);
        $this->data['pager'] = $listModel->pager;

        return view('home/category', $this->data);
    }

    public function search()
    {
        $query = $this->request->getGet('q');
        $listModel = new ListModel();

        if ($query) {
            $this->data['lists'] = $listModel->like('title', $query)
                ->orLike('description', $query)
                ->where('status', 'published')
                ->paginate(12);
            $this->data['pager'] = $listModel->pager;
        } else {
            $this->data['lists'] = [];
        }

        $this->data['query'] = $query;

        return view('home/search', $this->data);
    }
}
