<?php

namespace App\Models;

use CodeIgniter\Model;

class ListReminderModel extends Model
{
    protected $table = 'list_reminders';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'list_id',
        'recipient_email',
        'recipient_name',
        'reminder_type',
        'days_before',
        'sent_at',
        'status'
    ];

    protected bool $allowEmptyInserts = false;
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = '';

    /**
     * Check if reminder was already sent
     */
    public function wasReminderSent(int $listId, string $email, int $daysBefore): bool
    {
        $reminder = $this->where([
            'list_id' => $listId,
            'recipient_email' => $email,
            'days_before' => $daysBefore,
            'status' => 'sent'
        ])->first();

        return $reminder !== null;
    }

    /**
     * Mark reminder as sent
     */
    public function markAsSent(int $reminderId): bool
    {
        return $this->update($reminderId, [
            'status' => 'sent',
            'sent_at' => date('Y-m-d H:i:s')
        ]);
    }

    /**
     * Mark reminder as failed
     */
    public function markAsFailed(int $reminderId): bool
    {
        return $this->update($reminderId, [
            'status' => 'failed'
        ]);
    }

    /**
     * Create a reminder record
     */
    public function createReminder(array $data): int|false
    {
        // Check if already exists
        $existing = $this->where([
            'list_id' => $data['list_id'],
            'recipient_email' => $data['recipient_email'],
            'days_before' => $data['days_before']
        ])->first();

        if ($existing) {
            return $existing['id'];
        }

        return $this->insert($data);
    }

    /**
     * Get pending reminders that need to be sent today
     */
    public function getPendingReminders(): array
    {
        $db = \Config\Database::connect();
        $builder = $db->table($this->table . ' lr');
        
        $builder->select('lr.*, l.title as list_title, l.event_date, l.slug, u.first_name, u.last_name')
            ->join('lists l', 'l.id = lr.list_id')
            ->join('users u', 'u.id = l.user_id')
            ->where('lr.status', 'pending')
            ->where('l.reminder_enabled', 1)
            ->where('l.event_date IS NOT NULL');

        // Calculate which reminders should be sent today
        // Check if today is X days before the event
        $builder->where("DATEDIFF(l.event_date, CURDATE()) = lr.days_before");

        return $builder->get()->getResultArray();
    }

    /**
     * Get all reminders for a list
     */
    public function getListReminders(int $listId): array
    {
        return $this->where('list_id', $listId)
            ->orderBy('days_before', 'DESC')
            ->orderBy('sent_at', 'DESC')
            ->findAll();
    }

    /**
     * Delete all reminders for a list
     */
    public function deleteListReminders(int $listId): bool
    {
        return $this->where('list_id', $listId)->delete();
    }

    /**
     * Reset reminders for next year (for recurring events like birthdays)
     */
    public function resetListReminders(int $listId): bool
    {
        return $this->where('list_id', $listId)
            ->set(['status' => 'pending', 'sent_at' => null])
            ->update();
    }
}
