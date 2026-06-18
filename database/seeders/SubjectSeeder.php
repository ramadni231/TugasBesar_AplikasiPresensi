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
        DB::table('matakuliah')->insert([
            [
                'kode_matkul' => 'IF201',
                'nama_matkul' => 'Pemrograman Mobile',
                'sks' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kode_matkul' => 'IF202',
                'nama_matkul' => 'Pemrograman Web',
                'sks' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kode_matkul' => 'IF203',
                'nama_matkul' => 'Internet of Things',
                'sks' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kode_matkul' => 'IF204',
                'nama_matkul' => 'Data Mining',
                'sks' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
