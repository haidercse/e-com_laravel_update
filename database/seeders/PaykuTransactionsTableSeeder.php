<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class PaykuTransactionsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('payku_transactions')->delete();
        
        
        
    }
}