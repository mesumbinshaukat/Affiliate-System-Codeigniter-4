<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddGenderToUsersTable extends Migration
{
    public function up()
    {
        $this->forge->addColumn('users', [
            'gender' => [
                'type' => 'ENUM',
                'constraint' => ['male', 'female', 'other'],
                'null' => true,
                'default' => null,
                'after' => 'date_of_birth',
                'comment' => 'User gender: male, female, or other',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('users', 'gender');
    }
}
