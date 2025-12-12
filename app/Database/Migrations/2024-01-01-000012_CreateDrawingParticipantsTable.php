<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateDrawingParticipantsTable extends Migration
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
            'drawing_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'user_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'assigned_to_user_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
                'comment' => 'The person this user drew (who they need to buy for)',
            ],
            'list_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
                'comment' => 'The wish list of the assigned person',
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('drawing_id', 'drawings', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('assigned_to_user_id', 'users', 'id', 'SET NULL', 'CASCADE');
        $this->forge->addForeignKey('list_id', 'lists', 'id', 'SET NULL', 'CASCADE');
        $this->forge->createTable('drawing_participants');
    }

    public function down()
    {
        $this->forge->dropTable('drawing_participants');
    }
}
