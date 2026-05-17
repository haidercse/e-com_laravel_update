<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ProductTaxesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('product_taxes')->delete();
        
        
        
    }
}