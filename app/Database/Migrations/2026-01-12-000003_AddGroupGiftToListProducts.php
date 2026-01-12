<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddGroupGiftToListProducts extends Migration
{
    public function up()
    {
        $this->forge->addColumn('list_products', [
            'is_group_gift' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
                'after' => 'custom_note',
                'comment' => 'Enable group contributions for this product',
            ],
            'target_amount' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'null' => true,
                'after' => 'is_group_gift',
                'comment' => 'Target amount for group gift in EUR',
            ],
            'section_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
                'after' => 'target_amount',
                'comment' => 'Reference to list_sections table',
            ],
        ]);

        // Add foreign key for section_id
        $this->forge->addForeignKey('section_id', 'list_sections', 'id', 'SET NULL', 'CASCADE', 'list_products');
    }

    public function down()
    {
        // Drop foreign key first
        $this->forge->dropForeignKey('list_products', 'list_products_section_id_foreign');
        
        $this->forge->dropColumn('list_products', ['is_group_gift', 'target_amount', 'section_id']);
    }
}
