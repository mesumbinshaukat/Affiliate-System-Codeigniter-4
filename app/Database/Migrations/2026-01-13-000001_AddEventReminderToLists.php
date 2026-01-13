<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddEventReminderToLists extends Migration
{
    public function up()
    {
        $fields = [
            'event_date' => [
                'type' => 'DATE',
                'null' => true,
                'comment' => 'Event date for the list (e.g., birthday, anniversary)',
                'after' => 'description',
            ],
            'reminder_enabled' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
                'comment' => 'Enable automatic email reminders',
                'after' => 'event_date',
            ],
            'reminder_intervals' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
                'comment' => 'Comma-separated reminder intervals in days (e.g., 30,14,7)',
                'after' => 'reminder_enabled',
            ],
        ];
        
        $this->forge->addColumn('lists', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('lists', ['event_date', 'reminder_enabled', 'reminder_intervals']);
    }
}
