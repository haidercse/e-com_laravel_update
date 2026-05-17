<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class HomebannersTableSeeder extends Seeder
{
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('homebanners')->truncate();
        DB::table('homebanners')->insert([
            [
                'id' => 1,
                'image' => 'banner4.jpg',
                'created_at' => '2026-05-04 10:00:00',
                'updated_at' => '2026-05-04 10:00:00',
            ],
            [
                'id' => 2,
                'image' => 'banner5.jpg',
                'created_at' => '2026-05-04 10:00:00',
                'updated_at' => '2026-05-04 10:00:00',
            ],
            [
                'id' => 3,
                'image' => 'banner6.png',
                'created_at' => '2026-05-04 10:00:00',
                'updated_at' => '2026-05-04 10:00:00',
            ],
            [
                'id' => 4,
                'image' => 'banner7.png',
                'created_at' => '2026-05-04 10:00:00',
                'updated_at' => '2026-05-04 10:00:00',
            ],
        ]);
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
