<?php

namespace App\Models;

use CodeIgniter\Model;

class DrawingParticipantModel extends Model
{
    protected $table = 'drawing_participants';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = ['drawing_id', 'user_id', 'assigned_to_user_id', 'list_id', 'status'];

    protected bool $allowEmptyInserts = false;

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    // Validation
    protected $validationRules = [
        'drawing_id' => 'required|integer',
        'user_id' => 'required|integer',
    ];

    protected $validationMessages = [];
    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    public function getParticipantsByDrawing($drawingId)
    {
        return $this->where('drawing_id', $drawingId)->findAll();
    }

    public function getUserParticipations($userId)
    {
        return $this->select('drawing_participants.*, drawings.title as drawing_title, drawings.event_date, drawings.creator_id,
                            assigned_users.first_name as assigned_first_name, assigned_users.last_name as assigned_last_name,
                            lists.slug as list_slug')
            ->join('drawings', 'drawings.id = drawing_participants.drawing_id')
            ->join('users as assigned_users', 'assigned_users.id = drawing_participants.assigned_to_user_id', 'left')
            ->join('lists', 'lists.id = drawing_participants.list_id', 'left')
            ->where('drawing_participants.user_id', $userId)
            ->orderBy('drawings.created_at', 'DESC')
            ->findAll();
    }

    public function getAssignmentForUser($drawingId, $userId)
    {
        return $this->where('drawing_id', $drawingId)
            ->where('user_id', $userId)
            ->first();
    }

    public function acceptParticipation($participantId)
    {
        return $this->update($participantId, ['status' => 'accepted']);
    }

    public function declineParticipation($participantId)
    {
        return $this->update($participantId, ['status' => 'declined']);
    }

    public function getPendingInvitations($userId)
    {
        return $this->select('drawing_participants.*, drawings.title as drawing_title, drawings.event_date')
            ->join('drawings', 'drawings.id = drawing_participants.drawing_id')
            ->where('drawing_participants.user_id', $userId)
            ->where('drawing_participants.status', 'pending')
            ->orderBy('drawing_participants.created_at', 'DESC')
            ->findAll();
    }

    public function getAcceptedParticipants($drawingId)
    {
        return $this->where('drawing_id', $drawingId)
            ->where('status', 'accepted')
            ->findAll();
    }
}
