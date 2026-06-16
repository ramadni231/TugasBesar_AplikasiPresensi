<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SubjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('subjects')->insert([
            [
                'code' => 'IF201',
                'name' => 'Pemrograman Mobile',
                'sks' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'IF202',
                'name' => 'Pemrograman Web',
                'sks' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'IF203',
                'name' => 'Internet of Things',
                'sks' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'IF204',
                'name' => 'Data Mining',
                'sks' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
