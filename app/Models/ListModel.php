<?php

namespace App\Models;

use CodeIgniter\Model;

class ListModel extends Model
{
    protected $table = 'lists';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'user_id', 'category_id', 'title', 'slug', 'description',
        'status', 'is_featured', 'views'
    ];

    protected bool $allowEmptyInserts = false;

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    // Validation
    protected $validationRules = [
        'user_id' => 'required|integer',
        'title' => 'required|min_length[3]|max_length[255]',
        'slug' => 'required|alpha_dash|is_unique[lists.slug,id,{id}]',
    ];

    protected $validationMessages = [];
    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    public function getPublishedLists($limit = null, $categoryId = null)
    {
        $builder = $this->select('lists.*, users.username, users.first_name, users.last_name, categories.name as category_name')
            ->join('users', 'users.id = lists.user_id')
            ->join('categories', 'categories.id = lists.category_id', 'left')
            ->where('lists.status', 'published')
            ->orderBy('lists.created_at', 'DESC');

        if ($categoryId) {
            $builder->where('lists.category_id', $categoryId);
        }

        if ($limit) {
            return $builder->findAll($limit);
        }

        return $builder->paginate(12);
    }

    public function getListWithDetails($slug)
    {
        return $this->select('lists.*, users.username, users.first_name, users.last_name, users.avatar, categories.name as category_name, categories.slug as category_slug')
            ->join('users', 'users.id = lists.user_id')
            ->join('categories', 'categories.id = lists.category_id', 'left')
            ->where('lists.slug', $slug)
            ->first();
    }

    public function getUserLists($userId, $includePrivate = false)
    {
        $builder = $this->select('lists.*, categories.name as category_name, COUNT(list_products.id) as product_count')
            ->join('categories', 'categories.id = lists.category_id', 'left')
            ->join('list_products', 'list_products.list_id = lists.id', 'left')
            ->where('lists.user_id', $userId)
            ->groupBy('lists.id')
            ->orderBy('lists.created_at', 'DESC');

        if (!$includePrivate) {
            $builder->whereIn('lists.status', ['published', 'draft']);
        }

        return $builder->findAll();
    }

    public function incrementViews($listId)
    {
        return $this->set('views', 'views + 1', false)
            ->where('id', $listId)
            ->update();
    }

    public function getFeaturedLists($limit = 6)
    {
        return $this->select('lists.*, users.username, users.first_name, users.last_name, categories.name as category_name')
            ->join('users', 'users.id = lists.user_id')
            ->join('categories', 'categories.id = lists.category_id', 'left')
            ->where('lists.status', 'published')
            ->where('lists.is_featured', 1)
            ->orderBy('lists.created_at', 'DESC')
            ->findAll($limit);
    }

    public function getTrendingLists($limit = 6)
    {
        return $this->select('lists.*, users.username, users.first_name, users.last_name, categories.name as category_name')
            ->join('users', 'users.id = lists.user_id')
            ->join('categories', 'categories.id = lists.category_id', 'left')
            ->where('lists.status', 'published')
            ->orderBy('lists.views', 'DESC')
            ->findAll($limit);
    }
}
