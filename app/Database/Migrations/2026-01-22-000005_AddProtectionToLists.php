<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Forge;
use CodeIgniter\Database\Migration;

class AddProtectionToLists extends Migration
{
    public function up()
    {
        $fields = [
            'protection_type' => [
                'type' => 'ENUM',
                'constraint' => ['none', 'password', 'question'],
                'default' => 'none',
                'null' => false,
                'after' => 'is_crossable',
            ],
            'protection_password' => [
                'type' => 'TEXT',
                'null' => true,
                'after' => 'protection_type',
            ],
            'protection_question' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
                'after' => 'protection_password',
            ],
            'protection_answer' => [
                'type' => 'TEXT',
                'null' => true,
                'after' => 'protection_question',
            ],
        ];

        $this->forge->addColumn('lists', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('lists', 'protection_type');
        $this->forge->dropColumn('lists', 'protection_password');
        $this->forge->dropColumn('lists', 'protection_question');
        $this->forge->dropColumn('lists', 'protection_answer');
    }
}
