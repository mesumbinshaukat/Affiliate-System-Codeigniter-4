<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddAgeFieldsToCategoriesTable extends Migration
{
    public function up()
    {
        $this->forge->addColumn('categories', [
            'min_age' => [
                'type' => 'INT',
                'constraint' => 3,
                'unsigned' => true,
                'null' => true,
                'comment' => 'Minimum age for this category',
            ],
            'max_age' => [
                'type' => 'INT',
                'constraint' => 3,
                'unsigned' => true,
                'null' => true,
                'comment' => 'Maximum age for this category',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('categories', ['min_age', 'max_age']);
    }
}
