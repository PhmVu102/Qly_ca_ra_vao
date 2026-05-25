<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CompanyLocationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('company_locations')->insert([

            'name' => 'HUNONIC',

            'latitude' => 21.5942,

            'longitude' => 105.8481,

            'radius_meter' => 100,

            'is_main' => 1,

            'status' => 1,

            'created_at' => now(),

            'updated_at' => now(),
        ]);
    }
}
