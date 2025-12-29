<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddClaimedFieldsToListProductsTable extends Migration
{
    public function up()
    {
        $this->forge->addColumn('list_products', [
            'claimed_at' => [
                'type' => 'DATETIME',
                'null' => true,
                'after' => 'custom_note',
            ],
            'claimed_by_subid' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
                'after' => 'claimed_at',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('list_products', ['claimed_at', 'claimed_by_subid']);
    }
}
