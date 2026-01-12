<?php

namespace App\Models;

use CodeIgniter\Model;

class ContributionModel extends Model
{
    protected $table = 'contributions';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'list_product_id',
        'contributor_name',
        'contributor_email',
        'amount',
        'message',
        'is_anonymous',
        'status',
    ];

    protected bool $allowEmptyInserts = false;

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    // Validation
    protected $validationRules = [
        'list_product_id' => 'required|integer',
        'contributor_name' => 'required|max_length[255]',
        'contributor_email' => 'permit_empty|valid_email|max_length[255]',
        'amount' => 'required|decimal|greater_than[0]',
        'is_anonymous' => 'permit_empty|in_list[0,1]',
        'status' => 'permit_empty|in_list[pending,completed,refunded]',
    ];

    protected $validationMessages = [
        'contributor_name' => [
            'required' => 'Naam is verplicht',
            'max_length' => 'Naam mag maximaal 255 tekens bevatten',
        ],
        'contributor_email' => [
            'valid_email' => 'Voer een geldig e-mailadres in',
        ],
        'amount' => [
            'required' => 'Bedrag is verplicht',
            'decimal' => 'Bedrag moet een geldig getal zijn',
            'greater_than' => 'Bedrag moet groter zijn dan 0',
        ],
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    /**
     * Get all contributions for a specific list product
     */
    public function getProductContributions($listProductId, $includeAnonymous = true)
    {
        $query = $this->where('list_product_id', $listProductId)
            ->where('status', 'completed')
            ->orderBy('created_at', 'DESC');

        return $query->findAll();
    }

    /**
     * Get total contributed amount for a product
     */
    public function getTotalContributed($listProductId)
    {
        $result = $this->selectSum('amount')
            ->where('list_product_id', $listProductId)
            ->where('status', 'completed')
            ->first();

        return (float) ($result['amount'] ?? 0);
    }

    /**
     * Get contribution statistics for a product
     */
    public function getProductStats($listProductId)
    {
        $contributions = $this->where('list_product_id', $listProductId)
            ->where('status', 'completed')
            ->findAll();

        $total = 0;
        $count = count($contributions);

        foreach ($contributions as $contribution) {
            $total += (float) $contribution['amount'];
        }

        return [
            'total_amount' => $total,
            'contributor_count' => $count,
            'contributions' => $contributions,
        ];
    }

    /**
     * Get contributions for a list owner's dashboard
     */
    public function getListContributions($listId)
    {
        $db = \Config\Database::connect();
        $builder = $db->table('contributions c');
        
        $builder->select('c.*, lp.list_id, p.title as product_title, p.price as product_price, lp.target_amount')
            ->join('list_products lp', 'lp.id = c.list_product_id')
            ->join('products p', 'p.id = lp.product_id')
            ->where('lp.list_id', $listId)
            ->where('c.status', 'completed')
            ->orderBy('c.created_at', 'DESC');

        return $builder->get()->getResultArray();
    }

    /**
     * Add a new contribution
     */
    public function addContribution($data)
    {
        return $this->insert($data);
    }

    /**
     * Check if funding goal is reached
     */
    public function isFundingComplete($listProductId, $targetAmount)
    {
        $contributed = $this->getTotalContributed($listProductId);
        return $contributed >= $targetAmount;
    }

    /**
     * Get contribution percentage
     */
    public function getContributionPercentage($listProductId, $targetAmount)
    {
        if ($targetAmount <= 0) {
            return 0;
        }

        $contributed = $this->getTotalContributed($listProductId);
        $percentage = ($contributed / $targetAmount) * 100;

        return min($percentage, 100); // Cap at 100%
    }
}
