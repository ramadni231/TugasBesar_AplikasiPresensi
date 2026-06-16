<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoomSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('rooms')->insert([
            [
                'name' => 'Lab Terpadu 1',
                'capacity' => 40,
                'latitude' => -7.4243,
                'longitude' => 109.2302,
                'radius_meters' => 50,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Ruang Teori A',
                'capacity' => 60,
                'latitude' => -7.4250,
                'longitude' => 109.2310,
                'radius_meters' => 50,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
