<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table = 'users';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'username', 'email', 'password', 'first_name', 'last_name', 'date_of_birth', 'gender',
        'role', 'status', 'avatar', 'bio'
    ];

    protected bool $allowEmptyInserts = false;

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    // Validation
    protected $validationRules = [
        'username' => 'required|min_length[3]|max_length[100]|is_unique[users.username,id,{id}]',
        'email' => 'required|valid_email|is_unique[users.email,id,{id}]',
        'password' => 'required|min_length[8]',
    ];

    protected $validationMessages = [
        'email' => [
            'is_unique' => 'This email is already registered.',
        ],
        'username' => [
            'is_unique' => 'This username is already taken.',
        ],
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $beforeInsert = ['hashPassword'];
    protected $beforeUpdate = ['hashPassword'];

    protected function hashPassword(array $data)
    {
        if (isset($data['data']['password'])) {
            $data['data']['password'] = password_hash($data['data']['password'], PASSWORD_DEFAULT);
        }
        return $data;
    }

    public function getUserWithStats($userId)
    {
        return $this->select('users.*, 
            COUNT(DISTINCT lists.id) as total_lists,
            COUNT(DISTINCT clicks.id) as total_clicks,
            SUM(lists.views) as total_views')
            ->join('lists', 'lists.user_id = users.id', 'left')
            ->join('clicks', 'clicks.user_id = users.id', 'left')
            ->where('users.id', $userId)
            ->groupBy('users.id')
            ->first();
    }

    public function getActiveUsers($limit = 10)
    {
        return $this->where('status', 'active')
            ->orderBy('created_at', 'DESC')
            ->findAll($limit);
    }

    /**
     * Calculate user age from date_of_birth
     * 
     * @param int $userId User ID
     * @return int|null Age in years, or null if date_of_birth not set or invalid
     */
    public function getAge($userId)
    {
        $user = $this->find($userId);
        
        if (!$user || empty($user['date_of_birth'])) {
            return null;
        }

        try {
            $birthDate = new \DateTime($user['date_of_birth']);
            $today = new \DateTime();
            $age = $today->diff($birthDate)->y;
            
            return $age >= 0 ? $age : null;
        } catch (\Exception $e) {
            return null;
        }
    }
}
