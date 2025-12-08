<?php

namespace App\Controllers;

use App\Models\ProductModel;
use App\Models\CategoryModel;
use App\Models\ListProductModel;

class Products extends BaseController
{
    public function index()
    {
        $productModel = new ProductModel();
        $categoryModel = new CategoryModel();
        
        $categoryId = $this->request->getGet('category');
        $perPage = 12;
        
        $builder = $productModel->select('products.*, COUNT(DISTINCT list_products.list_id) as list_count')
            ->join('list_products', 'list_products.product_id = products.id', 'left')
            ->groupBy('products.id')
            ->orderBy('products.created_at', 'DESC');
        
        if ($categoryId) {
            // Filter by category through lists
            $builder->join('lists', 'lists.id = list_products.list_id', 'left')
                ->where('lists.category_id', $categoryId);
        }
        
        try {
            $this->data['products'] = $builder->paginate($perPage);
            $this->data['pager'] = $productModel->pager;
        } catch (\Exception $e) {
            // Fallback if pagination fails
            $this->data['products'] = $builder->findAll($perPage);
            $this->data['pager'] = null;
        }
        
        $this->data['categories'] = $categoryModel->getActiveCategories();
        $this->data['selectedCategory'] = $categoryId;
        
        return view('products/index', $this->data);
    }
    
    public function byCategory($categorySlug = null)
    {
        $categoryModel = new CategoryModel();
        $productModel = new ProductModel();
        
        if ($categorySlug) {
            $category = $categoryModel->where('slug', $categorySlug)->first();
            
            if (!$category) {
                throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
            }
            
            $this->data['category'] = $category;
            
            // Get products in this category through lists
            $builder = $productModel
                ->select('products.*, COUNT(DISTINCT list_products.list_id) as list_count')
                ->join('list_products', 'list_products.product_id = products.id', 'left')
                ->join('lists', 'lists.id = list_products.list_id', 'left')
                ->where('lists.category_id', $category['id'])
                ->where('lists.status', 'published')
                ->groupBy('products.id')
                ->orderBy('list_count', 'DESC');
            
            try {
                $this->data['products'] = $builder->paginate(12);
                $this->data['pager'] = $productModel->pager;
            } catch (\Exception $e) {
                // Fallback if pagination fails
                $this->data['products'] = $builder->findAll(12);
                $this->data['pager'] = null;
            }
        } else {
            // Show all categories
            $this->data['categories'] = $categoryModel->getActiveCategories();
            return view('products/categories', $this->data);
        }
        
        return view('products/by_category', $this->data);
    }
    
    public function view($productId)
    {
        $productModel = new ProductModel();
        $listProductModel = new ListProductModel();
        
        $product = $productModel->getProductWithClicks($productId);
        
        if (!$product) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }
        
        // Get lists containing this product
        $this->data['product'] = $product;
        $this->data['lists'] = $listProductModel
            ->select('lists.*, users.username, users.first_name, users.last_name')
            ->join('lists', 'lists.id = list_products.list_id')
            ->join('users', 'users.id = lists.user_id')
            ->where('list_products.product_id', $productId)
            ->where('lists.status', 'published')
            ->findAll();
        
        return view('products/view', $this->data);
    }
    
    public function search()
    {
        $query = $this->request->getGet('q');
        $productModel = new ProductModel();
        
        if ($query) {
            $builder = $productModel
                ->select('products.*, COUNT(DISTINCT list_products.list_id) as list_count')
                ->join('list_products', 'list_products.product_id = products.id', 'left')
                ->groupBy('products.id')
                ->like('products.title', $query)
                ->orLike('products.description', $query)
                ->orderBy('list_count', 'DESC');
            
            try {
                $this->data['products'] = $builder->paginate(12);
                $this->data['pager'] = $productModel->pager;
            } catch (\Exception $e) {
                // Fallback if pagination fails
                $this->data['products'] = $builder->findAll(12);
                $this->data['pager'] = null;
            }
        } else {
            $this->data['products'] = [];
            $this->data['pager'] = null;
        }
        
        $this->data['query'] = $query;
        
        return view('products/search', $this->data);
    }
}
