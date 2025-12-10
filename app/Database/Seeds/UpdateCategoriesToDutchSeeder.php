<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UpdateCategoriesToDutchSeeder extends Seeder
{
    public function run()
    {
        // Drop existing categories
        $this->db->table('categories')->truncate();

        // Create default categories in Dutch
        $categories = [
            ['name' => 'Elektronica', 'slug' => 'electronics', 'icon' => 'laptop', 'status' => 'active'],
            ['name' => 'Baby & Kinderen', 'slug' => 'baby-kids', 'icon' => 'baby-carriage', 'status' => 'active'],
            ['name' => 'Huis & Wonen', 'slug' => 'home-living', 'icon' => 'home', 'status' => 'active'],
            ['name' => 'Mode', 'slug' => 'fashion', 'icon' => 'shirt', 'status' => 'active'],
            ['name' => 'Sport & Buiten', 'slug' => 'sports-outdoor', 'icon' => 'dumbbell', 'status' => 'active'],
            ['name' => 'Boeken & Media', 'slug' => 'books-media', 'icon' => 'book', 'status' => 'active'],
            ['name' => 'Schoonheid & Gezondheid', 'slug' => 'beauty-health', 'icon' => 'heart', 'status' => 'active'],
            ['name' => 'Speelgoed & Spellen', 'slug' => 'toys-games', 'icon' => 'gamepad', 'status' => 'active'],
        ];

        foreach ($categories as $category) {
            $category['created_at'] = date('Y-m-d H:i:s');
            $category['updated_at'] = date('Y-m-d H:i:s');
            $this->db->table('categories')->insert($category);
        }
    }
}
