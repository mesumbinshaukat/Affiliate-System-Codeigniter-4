<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateListCollaboratorsTable extends Migration
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
            'user_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'comment' => 'Co-owner user ID',
            ],
            'role' => [
                'type' => 'ENUM',
                'constraint' => ['owner', 'editor'],
                'default' => 'editor',
                'comment' => 'owner = original creator, editor = co-owner with edit rights',
            ],
            'can_invite' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
                'comment' => 'Can this collaborator invite others',
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
        $this->forge->addKey(['list_id', 'user_id'], false, true); // Unique constraint
        $this->forge->addForeignKey('list_id', 'lists', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('list_collaborators');
    }

    public function down()
    {
        $this->forge->dropTable('list_collaborators');
    }
}
