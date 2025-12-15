<?php

namespace App\Models;

use CodeIgniter\Model;

class SalesModel extends Model
{
    protected $table = 'sales';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'sub_id', 'order_id', 'product_id', 'quantity', 'commission', 
        'revenue_excl_vat', 'status', 'user_id', 'list_id'
    ];

    protected bool $allowEmptyInserts = false;

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    // Validation
    protected $validationRules = [
        'sub_id' => 'required|string',
        'order_id' => 'required|string',
    ];

    protected $validationMessages = [];
    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    /**
     * Get user's sales and commissions
     * 
     * @param int $userId User ID
     * @param string $status Filter by status (optional)
     * @return array Sales records
     */
    public function getUserSales($userId, $status = null)
    {
        $builder = $this->where('user_id', $userId);
        
        if ($status) {
            $builder->where('status', $status);
        }
        
        return $builder->orderBy('created_at', 'DESC')->findAll();
    }

    /**
     * Get user's total commission
     * 
     * @param int $userId User ID
     * @param string $status Filter by status (optional)
     * @return float Total commission amount
     */
    public function getUserTotalCommission($userId, $status = 'approved')
    {
        $result = $this->selectSum('commission')
            ->where('user_id', $userId);
        
        if ($status) {
            $result->where('status', $status);
        }
        
        $row = $result->first();
        return $row['commission'] ?? 0;
    }

    /**
     * Get user's total sales count
     * 
     * @param int $userId User ID
     * @param string $status Filter by status (optional)
     * @return int Total sales count
     */
    public function getUserSalesCount($userId, $status = null)
    {
        $builder = $this->where('user_id', $userId);
        
        if ($status) {
            $builder->where('status', $status);
        }
        
        return $builder->countAllResults();
    }

    /**
     * Get sales by subId
     * 
     * @param string $subId Tracking subId
     * @return array Sales records
     */
    public function getSalesBySubId($subId)
    {
        return $this->where('sub_id', $subId)->findAll();
    }

    /**
     * Check if order already exists
     * 
     * @param string $orderId Bol.com order ID
     * @return bool
     */
    public function orderExists($orderId)
    {
        return $this->where('order_id', $orderId)->countAllResults() > 0;
    }

    /**
     * Extract user_id and list_id from subId
     * 
     * @param string $subId Tracking subId (format: user_id_list_id)
     * @return array ['user_id' => int, 'list_id' => int] or null
     */
    public function extractIdsFromSubId($subId)
    {
        if (empty($subId)) {
            return null;
        }

        $parts = explode('_', $subId);
        if (count($parts) !== 2) {
            return null;
        }

        $userId = (int) $parts[0];
        $listId = (int) $parts[1];

        if ($userId <= 0 || $listId <= 0) {
            return null;
        }

        return [
            'user_id' => $userId,
            'list_id' => $listId,
        ];
    }

    /**
     * Get all sales with user and list details
     * 
     * @param string $status Filter by status (optional)
     * @return array Sales records with details
     */
    public function getAllSalesWithDetails($status = null)
    {
        $builder = $this->select('sales.*, users.username, users.first_name, users.last_name, lists.title as list_title')
            ->join('users', 'users.id = sales.user_id', 'left')
            ->join('lists', 'lists.id = sales.list_id', 'left');
        
        if ($status) {
            $builder->where('sales.status', $status);
        }
        
        return $builder->orderBy('sales.created_at', 'DESC')->findAll();
    }

    /**
     * Get sales statistics for user
     * 
     * @param int $userId User ID
     * @return array Statistics
     */
    public function getUserStatistics($userId)
    {
        $totalSales = $this->where('user_id', $userId)->countAllResults();
        $approvedSales = $this->where('user_id', $userId)->where('status', 'approved')->countAllResults();
        $pendingSales = $this->where('user_id', $userId)->where('status', 'pending')->countAllResults();
        $rejectedSales = $this->where('user_id', $userId)->where('status', 'rejected')->countAllResults();
        
        $totalCommission = $this->selectSum('commission')
            ->where('user_id', $userId)
            ->where('status', 'approved')
            ->first()['commission'] ?? 0;

        return [
            'total_sales' => $totalSales,
            'approved_sales' => $approvedSales,
            'pending_sales' => $pendingSales,
            'rejected_sales' => $rejectedSales,
            'total_commission' => (float) $totalCommission,
        ];
    }

    /**
     * Get global sales statistics
     * 
     * @return array Statistics
     */
    public function getGlobalStatistics()
    {
        $totalSales = $this->countAllResults();
        $approvedSales = $this->where('status', 'approved')->countAllResults();
        $pendingSales = $this->where('status', 'pending')->countAllResults();
        $rejectedSales = $this->where('status', 'rejected')->countAllResults();
        
        $totalCommission = $this->selectSum('commission')
            ->where('status', 'approved')
            ->first()['commission'] ?? 0;

        return [
            'total_sales' => $totalSales,
            'approved_sales' => $approvedSales,
            'pending_sales' => $pendingSales,
            'rejected_sales' => $rejectedSales,
            'total_commission' => (float) $totalCommission,
        ];
    }

    /**
     * Get sales grouped by user
     * 
     * @return array Sales grouped by user
     */
    public function getSalesByUser()
    {
        return $this->select('sales.user_id, users.username, users.first_name, users.last_name, 
                            COUNT(*) as total_sales, 
                            SUM(CASE WHEN sales.status = "approved" THEN 1 ELSE 0 END) as approved_sales,
                            SUM(CASE WHEN sales.status = "approved" THEN sales.commission ELSE 0 END) as total_commission')
            ->join('users', 'users.id = sales.user_id', 'left')
            ->groupBy('sales.user_id')
            ->orderBy('total_commission', 'DESC')
            ->findAll();
    }
}
