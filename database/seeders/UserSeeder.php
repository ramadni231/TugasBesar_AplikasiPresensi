<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('pengguna')->insert([
            [
                'nama' => 'Admin Pusat',
                'nomor_identitas' => 'ADMIN001',
                'email' => 'admin@stmik.ac.id',
                'password' => Hash::make('admin123'),
                'peran' => 'admin',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama' => 'Turiman, M.Kom.',
                'nomor_identitas' => 'DOSEN001',
                'email' => 'turiman@example.com',
                'password' => Hash::make('dosen123'),
                'peran' => 'dosen',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama' => 'Rikki Ramadhani',
                'nomor_identitas' => 'STI202303415',
                'email' => 'rikki@example.com',
                'password' => Hash::make('11111111'),
                'peran' => 'mahasiswa',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama' => 'Andrean Syah Putra',
                'nomor_identitas' => 'STI202303719',
                'email' => 'andrean@example.com',
                'password' => Hash::make('password123'),
                'peran' => 'mahasiswa',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama' => 'Fani Amalia Riswati',
                'nomor_identitas' => 'STI202303720',
                'email' => 'fani@example.com',
                'password' => Hash::make('password123'),
                'peran' => 'mahasiswa',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
