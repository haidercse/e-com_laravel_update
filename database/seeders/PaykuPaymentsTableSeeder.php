<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class PaykuPaymentsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('payku_payments')->delete();
        
        
        
    }
}