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
        'role', 'status', 'avatar', 'bio', 'provider', 'provider_id', 'provider_token', 'email_verified'
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

    /**
     * Find user by social provider credentials
     * 
     * @param string $provider Provider name (facebook, google)
     * @param string $providerId Provider's unique user ID
     * @return array|null User data or null if not found
     */
    public function findBySocialProvider(string $provider, string $providerId): ?array
    {
        return $this->where('provider', $provider)
                    ->where('provider_id', $providerId)
                    ->first();
    }

    /**
     * Create or update user from social provider data
     * 
     * @param array $providerData Data from OAuth provider
     * @param string $provider Provider name (facebook, google)
     * @return array|false User data or false on failure
     */
    public function createOrUpdateFromSocial(array $providerData, string $provider)
    {
        try {
            // Validate provider data
            if (empty($providerData['identifier'])) {
                log_message('error', 'createOrUpdateFromSocial: Missing provider identifier');
                return false;
            }
            
            log_message('info', "Checking if user exists - Provider: {$provider}, ID: {$providerData['identifier']}");
            
            // Check if user exists with this provider
            $existingUser = $this->findBySocialProvider($provider, $providerData['identifier']);
            
            if ($existingUser) {
                log_message('info', "Existing user found by provider. User ID: {$existingUser['id']}, Email: " . ($existingUser['email'] ?? 'NULL'));
                
                // Update existing user's token and info
                $updateData = [
                    'provider_token' => $providerData['access_token'] ?? null,
                    'email_verified' => !empty($providerData['emailVerified']) ? 1 : 0,
                ];
                
                // Update name if provided and current name is empty
                if (!empty($providerData['firstName']) && empty($existingUser['first_name'])) {
                    $updateData['first_name'] = $providerData['firstName'];
                }
                if (!empty($providerData['lastName']) && empty($existingUser['last_name'])) {
                    $updateData['last_name'] = $providerData['lastName'];
                }
                
                // Update avatar if provided and not already set
                if (!empty($providerData['photoURL']) && empty($existingUser['avatar'])) {
                    $updateData['avatar'] = $providerData['photoURL'];
                }
                
                // Update email if missing and provided
                if (!empty($providerData['email']) && empty($existingUser['email'])) {
                    $updateData['email'] = $providerData['email'];
                }
                
                $this->update($existingUser['id'], $updateData);
                log_message('info', "User updated successfully. User ID: {$existingUser['id']}");
                return $this->find($existingUser['id']);
            }
            
            // Check if email already exists (user registered normally)
            if (!empty($providerData['email'])) {
                log_message('info', "Checking if email exists: {$providerData['email']}");
                $emailUser = $this->where('email', $providerData['email'])->first();
                
                if ($emailUser) {
                    log_message('info', "User found by email. Linking social account. User ID: {$emailUser['id']}, Provider: {$provider}");
                    
                    // Link social account to existing email account
                    $updateData = [
                        'provider' => $provider,
                        'provider_id' => $providerData['identifier'],
                        'provider_token' => $providerData['access_token'] ?? null,
                        'email_verified' => 1,
                    ];
                    
                    // Update avatar if missing
                    if (!empty($providerData['photoURL']) && empty($emailUser['avatar'])) {
                        $updateData['avatar'] = $providerData['photoURL'];
                    }
                    
                    // Update names if missing
                    if (!empty($providerData['firstName']) && empty($emailUser['first_name'])) {
                        $updateData['first_name'] = $providerData['firstName'];
                    }
                    if (!empty($providerData['lastName']) && empty($emailUser['last_name'])) {
                        $updateData['last_name'] = $providerData['lastName'];
                    }
                    
                    $this->update($emailUser['id'], $updateData);
                    log_message('info', "Social account linked successfully. User ID: {$emailUser['id']}");
                    return $this->find($emailUser['id']);
                }
            }
            
            // Create new user
            log_message('info', "Creating new user from social auth. Provider: {$provider}");
            $username = $this->generateUniqueUsername($providerData);
            
            $userData = [
                'username' => $username,
                'email' => $providerData['email'] ?? null,
                'password' => password_hash(bin2hex(random_bytes(16)), PASSWORD_DEFAULT), // Random password
                'first_name' => $providerData['firstName'] ?? '',
                'last_name' => $providerData['lastName'] ?? '',
                'avatar' => $providerData['photoURL'] ?? null,
                'provider' => $provider,
                'provider_id' => $providerData['identifier'],
                'provider_token' => $providerData['access_token'] ?? null,
                'email_verified' => !empty($providerData['emailVerified']) ? 1 : 0,
                'role' => 'user',
                'status' => 'active',
            ];
            
            log_message('info', "Inserting new user. Username: {$username}, Email: " . ($userData['email'] ?? 'NULL'));
            
            // Disable validation temporarily for social auth users
            $this->skipValidation = true;
            
            if ($this->insert($userData)) {
                $userId = $this->getInsertID();
                log_message('info', "New user created successfully. User ID: {$userId}");
                
                // Re-enable validation
                $this->skipValidation = false;
                
                return $this->find($userId);
            } else {
                log_message('error', 'Failed to insert user. Validation errors: ' . json_encode($this->errors()));
                
                // Re-enable validation
                $this->skipValidation = false;
                
                return false;
            }
            
        } catch (\Exception $e) {
            log_message('error', 'Exception in createOrUpdateFromSocial: ' . $e->getMessage());
            log_message('error', 'Stack trace: ' . $e->getTraceAsString());
            return false;
        }
    }

    /**
     * Generate unique username from social provider data
     * 
     * @param array $providerData Provider user data
     * @return string Unique username
     */
    private function generateUniqueUsername(array $providerData): string
    {
        // Try email-based username first
        if (!empty($providerData['email'])) {
            $baseUsername = explode('@', $providerData['email'])[0];
        } 
        // Try display name
        elseif (!empty($providerData['displayName'])) {
            $baseUsername = strtolower(str_replace(' ', '', $providerData['displayName']));
        }
        // Try first name
        elseif (!empty($providerData['firstName'])) {
            $baseUsername = strtolower($providerData['firstName']);
        }
        // Fallback to random
        else {
            $baseUsername = 'user' . time();
        }
        
        // Clean username
        $baseUsername = preg_replace('/[^a-z0-9_]/', '', strtolower($baseUsername));
        $username = $baseUsername;
        $counter = 1;
        
        // Ensure uniqueness
        while ($this->where('username', $username)->first()) {
            $username = $baseUsername . $counter;
            $counter++;
        }
        
        return $username;
    }
}
