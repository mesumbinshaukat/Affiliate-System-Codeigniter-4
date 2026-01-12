<?php

namespace App\Models;

use CodeIgniter\Model;

class ListInvitationModel extends Model
{
    protected $table = 'list_invitations';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'list_id', 'inviter_id', 'invitee_email', 'invitee_id', 
        'token', 'status', 'message', 'expires_at', 'responded_at'
    ];

    protected bool $allowEmptyInserts = false;

    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $validationRules = [
        'list_id' => 'required|integer',
        'inviter_id' => 'required|integer',
        'invitee_email' => 'required|valid_email',
    ];

    protected $validationMessages = [];
    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    /**
     * Create a new invitation
     */
    public function createInvitation(int $listId, int $inviterId, string $inviteeEmail, ?string $message = null): array|false
    {
        // Check if invitation already exists for this email and list
        $existing = $this->where('list_id', $listId)
            ->where('invitee_email', $inviteeEmail)
            ->whereIn('status', ['pending'])
            ->first();

        if ($existing) {
            return false; // Already invited
        }

        // Check if user with this email exists
        $userModel = new UserModel();
        $invitee = $userModel->where('email', $inviteeEmail)->first();
        $inviteeId = $invitee ? $invitee['id'] : null;

        // Check if user is already a collaborator
        if ($inviteeId) {
            $collaboratorModel = new ListCollaboratorModel();
            if ($collaboratorModel->isCollaborator($listId, $inviteeId)) {
                return false; // Already a collaborator
            }
        }

        $token = bin2hex(random_bytes(32));
        $expiresAt = date('Y-m-d H:i:s', strtotime('+7 days'));

        $data = [
            'list_id' => $listId,
            'inviter_id' => $inviterId,
            'invitee_email' => $inviteeEmail,
            'invitee_id' => $inviteeId,
            'token' => $token,
            'status' => 'pending',
            'message' => $message,
            'expires_at' => $expiresAt,
        ];

        if ($this->insert($data)) {
            return $this->find($this->getInsertID());
        }

        return false;
    }

    /**
     * Get pending invitations for a user by email
     */
    public function getPendingInvitationsForEmail(string $email): array
    {
        return $this->select('list_invitations.*, lists.title as list_title, lists.slug as list_slug, 
                             users.username as inviter_username, users.first_name as inviter_first_name, 
                             users.last_name as inviter_last_name')
            ->join('lists', 'lists.id = list_invitations.list_id')
            ->join('users', 'users.id = list_invitations.inviter_id')
            ->where('list_invitations.invitee_email', $email)
            ->where('list_invitations.status', 'pending')
            ->where('list_invitations.expires_at >', date('Y-m-d H:i:s'))
            ->orderBy('list_invitations.created_at', 'DESC')
            ->findAll();
    }

    /**
     * Get pending invitations for a user by ID
     */
    public function getPendingInvitationsForUser(int $userId): array
    {
        return $this->select('list_invitations.*, lists.title as list_title, lists.slug as list_slug,
                             users.username as inviter_username, users.first_name as inviter_first_name,
                             users.last_name as inviter_last_name')
            ->join('lists', 'lists.id = list_invitations.list_id')
            ->join('users', 'users.id = list_invitations.inviter_id')
            ->where('list_invitations.invitee_id', $userId)
            ->where('list_invitations.status', 'pending')
            ->where('list_invitations.expires_at >', date('Y-m-d H:i:s'))
            ->orderBy('list_invitations.created_at', 'DESC')
            ->findAll();
    }

    /**
     * Get all invitations sent by a user
     */
    public function getSentInvitations(int $inviterId): array
    {
        return $this->select('list_invitations.*, lists.title as list_title')
            ->join('lists', 'lists.id = list_invitations.list_id')
            ->where('list_invitations.inviter_id', $inviterId)
            ->orderBy('list_invitations.created_at', 'DESC')
            ->findAll();
    }

    /**
     * Get invitations for a specific list
     */
    public function getListInvitations(int $listId): array
    {
        return $this->select('list_invitations.*, users.username as inviter_username')
            ->join('users', 'users.id = list_invitations.inviter_id')
            ->where('list_invitations.list_id', $listId)
            ->orderBy('list_invitations.created_at', 'DESC')
            ->findAll();
    }

    /**
     * Accept an invitation
     */
    public function acceptInvitation(string $token, int $userId): bool
    {
        $invitation = $this->where('token', $token)
            ->where('status', 'pending')
            ->first();

        if (!$invitation) {
            return false;
        }

        // Check if expired
        if (strtotime($invitation['expires_at']) < time()) {
            $this->update($invitation['id'], [
                'status' => 'expired',
                'responded_at' => date('Y-m-d H:i:s'),
            ]);
            return false;
        }

        $db = \Config\Database::connect();
        $db->transStart();

        // Update invitation status
        $this->update($invitation['id'], [
            'status' => 'accepted',
            'invitee_id' => $userId,
            'responded_at' => date('Y-m-d H:i:s'),
        ]);

        // Add user as collaborator
        $collaboratorModel = new ListCollaboratorModel();
        $collaboratorModel->addCollaborator($invitation['list_id'], $userId, 'editor', false);

        $db->transComplete();
        return $db->transStatus();
    }

    /**
     * Reject an invitation
     */
    public function rejectInvitation(string $token): bool
    {
        $invitation = $this->where('token', $token)
            ->where('status', 'pending')
            ->first();

        if (!$invitation) {
            return false;
        }

        return $this->update($invitation['id'], [
            'status' => 'rejected',
            'responded_at' => date('Y-m-d H:i:s'),
        ]);
    }

    /**
     * Cancel an invitation (by inviter)
     */
    public function cancelInvitation(int $invitationId, int $inviterId): bool
    {
        $invitation = $this->where('id', $invitationId)
            ->where('inviter_id', $inviterId)
            ->where('status', 'pending')
            ->first();

        if (!$invitation) {
            return false;
        }

        return $this->delete($invitationId);
    }

    /**
     * Expire old invitations
     */
    public function expireOldInvitations(): int
    {
        return $this->where('status', 'pending')
            ->where('expires_at <', date('Y-m-d H:i:s'))
            ->set(['status' => 'expired'])
            ->update();
    }

    /**
     * Get invitation by token
     */
    public function getInvitationByToken(string $token): ?array
    {
        $invitation = $this->select('list_invitations.*, lists.title as list_title, lists.slug as list_slug,
                                    users.username as inviter_username, users.first_name as inviter_first_name,
                                    users.last_name as inviter_last_name, users.avatar as inviter_avatar')
            ->join('lists', 'lists.id = list_invitations.list_id')
            ->join('users', 'users.id = list_invitations.inviter_id')
            ->where('list_invitations.token', $token)
            ->first();

        return $invitation ?: null;
    }

    /**
     * Link pending invitations to user account
     * Called when a user registers with an email that has pending invitations
     */
    public function linkInvitationsToUser(string $email, int $userId): int
    {
        return $this->where('invitee_email', $email)
            ->where('invitee_id', null)
            ->where('status', 'pending')
            ->set(['invitee_id' => $userId])
            ->update();
    }
}
