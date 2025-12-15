<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSalesTable extends Migration
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
            'sub_id' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'comment' => 'Tracking subId from affiliate link (format: user_id_list_id)',
            ],
            'order_id' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'comment' => 'Bol.com order ID',
            ],
            'product_id' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
                'comment' => 'Bol.com product ID',
            ],
            'quantity' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 1,
            ],
            'commission' => [
                'type' => 'DECIMAL',
                'constraint' => [10, 2],
                'default' => 0.00,
                'comment' => 'Commission amount in EUR',
            ],
            'revenue_excl_vat' => [
                'type' => 'DECIMAL',
                'constraint' => [10, 2],
                'null' => true,
                'comment' => 'Order revenue excluding VAT',
            ],
            'status' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'default' => 'pending',
                'comment' => 'Commission status: pending, approved, rejected',
            ],
            'user_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
                'comment' => 'List owner user ID (extracted from subId)',
            ],
            'list_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
                'comment' => 'List ID (extracted from subId)',
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
        $this->forge->addKey('sub_id');
        $this->forge->addKey('order_id');
        $this->forge->addKey('user_id');
        $this->forge->addKey('list_id');
        $this->forge->addKey('status');
        $this->forge->addForeignKey('user_id', 'users', 'id', 'SET NULL', 'CASCADE');
        $this->forge->addForeignKey('list_id', 'lists', 'id', 'SET NULL', 'CASCADE');
        $this->forge->createTable('sales');
    }

    public function down()
    {
        $this->forge->dropTable('sales');
    }
}
