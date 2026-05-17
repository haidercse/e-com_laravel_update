<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BlogsTableSeeder extends Seeder
{
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('blogs')->truncate();
        // No data found for this table.
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
