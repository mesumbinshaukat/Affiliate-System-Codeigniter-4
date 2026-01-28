<?php

namespace App\Libraries;

use App\Models\ListModel;
use App\Models\ListReminderModel;
use App\Models\ListCollaboratorModel;
use App\Models\UserModel;

class ReminderService
{
    protected $listModel;
    protected $reminderModel;
    protected $collaboratorModel;
    protected $userModel;

    public function __construct()
    {
        $this->listModel = new ListModel();
        $this->reminderModel = new ListReminderModel();
        $this->collaboratorModel = new ListCollaboratorModel();
        $this->userModel = new UserModel();
    }

    /**
     * Generate reminders for a list when event date is set
     */
    public function generateRemindersForList(int $listId): bool
    {
        $list = $this->listModel->find($listId);
        
        if (!$list || !$list['event_date'] || !$list['reminder_enabled']) {
            return false;
        }

        // Get reminder intervals (default: 30, 14, 7 days)
        $intervals = $list['reminder_intervals'] 
            ? explode(',', $list['reminder_intervals']) 
            : [30, 14, 7];

        // Get list owner
        $owner = $this->userModel->find($list['user_id']);
        
        // Get all collaborators
        $collaborators = $this->collaboratorModel->getListCollaborators($listId);
        
        // Collect all recipients
        $recipients = [];
        
        // Add owner
        if ($owner && !empty($owner['email'])) {
            $recipients[] = [
                'email' => $owner['email'],
                'name' => $owner['first_name'] . ' ' . $owner['last_name'],
                'type' => 'owner'
            ];
        }
        
        // Add collaborators
        foreach ($collaborators as $collab) {
            if (!empty($collab['email'])) {
                $recipients[] = [
                    'email' => $collab['email'],
                    'name' => $collab['first_name'] . ' ' . $collab['last_name'],
                    'type' => 'collaborator'
                ];
            }
        }

        // Create reminder records for each recipient and interval
        $created = 0;
        foreach ($recipients as $recipient) {
            foreach ($intervals as $days) {
                $data = [
                    'list_id' => $listId,
                    'recipient_email' => $recipient['email'],
                    'recipient_name' => $recipient['name'],
                    'reminder_type' => $recipient['type'],
                    'days_before' => (int)trim($days),
                    'status' => 'pending'
                ];
                
                if ($this->reminderModel->createReminder($data)) {
                    $created++;
                }
            }
        }

        return $created > 0;
    }

    /**
     * Send pending reminders
     */
    public function sendPendingReminders(): array
    {
        $pendingReminders = $this->reminderModel->getPendingReminders();
        $results = [
            'sent' => 0,
            'failed' => 0,
            'total' => count($pendingReminders)
        ];

        foreach ($pendingReminders as $reminder) {
            if ($this->sendReminderEmail($reminder)) {
                $this->reminderModel->markAsSent($reminder['id']);
                $results['sent']++;
            } else {
                $this->reminderModel->markAsFailed($reminder['id']);
                $results['failed']++;
            }
        }

        return $results;
    }

    /**
     * Send reminder email to recipient
     */
    protected function sendReminderEmail(array $reminder): bool
    {
        $email = \Config\Services::email();
        
        $listUrl = base_url('list/' . $reminder['slug']);
        $ownerName = trim($reminder['first_name'] . ' ' . $reminder['last_name']);
        $eventDate = date('d F Y', strtotime($reminder['event_date']));
        $daysText = $reminder['days_before'] == 1 ? 'morgen' : $reminder['days_before'] . ' dagen';

        $subject = "🎁 Herinnering: {$reminder['list_title']} - Nog {$daysText}!";
        
        $message = "
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: linear-gradient(135deg, #E31E24, #c41a1f); color: white; padding: 30px; text-align: center; border-radius: 8px 8px 0 0; }
                .content { background: #f8f9fa; padding: 30px; border-radius: 0 0 8px 8px; }
                .event-info { background: white; padding: 20px; border-left: 4px solid #E31E24; margin: 20px 0; border-radius: 4px; }
                .btn { display: inline-block; padding: 12px 30px; background: #E31E24; color: white; text-decoration: none; border-radius: 5px; margin: 20px 0; }
                .footer { text-align: center; margin-top: 30px; color: #666; font-size: 12px; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>🎉 Evenement Herinnering</h1>
                </div>
                <div class='content'>
                    <p>Hallo {$reminder['recipient_name']},</p>
                    
                    <p>Dit is een vriendelijke herinnering dat <strong>{$ownerName}</strong>'s evenement binnenkort plaatsvindt!</p>
                    
                    <div class='event-info'>
                        <h2 style='margin-top: 0; color: #E31E24;'>{$reminder['list_title']}</h2>
                        <p><strong>📅 Datum:</strong> {$eventDate}</p>
                        <p><strong>⏰ Nog:</strong> {$daysText}</p>
                    </div>
                    
                    <p>Bekijk de verlanglijst om het perfecte cadeau te vinden:</p>
                    
                    <div style='text-align: center;'>
                        <a href='{$listUrl}' class='btn'>Bekijk Verlanglijst</a>
                    </div>
                    
                    <p style='margin-top: 30px; color: #666; font-size: 14px;'>
                        💡 <strong>Tip:</strong> Kies een cadeau voordat het te laat is!
                    </p>
                </div>
                <div class='footer'>
                    <p>Deze herinnering werd automatisch verzonden voor {$ownerName}'s verlanglijst.</p>
                    <p>&copy; " . date('Y') . " Lijst.je - Alle rechten voorbehouden</p>
                </div>
            </div>
        </body>
        </html>
        ";

        $email->setFrom('noreply@lijst.je', 'Lijst.je');
        $email->setTo($reminder['recipient_email']);
        $email->setSubject($subject);
        $email->setMessage($message);

        if ($email->send()) {
            log_message('info', "Reminder sent to {$reminder['recipient_email']} for list {$reminder['list_id']}");
            return true;
        } else {
            log_message('error', "Failed to send reminder to {$reminder['recipient_email']}: " . $email->printDebugger(['headers']));
            return false;
        }
    }

    /**
     * Regenerate reminders for a list (e.g., after event date change)
     */
    public function regenerateReminders(int $listId): bool
    {
        // Delete existing reminders
        $this->reminderModel->deleteListReminders($listId);
        
        // Generate new reminders
        return $this->generateRemindersForList($listId);
    }

    /**
     * Reset reminders for next year (for recurring events)
     */
    public function resetRemindersForNextYear(int $listId): bool
    {
        return $this->reminderModel->resetListReminders($listId);
    }
}
