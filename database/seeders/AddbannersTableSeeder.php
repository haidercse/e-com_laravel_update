<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AddbannersTableSeeder extends Seeder
{
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('addbanners')->truncate();
        DB::table('addbanners')->insert([
            [
                'id' => 1,
                'name' => '/products/category/mobile-phones',
                'image' => 'banner4.jpg',
                'created_at' => '2026-05-04 10:00:00',
                'updated_at' => '2026-05-04 10:00:00',
            ],
            [
                'id' => 2,
                'name' => '/products/category/mens-fashion',
                'image' => 'banner5.jpg',
                'created_at' => '2026-05-04 10:00:00',
                'updated_at' => '2026-05-04 10:00:00',
            ],
            [
                'id' => 3,
                'name' => '/products/category/home-appliances',
                'image' => 'banner6.png',
                'created_at' => '2026-05-04 10:00:00',
                'updated_at' => '2026-05-04 10:00:00',
            ],
            [
                'id' => 4,
                'name' => '/products/category/electronics',
                'image' => 'banner7.png',
                'created_at' => '2026-05-04 10:00:00',
                'updated_at' => '2026-05-04 10:00:00',
            ],
        ]);
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
