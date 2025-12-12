<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddStatusToDrawingParticipantsTable extends Migration
{
    public function up()
    {
        $this->forge->addColumn('drawing_participants', [
            'status' => [
                'type' => 'ENUM',
                'constraint' => ['pending', 'accepted', 'declined'],
                'default' => 'pending',
                'comment' => 'Participation status: pending (invitation sent), accepted (user accepted), declined (user declined)',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('drawing_participants', 'status');
    }
}
