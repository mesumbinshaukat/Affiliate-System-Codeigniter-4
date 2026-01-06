<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateListSectionsTable extends Migration
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
            'title' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'comment' => 'Section title (e.g., Jewelry, Tech, Lifetime)',
            ],
            'position' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 0,
                'comment' => 'Display order of sections',
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
        $this->forge->addKey('list_id');
        $this->forge->addForeignKey('list_id', 'lists', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('list_sections');
    }

    public function down()
    {
        $this->forge->dropTable('list_sections');
    }
}
