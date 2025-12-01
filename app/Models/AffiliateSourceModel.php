<?php

namespace App\Models;

use CodeIgniter\Model;

class AffiliateSourceModel extends Model
{
    protected $table = 'affiliate_sources';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = ['name', 'slug', 'api_endpoint', 'status', 'settings'];

    protected bool $allowEmptyInserts = false;

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    // Validation
    protected $validationRules = [
        'name' => 'required|min_length[3]|max_length[100]',
        'slug' => 'required|alpha_dash|is_unique[affiliate_sources.slug,id,{id}]',
    ];

    protected $validationMessages = [];
    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    public function getActiveSources()
    {
        return $this->where('status', 'active')->findAll();
    }

    public function getSourceBySlug($slug)
    {
        return $this->where('slug', $slug)->first();
    }
}
