<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BrandsTableSeeder extends Seeder
{
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('brands')->truncate();
        DB::table('brands')->insert([
  0 => 
  [
    'id' => 1,
    'name' => 'Walton',
    'logo' => 'uploads/brands/brand.jpg',
    'top' => 1,
    'slug' => 'walton',
    'meta_title' => 'Walton',
    'meta_description' => 'Walton electronics and appliances',
    'created_at' => '2026-05-04 10:00:00',
    'updated_at' => '2026-05-04 10:00:00',
  ],
  1 => 
  [
    'id' => 2,
    'name' => 'Samsung',
    'logo' => 'uploads/brands/brand.jpg',
    'top' => 1,
    'slug' => 'samsung',
    'meta_title' => 'Samsung',
    'meta_description' => 'Samsung mobile phones and electronics',
    'created_at' => '2026-05-04 10:00:00',
    'updated_at' => '2026-05-04 10:00:00',
  ],
  2 => 
  [
    'id' => 3,
    'name' => 'Xiaomi',
    'logo' => 'uploads/brands/brand.jpg',
    'top' => 1,
    'slug' => 'xiaomi',
    'meta_title' => 'Xiaomi',
    'meta_description' => 'Xiaomi smartphones and accessories',
    'created_at' => '2026-05-04 10:00:00',
    'updated_at' => '2026-05-04 10:00:00',
  ],
  3 => 
  [
    'id' => 4,
    'name' => 'OPPO',
    'logo' => 'uploads/brands/brand.jpg',
    'top' => 1,
    'slug' => 'oppo',
    'meta_title' => 'OPPO',
    'meta_description' => 'OPPO mobile phones and accessories',
    'created_at' => '2026-05-04 10:00:00',
    'updated_at' => '2026-05-04 10:00:00',
  ],
  4 => 
  [
    'id' => 5,
    'name' => 'Infinix',
    'logo' => 'uploads/brands/brand.jpg',
    'top' => 1,
    'slug' => 'infinix',
    'meta_title' => 'Infinix',
    'meta_description' => 'Infinix smartphones and accessories',
    'created_at' => '2026-05-04 10:00:00',
    'updated_at' => '2026-05-04 10:00:00',
  ],
  5 => 
  [
    'id' => 6,
    'name' => 'Nokia',
    'logo' => 'uploads/brands/brand.jpg',
    'top' => 1,
    'slug' => 'nokia',
    'meta_title' => 'Nokia',
    'meta_description' => 'Nokia phones and accessories',
    'created_at' => '2026-05-04 10:00:00',
    'updated_at' => '2026-05-04 10:00:00',
  ],
]);
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
