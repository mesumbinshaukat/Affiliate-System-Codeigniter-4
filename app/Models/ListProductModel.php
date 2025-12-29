<?php

namespace App\Models;

use CodeIgniter\Model;

class ListProductModel extends Model
{
    protected $table = 'list_products';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = ['list_id', 'product_id', 'position', 'custom_note', 'claimed_at', 'claimed_by_subid'];

    protected bool $allowEmptyInserts = false;

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = '';

    // Validation
    protected $validationRules = [
        'list_id' => 'required|integer',
        'product_id' => 'required|integer',
    ];

    protected $validationMessages = [];
    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    public function getListProducts($listId)
    {
        return $this->select('list_products.id as list_product_id, list_products.*, products.*')
            ->join('products', 'products.id = list_products.product_id')
            ->where('list_products.list_id', $listId)
            ->orderBy('list_products.position', 'ASC')
            ->findAll();
    }

    public function addProductToList($listId, $productId, $position = 0, $customNote = null)
    {
        return $this->insert([
            'list_id' => $listId,
            'product_id' => $productId,
            'position' => $position,
            'custom_note' => $customNote,
            'created_at' => date('Y-m-d H:i:s'),
        ]);
    }

    public function removeProductFromList($listId, $productId)
    {
        return $this->where('list_id', $listId)
            ->where('product_id', $productId)
            ->delete();
    }

    public function updatePositions($listId, $positions)
    {
        foreach ($positions as $productId => $position) {
            $this->where('list_id', $listId)
                ->where('product_id', $productId)
                ->set('position', $position)
                ->update();
        }
        return true;
    }

    public function claimProduct($listProductId, $subId = null)
    {
        return $this->update($listProductId, [
            'claimed_at' => date('Y-m-d H:i:s'),
            'claimed_by_subid' => $subId,
        ]);
    }

    public function unclaimProduct($listProductId)
    {
        return $this->update($listProductId, [
            'claimed_at' => null,
            'claimed_by_subid' => null,
        ]);
    }

    public function findBySubId($subId)
    {
        return $this->where('claimed_by_subid', $subId)->first();
    }
}
