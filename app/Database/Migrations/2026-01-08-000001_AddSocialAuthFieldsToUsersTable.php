<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddSocialAuthFieldsToUsersTable extends Migration
{
    public function up()
    {
        $this->forge->addColumn('users', [
            'provider' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
                'comment' => 'OAuth provider: facebook, google, etc.',
                'after' => 'password',
            ],
            'provider_id' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
                'comment' => 'Unique ID from OAuth provider',
                'after' => 'provider',
            ],
            'provider_token' => [
                'type' => 'TEXT',
                'null' => true,
                'comment' => 'OAuth access token (encrypted)',
                'after' => 'provider_id',
            ],
            'email_verified' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
                'comment' => 'Email verification status',
                'after' => 'provider_token',
            ],
        ]);

        // Add index for faster lookups
        $this->forge->addKey(['provider', 'provider_id'], false, false, 'idx_provider_auth');
    }

    public function down()
    {
        $this->forge->dropKey('users', 'idx_provider_auth');
        $this->forge->dropColumn('users', ['provider', 'provider_id', 'provider_token', 'email_verified']);
    }
}
