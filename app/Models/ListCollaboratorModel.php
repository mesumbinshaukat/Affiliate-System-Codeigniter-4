<?php

namespace App\Models;

use CodeIgniter\Model;

class ListCollaboratorModel extends Model
{
    protected $table = 'list_collaborators';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'list_id', 'user_id', 'role', 'can_invite'
    ];

    protected bool $allowEmptyInserts = false;

    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $validationRules = [
        'list_id' => 'required|integer',
        'user_id' => 'required|integer',
        'role' => 'required|in_list[owner,editor]',
    ];

    protected $validationMessages = [];
    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    /**
     * Get all collaborators for a list with user details
     */
    public function getListCollaborators(int $listId): array
    {
        return $this->select('list_collaborators.*, users.username, users.email, users.first_name, users.last_name, users.avatar')
            ->join('users', 'users.id = list_collaborators.user_id')
            ->where('list_collaborators.list_id', $listId)
            ->orderBy('list_collaborators.role', 'ASC')
            ->orderBy('list_collaborators.created_at', 'ASC')
            ->findAll();
    }

    /**
     * Check if user is a collaborator on a list
     */
    public function isCollaborator(int $listId, int $userId): bool
    {
        return $this->where('list_id', $listId)
            ->where('user_id', $userId)
            ->countAllResults() > 0;
    }

    /**
     * Check if user is the owner of a list
     */
    public function isOwner(int $listId, int $userId): bool
    {
        $collaborator = $this->where('list_id', $listId)
            ->where('user_id', $userId)
            ->first();
        
        return $collaborator && $collaborator['role'] === 'owner';
    }

    /**
     * Check if user can edit a list (is owner or editor)
     */
    public function canEdit(int $listId, int $userId): bool
    {
        return $this->isCollaborator($listId, $userId);
    }

    /**
     * Check if user can invite others to this list
     */
    public function canInvite(int $listId, int $userId): bool
    {
        $collaborator = $this->where('list_id', $listId)
            ->where('user_id', $userId)
            ->first();
        
        return $collaborator && ($collaborator['role'] === 'owner' || $collaborator['can_invite'] == 1);
    }

    /**
     * Get all lists user is collaborating on
     */
    public function getUserCollaborations(int $userId): array
    {
        return $this->select('list_collaborators.*, lists.title, lists.slug, lists.status, categories.name as category_name')
            ->join('lists', 'lists.id = list_collaborators.list_id')
            ->join('categories', 'categories.id = lists.category_id', 'left')
            ->where('list_collaborators.user_id', $userId)
            ->orderBy('list_collaborators.created_at', 'DESC')
            ->findAll();
    }

    /**
     * Add collaborator to list
     */
    public function addCollaborator(int $listId, int $userId, string $role = 'editor', bool $canInvite = false): bool
    {
        // Check if already exists
        if ($this->isCollaborator($listId, $userId)) {
            return false;
        }

        return $this->insert([
            'list_id' => $listId,
            'user_id' => $userId,
            'role' => $role,
            'can_invite' => $canInvite ? 1 : 0,
        ]) !== false;
    }

    /**
     * Remove collaborator from list
     */
    public function removeCollaborator(int $listId, int $userId): bool
    {
        // Don't allow removing the owner
        if ($this->isOwner($listId, $userId)) {
            return false;
        }

        return $this->where('list_id', $listId)
            ->where('user_id', $userId)
            ->delete();
    }

    /**
     * Transfer ownership
     */
    public function transferOwnership(int $listId, int $currentOwnerId, int $newOwnerId): bool
    {
        $db = \Config\Database::connect();
        $db->transStart();

        // Demote current owner to editor
        $this->where('list_id', $listId)
            ->where('user_id', $currentOwnerId)
            ->set(['role' => 'editor', 'can_invite' => 0])
            ->update();

        // Promote new owner
        $this->where('list_id', $listId)
            ->where('user_id', $newOwnerId)
            ->set(['role' => 'owner', 'can_invite' => 1])
            ->update();

        $db->transComplete();
        return $db->transStatus();
    }

    /**
     * Get collaborator count for a list
     */
    public function getCollaboratorCount(int $listId): int
    {
        return $this->where('list_id', $listId)
            ->countAllResults();
    }
}
