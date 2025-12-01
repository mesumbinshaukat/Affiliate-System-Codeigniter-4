<?php

namespace App\Controllers;

use App\Models\ListModel;
use App\Models\CategoryModel;

class Home extends BaseController
{
    public function index()
    {
        $listModel = new ListModel();
        $categoryModel = new CategoryModel();

        $this->data['featuredLists'] = $listModel->getFeaturedLists(6);
        $this->data['trendingLists'] = $listModel->getTrendingLists(6);
        $this->data['recentLists'] = $listModel->getPublishedLists(12);
        $this->data['categories'] = $categoryModel->getActiveCategories();

        return view('home/index', $this->data);
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
