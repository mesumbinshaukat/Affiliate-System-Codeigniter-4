<?php

namespace App\Models;

use CodeIgniter\Model;

class CategoryModel extends Model
{
    protected $table = 'categories';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = ['name', 'slug', 'description', 'icon', 'status', 'min_age', 'max_age'];

    protected bool $allowEmptyInserts = false;

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    // Validation
    protected $validationRules = [
        'name' => 'required|min_length[3]|max_length[100]',
        'slug' => 'required|alpha_dash|is_unique[categories.slug,id,{id}]',
    ];

    protected $validationMessages = [];
    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    public function getActiveCategories()
    {
        return $this->where('status', 'active')
            ->orderBy('name', 'ASC')
            ->findAll();
    }

    public function getCategoryWithListCount($categoryId = null)
    {
        $builder = $this->select('categories.*, COUNT(lists.id) as list_count')
            ->join('lists', 'lists.category_id = categories.id', 'left')
            ->groupBy('categories.id');

        if ($categoryId) {
            return $builder->where('categories.id', $categoryId)->first();
        }

        return $builder->findAll();
    }
}
