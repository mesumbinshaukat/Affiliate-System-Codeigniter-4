<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Libraries\ReminderService;

class SendReminders extends BaseCommand
{
    protected $group = 'Tasks';
    protected $name = 'reminders:send';
    protected $description = 'Send pending event reminder emails';
    protected $usage = 'reminders:send';

    public function run(array $params)
    {
        CLI::write('Starting reminder email service...', 'green');
        
        $reminderService = new ReminderService();
        
        try {
            $results = $reminderService->sendPendingReminders();
            
            CLI::write('');
            CLI::write('✓ Reminder Email Report:', 'yellow');
            CLI::write("  Total pending: {$results['total']}", 'white');
            CLI::write("  Successfully sent: {$results['sent']}", 'green');
            CLI::write("  Failed: {$results['failed']}", 'red');
            CLI::write('');
            
            if ($results['total'] === 0) {
                CLI::write('No reminders to send today.', 'cyan');
            } elseif ($results['failed'] > 0) {
                CLI::write('Some reminders failed. Check logs for details.', 'yellow');
            } else {
                CLI::write('All reminders sent successfully!', 'green');
            }
            
        } catch (\Exception $e) {
            CLI::error('Error sending reminders: ' . $e->getMessage());
            log_message('error', 'Reminder command failed: ' . $e->getMessage());
        }
    }
}
