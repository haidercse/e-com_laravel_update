<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class CategoriesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('categories')->delete();
        
        \DB::table('categories')->insert(array (
            0 => 
            array (
                'id' => 1,
                'parent_id' => 0,
                'level' => 0,
                'name' => 'Mobile Phones',
                'order_level' => 0,
                'commision_rate' => 0.0,
                'banner' => 'uploads/categories/banner/category-banner.jpg',
                'icon' => 'uploads/categories/icon/mobile.png',
                'featured' => 1,
                'top' => 1,
                'digital' => 0,
                'slug' => 'mobile-phones',
                'meta_title' => 'Mobile Phones',
                'meta_description' => 'Smartphones and mobile accessories for Bangladesh',
                'created_at' => '2026-05-04 10:00:00',
                'updated_at' => '2026-05-04 10:00:00',
            ),
            1 => 
            array (
                'id' => 2,
                'parent_id' => 0,
                'level' => 0,
                'name' => 'Men\'s Fashion',
                'order_level' => 0,
                'commision_rate' => 0.0,
                'banner' => 'uploads/categories/banner/category-banner.jpg',
                'icon' => 'uploads/categories/icon/fashion.png',
                'featured' => 1,
                'top' => 1,
                'digital' => 0,
                'slug' => 'mens-fashion',
                'meta_title' => 'Men\'s Fashion',
                'meta_description' => 'Men\'s clothing, shirts, and accessories',
                'created_at' => '2026-05-04 10:00:00',
                'updated_at' => '2026-05-04 10:00:00',
            ),
            2 => 
            array (
                'id' => 3,
                'parent_id' => 0,
                'level' => 0,
                'name' => 'Home Appliances',
                'order_level' => 0,
                'commision_rate' => 0.0,
                'banner' => 'uploads/categories/banner/category-banner.jpg',
                'icon' => 'uploads/categories/icon/home.png',
                'featured' => 1,
                'top' => 1,
                'digital' => 0,
                'slug' => 'home-appliances',
                'meta_title' => 'Home Appliances',
                'meta_description' => 'Kitchen and home appliances for Bangladesh',
                'created_at' => '2026-05-04 10:00:00',
                'updated_at' => '2026-05-04 10:00:00',
            ),
            3 => 
            array (
                'id' => 4,
                'parent_id' => 0,
                'level' => 0,
                'name' => 'Grocery',
                'order_level' => 0,
                'commision_rate' => 0.0,
                'banner' => 'uploads/categories/banner/category-banner.jpg',
                'icon' => 'uploads/categories/icon/grocery.png',
                'featured' => 1,
                'top' => 1,
                'digital' => 0,
                'slug' => 'grocery',
                'meta_title' => 'Grocery',
                'meta_description' => 'Daily grocery items and food products for Bangladesh',
                'created_at' => '2026-05-04 10:00:00',
                'updated_at' => '2026-05-04 10:00:00',
            ),
            4 => 
            array (
                'id' => 5,
                'parent_id' => 0,
                'level' => 0,
                'name' => 'Beauty & Personal Care',
                'order_level' => 0,
                'commision_rate' => 0.0,
                'banner' => 'uploads/categories/banner/category-banner.jpg',
                'icon' => 'uploads/categories/icon/beauty.png',
                'featured' => 1,
                'top' => 1,
                'digital' => 0,
                'slug' => 'beauty-personal-care',
                'meta_title' => 'Beauty & Personal Care',
                'meta_description' => 'Skincare, hair care and beauty essentials for Bangladesh.',
                'created_at' => '2026-05-04 10:00:00',
                'updated_at' => '2026-05-04 10:00:00',
            ),
            5 => 
            array (
                'id' => 6,
                'parent_id' => 0,
                'level' => 0,
                'name' => 'Electronics',
                'order_level' => 0,
                'commision_rate' => 0.0,
                'banner' => 'uploads/categories/banner/category-banner.jpg',
                'icon' => 'uploads/categories/icon/electronics.png',
                'featured' => 1,
                'top' => 1,
                'digital' => 0,
                'slug' => 'electronics',
                'meta_title' => 'Electronics',
                'meta_description' => 'TV, audio, and electronic accessories for Bangladeshi shoppers.',
                'created_at' => '2026-05-04 10:00:00',
                'updated_at' => '2026-05-04 10:00:00',
            ),
        ));
        
        
    }
}