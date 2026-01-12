<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateContributionsTable extends Migration
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
            'list_product_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'comment' => 'Reference to list_products table',
            ],
            'contributor_name' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'comment' => 'Name of the person contributing',
            ],
            'contributor_email' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
                'comment' => 'Optional email for notifications',
            ],
            'amount' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'comment' => 'Contribution amount in EUR',
            ],
            'message' => [
                'type' => 'TEXT',
                'null' => true,
                'comment' => 'Optional message from contributor',
            ],
            'is_anonymous' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
                'comment' => 'Hide contributor name from public view',
            ],
            'status' => [
                'type' => 'ENUM',
                'constraint' => ['pending', 'completed', 'refunded'],
                'default' => 'completed',
                'comment' => 'Contribution status',
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
        $this->forge->addKey('list_product_id');
        $this->forge->addKey('status');
        $this->forge->addForeignKey('list_product_id', 'list_products', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('contributions');
    }

    public function down()
    {
        $this->forge->dropTable('contributions');
    }
}
