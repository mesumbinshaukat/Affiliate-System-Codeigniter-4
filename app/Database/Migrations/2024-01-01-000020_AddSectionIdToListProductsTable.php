<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddSectionIdToListProductsTable extends Migration
{
    public function up()
    {
        $this->forge->addColumn('list_products', [
            'section_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
                'comment' => 'Optional section/category within the list',
                'after' => 'list_id',
            ],
        ]);

        // Add foreign key
        $this->forge->processIndexes('list_products');
        $this->db->query('ALTER TABLE list_products ADD CONSTRAINT fk_list_products_section FOREIGN KEY (section_id) REFERENCES list_sections(id) ON DELETE SET NULL ON UPDATE CASCADE');
    }

    public function down()
    {
        // Drop foreign key first
        $this->db->query('ALTER TABLE list_products DROP FOREIGN KEY fk_list_products_section');
        
        // Drop column
        $this->forge->dropColumn('list_products', 'section_id');
    }
}
