<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class FlashDealProductsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        \DB::table('flash_deal_products')->delete();
        \DB::table('flash_deal_products')->insert([
            [
                'id' => 1,
                'flash_deal_id' => 1,
                'product_id' => 1,
                'discount' => 1200,
                'discount_type' => 'amount',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id' => 2,
                'flash_deal_id' => 1,
                'product_id' => 2,
                'discount' => 10,
                'discount_type' => 'percent',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id' => 3,
                'flash_deal_id' => 1,
                'product_id' => 4,
                'discount' => 150,
                'discount_type' => 'amount',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
        ]);
    }
}