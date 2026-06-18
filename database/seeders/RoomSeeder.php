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
        DB::table('ruangan')->insert([
            [
                'nama_ruangan' => 'Lab Terpadu 1',
                'kapasitas' => 40,
                'latitude' => -7.4243,
                'longitude' => 109.2302,
                'radius_meter' => 50,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_ruangan' => 'Ruang Teori A',
                'kapasitas' => 60,
                'latitude' => -7.4250,
                'longitude' => 109.2310,
                'radius_meter' => 50,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
