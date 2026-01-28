<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddGroupGiftToListProducts extends Migration
{
    public function up()
    {
        $db = \Config\Database::connect();
        $fields = [];

        if (! $db->fieldExists('is_group_gift', 'list_products')) {
            $fields['is_group_gift'] = [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
                'after' => 'custom_note',
                'comment' => 'Enable group contributions for this product',
            ];
        }

        if (! $db->fieldExists('target_amount', 'list_products')) {
            $fields['target_amount'] = [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'null' => true,
                'after' => isset($fields['is_group_gift']) ? 'is_group_gift' : 'custom_note',
                'comment' => 'Target amount for group gift in EUR',
            ];
        }

        if (! $db->fieldExists('section_id', 'list_products')) {
            $fields['section_id'] = [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
                'after' => isset($fields['target_amount']) ? 'target_amount' : 'custom_note',
                'comment' => 'Reference to list_sections table',
            ];
        }

        if (! empty($fields)) {
            $this->forge->addColumn('list_products', $fields);
        }

        $constraintName = 'list_products_section_id_foreign';
        if ($db->fieldExists('section_id', 'list_products') && ! $this->foreignKeyExists($constraintName)) {
            $db->query('ALTER TABLE `list_products` ADD CONSTRAINT `' . $constraintName . '` FOREIGN KEY (`section_id`) REFERENCES `list_sections`(`id`) ON DELETE SET NULL ON UPDATE CASCADE');
        }
    }

    public function down()
    {
        $constraintName = 'list_products_section_id_foreign';
        $db = \Config\Database::connect();

        if ($this->foreignKeyExists($constraintName)) {
            $this->forge->dropForeignKey('list_products', $constraintName);
        }

        foreach (['section_id', 'target_amount', 'is_group_gift'] as $column) {
            if ($db->fieldExists($column, 'list_products')) {
                $this->forge->dropColumn('list_products', $column);
            }
        }
    }

    private function foreignKeyExists(string $constraintName): bool
    {
        $db = \Config\Database::connect();
        $sql = "SELECT 1 FROM INFORMATION_SCHEMA.TABLE_CONSTRAINTS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'list_products' AND CONSTRAINT_NAME = ? LIMIT 1";
        $result = $db->query($sql, [$constraintName]);
        return $result && $result->getNumRows() > 0;
    }
}
