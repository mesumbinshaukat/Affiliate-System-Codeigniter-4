<?php

namespace App\Models;

use CodeIgniter\Model;

class DrawingModel extends Model
{
    protected $table = 'drawings';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = ['creator_id', 'title', 'description', 'event_date', 'status'];

    protected bool $allowEmptyInserts = false;

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    // Validation
    protected $validationRules = [
        'creator_id' => 'required|integer',
        'title' => 'required|min_length[3]|max_length[255]',
    ];

    protected $validationMessages = [];
    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    public function getUserDrawings($userId)
    {
        return $this->where('creator_id', $userId)
            ->orderBy('created_at', 'DESC')
            ->findAll();
    }

    public function getDrawingWithParticipants($drawingId)
    {
        return $this->select('drawings.*, users.username as creator_username, users.first_name as creator_first_name, users.last_name as creator_last_name')
            ->join('users', 'users.id = drawings.creator_id')
            ->where('drawings.id', $drawingId)
            ->first();
    }

    public function getDrawingParticipants($drawingId)
    {
        $participantModel = new DrawingParticipantModel();
        return $participantModel->select('drawing_participants.*, 
                                         users.username, users.email, users.first_name, users.last_name,
                                         assigned_users.username as assigned_username, assigned_users.first_name as assigned_first_name, assigned_users.last_name as assigned_last_name,
                                         lists.title as list_title')
            ->join('users', 'users.id = drawing_participants.user_id')
            ->join('users as assigned_users', 'assigned_users.id = drawing_participants.assigned_to_user_id', 'left')
            ->join('lists', 'lists.id = drawing_participants.list_id', 'left')
            ->where('drawing_participants.drawing_id', $drawingId)
            ->findAll();
    }

    public function getAllDrawingsWithStats()
    {
        $db = \Config\Database::connect();
        
        return $this->select('drawings.*, 
                            users.username as creator_username, 
                            users.first_name as creator_first_name, 
                            users.last_name as creator_last_name,
                            COUNT(DISTINCT dp.id) as total_participants,
                            SUM(CASE WHEN dp.status = "accepted" THEN 1 ELSE 0 END) as accepted_count,
                            SUM(CASE WHEN dp.status = "declined" THEN 1 ELSE 0 END) as declined_count,
                            SUM(CASE WHEN dp.status = "pending" THEN 1 ELSE 0 END) as pending_count')
            ->join('users', 'users.id = drawings.creator_id')
            ->join('drawing_participants as dp', 'dp.drawing_id = drawings.id', 'left')
            ->groupBy('drawings.id')
            ->orderBy('drawings.created_at', 'DESC')
            ->findAll();
    }

    public function getDrawingStats($drawingId)
    {
        return $this->select('drawings.*, 
                            users.username as creator_username, 
                            users.first_name as creator_first_name, 
                            users.last_name as creator_last_name,
                            COUNT(DISTINCT dp.id) as total_participants,
                            SUM(CASE WHEN dp.status = "accepted" THEN 1 ELSE 0 END) as accepted_count,
                            SUM(CASE WHEN dp.status = "declined" THEN 1 ELSE 0 END) as declined_count,
                            SUM(CASE WHEN dp.status = "pending" THEN 1 ELSE 0 END) as pending_count')
            ->join('users', 'users.id = drawings.creator_id')
            ->join('drawing_participants as dp', 'dp.drawing_id = drawings.id', 'left')
            ->where('drawings.id', $drawingId)
            ->groupBy('drawings.id')
            ->first();
    }
}
