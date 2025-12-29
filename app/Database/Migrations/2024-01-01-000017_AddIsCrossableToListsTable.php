<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddIsCrossableToListsTable extends Migration
{
    public function up()
    {
        $this->forge->addColumn('lists', [
            'is_crossable' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 1,
                'after' => 'is_featured',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('lists', 'is_crossable');
    }
}
