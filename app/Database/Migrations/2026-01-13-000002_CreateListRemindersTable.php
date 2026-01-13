<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateListRemindersTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'list_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'comment' => 'Reference to lists table',
            ],
            'recipient_email' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'comment' => 'Email address of reminder recipient',
            ],
            'recipient_name' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
                'comment' => 'Name of reminder recipient',
            ],
            'reminder_type' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'comment' => 'Type of reminder: collaborator, invited_person',
            ],
            'days_before' => [
                'type' => 'INT',
                'constraint' => 11,
                'comment' => 'Days before event (e.g., 30, 14, 7)',
            ],
            'sent_at' => [
                'type' => 'DATETIME',
                'null' => true,
                'comment' => 'When the reminder was sent',
            ],
            'status' => [
                'type' => 'ENUM',
                'constraint' => ['pending', 'sent', 'failed'],
                'default' => 'pending',
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey(['list_id', 'recipient_email', 'days_before'], false, 'idx_unique_reminder');
        $this->forge->addForeignKey('list_id', 'lists', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('list_reminders');
    }

    public function down()
    {
        $this->forge->dropTable('list_reminders');
    }
}
