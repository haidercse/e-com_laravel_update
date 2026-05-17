<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AttributesTableSeeder extends Seeder
{
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('attributes')->truncate();
        DB::table('attributes')->insert([
  0 => 
  [
    'id' => 1,
    'name' => 'Size',
    'created_at' => '2020-02-24 06:55:07',
    'updated_at' => '2020-02-24 06:55:07',
  ],
  1 => 
  [
    'id' => 2,
    'name' => 'Fabric',
    'created_at' => '2020-02-24 06:55:13',
    'updated_at' => '2020-02-24 06:55:13',
  ],
]);
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
