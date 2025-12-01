<?php

namespace App\Models;

use CodeIgniter\Model;

class ClickModel extends Model
{
    protected $table = 'clicks';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'product_id', 'list_id', 'user_id', 'ip_address', 'user_agent', 'referer'
    ];

    protected bool $allowEmptyInserts = false;

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = '';

    // Validation
    protected $validationRules = [
        'product_id' => 'required|integer',
    ];

    protected $validationMessages = [];
    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    public function logClick($productId, $listId = null, $userId = null)
    {
        $request = \Config\Services::request();
        
        // Anonymize IP (GDPR compliant)
        $ip = $request->getIPAddress();
        $anonymizedIp = $this->anonymizeIP($ip);

        return $this->insert([
            'product_id' => $productId,
            'list_id' => $listId,
            'user_id' => $userId,
            'ip_address' => $anonymizedIp,
            'user_agent' => $request->getUserAgent()->getAgentString(),
            'referer' => $request->getServer('HTTP_REFERER'),
            'created_at' => date('Y-m-d H:i:s'),
        ]);
    }

    private function anonymizeIP($ip)
    {
        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
            // IPv4: Remove last octet
            return preg_replace('/\.\d+$/', '.0', $ip);
        } elseif (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
            // IPv6: Remove last 80 bits
            return preg_replace('/([\da-f]{1,4}:){4}.*/', '$1::', $ip);
        }
        return $ip;
    }

    public function getClickStats($startDate = null, $endDate = null)
    {
        $builder = $this->select('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('DATE(created_at)')
            ->orderBy('date', 'DESC');

        if ($startDate) {
            $builder->where('created_at >=', $startDate);
        }
        if ($endDate) {
            $builder->where('created_at <=', $endDate);
        }

        return $builder->findAll();
    }

    public function getListClicks($listId)
    {
        return $this->where('list_id', $listId)->countAllResults();
    }

    public function getUserClicks($userId)
    {
        return $this->select('clicks.*, products.title as product_title, lists.title as list_title')
            ->join('products', 'products.id = clicks.product_id')
            ->join('lists', 'lists.id = clicks.list_id', 'left')
            ->where('clicks.user_id', $userId)
            ->orderBy('clicks.created_at', 'DESC')
            ->findAll();
    }
}
