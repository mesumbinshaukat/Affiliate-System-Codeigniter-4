<?php

namespace App\Models;

use CodeIgniter\Model;

class ProductModel extends Model
{
    protected $table = 'products';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'title', 'description', 'image_url', 'price', 'affiliate_url',
        'source', 'ean', 'external_id'
    ];

    protected bool $allowEmptyInserts = false;

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    // Validation
    protected $validationRules = [
        'title' => 'required|min_length[3]|max_length[255]',
        'affiliate_url' => 'permit_empty|valid_url',
    ];

    protected $validationMessages = [];
    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    public function getProductWithClicks($productId)
    {
        return $this->select('products.*, COUNT(clicks.id) as click_count')
            ->join('clicks', 'clicks.product_id = products.id', 'left')
            ->where('products.id', $productId)
            ->groupBy('products.id')
            ->first();
    }

    public function findByEAN($ean)
    {
        return $this->where('ean', $ean)->first();
    }

    public function findByExternalId($externalId, $source)
    {
        return $this->where('external_id', $externalId)
            ->where('source', $source)
            ->first();
    }

    public function getTopProducts($limit = 10)
    {
        return $this->select('products.*, COUNT(clicks.id) as click_count')
            ->join('clicks', 'clicks.product_id = products.id', 'left')
            ->groupBy('products.id')
            ->orderBy('click_count', 'DESC')
            ->findAll($limit);
    }
}
