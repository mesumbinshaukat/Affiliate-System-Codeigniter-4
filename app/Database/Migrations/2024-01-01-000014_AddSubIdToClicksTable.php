<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddSubIdToClicksTable extends Migration
{
    public function up()
    {
        $this->forge->addColumn('clicks', [
            'sub_id' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
                'comment' => 'Tracking subId for commission attribution (format: user_id_list_id)',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('clicks', 'sub_id');
    }
}
