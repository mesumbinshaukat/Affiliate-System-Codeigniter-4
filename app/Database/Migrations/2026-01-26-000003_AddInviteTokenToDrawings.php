<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddInviteTokenToDrawings extends Migration
{
    public function up()
    {
        $this->forge->addColumn('drawings', [
            'invite_token' => [
                'type' => 'VARCHAR',
                'constraint' => 64,
                'null' => true,
                'after' => 'status',
            ],
        ]);

        $db = \Config\Database::connect();
        $builder = $db->table('drawings');
        $drawings = $builder->select('id')->get()->getResultArray();

        foreach ($drawings as $drawing) {
            $builder->update([
                'invite_token' => bin2hex(random_bytes(16)),
            ], ['id' => $drawing['id']]);
        }
    }

    public function down()
    {
        $this->forge->dropColumn('drawings', 'invite_token');
    }
}
