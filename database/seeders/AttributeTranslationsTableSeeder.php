<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AttributeTranslationsTableSeeder extends Seeder
{
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('attribute_translations')->truncate();
        // No data found for this table.
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
