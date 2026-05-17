<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class FlashDealsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        \DB::table('flash_deals')->delete();
        \DB::table('flash_deals')->insert([
            [
                'id' => 1,
                'title' => 'Bangladesh Flash Sale',
                'start_date' => time() - 86400,
                'end_date' => time() + 86400,
                'status' => 1,
                'featured' => 1,
                'background_color' => '#FF5733',
                'text_color' => '#FFFFFF',
                'banner' => 'banner4.jpg',
                'slug' => 'bangladesh-flash-sale',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
        ]);
    }
}