<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateListInvitationsTable extends Migration
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
            'inviter_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'comment' => 'User who sent the invitation',
            ],
            'invitee_email' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'comment' => 'Email of person being invited',
            ],
            'invitee_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
                'comment' => 'User ID if invitee has an account',
            ],
            'token' => [
                'type' => 'VARCHAR',
                'constraint' => 64,
                'comment' => 'Unique invitation token',
            ],
            'status' => [
                'type' => 'ENUM',
                'constraint' => ['pending', 'accepted', 'rejected', 'expired'],
                'default' => 'pending',
            ],
            'message' => [
                'type' => 'TEXT',
                'null' => true,
                'comment' => 'Personal message from inviter',
            ],
            'expires_at' => [
                'type' => 'DATETIME',
                'null' => true,
                'comment' => 'Invitation expiry (default 7 days)',
            ],
            'responded_at' => [
                'type' => 'DATETIME',
                'null' => true,
                'comment' => 'When invitation was accepted/rejected',
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
        $this->forge->addKey('token');
        $this->forge->addKey('invitee_email');
        $this->forge->addKey('invitee_id');
        $this->forge->addKey('status');
        $this->forge->addForeignKey('list_id', 'lists', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('inviter_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('invitee_id', 'users', 'id', 'SET NULL', 'CASCADE');
        $this->forge->createTable('list_invitations');
    }

    public function down()
    {
        $this->forge->dropTable('list_invitations');
    }
}
