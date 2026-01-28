<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class InitialSeeder extends Seeder
{
    public function run()
    {
        // Create admin user
        $this->db->table('users')->insert([
            'username' => 'admin',
            'email' => getenv('ADMIN_EMAIL') ?: 'admin@maakjelijstje.nl',
            'password' => password_hash(getenv('ADMIN_PASSWORD') ?: 'Admin@123', PASSWORD_DEFAULT),
            'first_name' => 'Admin',
            'last_name' => 'User',
            'role' => 'admin',
            'status' => 'active',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        // Create default categories
        $categories = [
            ['name' => 'Electronics', 'slug' => 'electronics', 'icon' => 'laptop', 'status' => 'active'],
            ['name' => 'Baby & Kids', 'slug' => 'baby-kids', 'icon' => 'baby-carriage', 'status' => 'active'],
            ['name' => 'Home & Living', 'slug' => 'home-living', 'icon' => 'home', 'status' => 'active'],
            ['name' => 'Fashion', 'slug' => 'fashion', 'icon' => 'shirt', 'status' => 'active'],
            ['name' => 'Sports & Outdoor', 'slug' => 'sports-outdoor', 'icon' => 'dumbbell', 'status' => 'active'],
            ['name' => 'Books & Media', 'slug' => 'books-media', 'icon' => 'book', 'status' => 'active'],
            ['name' => 'Beauty & Health', 'slug' => 'beauty-health', 'icon' => 'heart', 'status' => 'active'],
            ['name' => 'Toys & Games', 'slug' => 'toys-games', 'icon' => 'gamepad', 'status' => 'active'],
        ];

        foreach ($categories as $category) {
            $category['created_at'] = date('Y-m-d H:i:s');
            $category['updated_at'] = date('Y-m-d H:i:s');
            $this->db->table('categories')->insert($category);
        }

        // Create affiliate source for Bol.com
        $this->db->table('affiliate_sources')->insert([
            'name' => 'Bol.com',
            'slug' => 'bol-com',
            'api_endpoint' => 'https://api.bol.com/catalog/v4',
            'status' => 'active',
            'settings' => json_encode([
                'client_id' => getenv('BOL_CLIENT_ID'),
                'client_secret' => getenv('BOL_CLIENT_SECRET'),
                'affiliate_id' => getenv('BOL_AFFILIATE_ID'),
            ]),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        // Create default settings
        $settings = [
            ['key' => 'site_name', 'value' => 'Maakjelijstje.nl', 'type' => 'string'],
            ['key' => 'site_description', 'value' => 'Create and share product lists', 'type' => 'string'],
            ['key' => 'items_per_page', 'value' => '12', 'type' => 'integer'],
            ['key' => 'enable_registration', 'value' => '1', 'type' => 'boolean'],
            ['key' => 'require_email_verification', 'value' => '0', 'type' => 'boolean'],
        ];

        foreach ($settings as $setting) {
            $setting['updated_at'] = date('Y-m-d H:i:s');
            $this->db->table('settings')->insert($setting);
        }
    }
}
