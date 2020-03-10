<?php

use Illuminate\Database\Seeder;

class TablepricesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('tableprices')->insert([
            ['description' => 'Padrão']
        ]);
    }
}
