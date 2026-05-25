<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WorkScheduleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('work_schedules')->insert([
            [
                'user_id' => 2,
                'shift_id' => 1,
                'work_date' => now()->toDateString(),
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }
}
