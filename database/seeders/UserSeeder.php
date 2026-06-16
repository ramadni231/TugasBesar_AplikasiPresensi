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
        DB::table('users')->insert([
            [
                'name' => 'Admin Pusat',
                'identity_number' => 'ADMIN001',
                'email' => 'admin@stmik.ac.id',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Turiman, M.Kom.',
                'identity_number' => '198001012010011001',
                'email' => 'turiman@example.com',
                'password' => Hash::make('dosen123'),
                'role' => 'dosen',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Rikki Ramadhani, M.Kom.',
                'identity_number' => '198502022015021002',
                'email' => 'rikki@example.com',
                'password' => Hash::make('dosen123'),
                'role' => 'dosen',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Andrean Syah Putra',
                'identity_number' => 'STI202303719',
                'email' => 'andrean@example.com',
                'password' => Hash::make('password123'),
                'role' => 'mahasiswa',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Fani Amalia Riswati',
                'identity_number' => 'STI202303720',
                'email' => 'fani@example.com',
                'password' => Hash::make('password123'),
                'role' => 'mahasiswa',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
