<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ScheduleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $matkulMobile = DB::table('matakuliah')->where('nama_matkul', 'Pemrograman Mobile')->first();
        $labRuangan = DB::table('ruangan')->where('nama_ruangan', 'Lab Terpadu 1')->first();
        $dosenTuriman = DB::table('pengguna')->where('nama', 'Turiman, M.Kom.')->first();

        if ($matkulMobile && $labRuangan && $dosenTuriman) {
            DB::table('jadwal')->insert([
                [
                    'matakuliah_id' => $matkulMobile->id,
                    'ruangan_id' => $labRuangan->id,
                    'dosen_id' => $dosenTuriman->id,
                    'hari' => 'Senin',
                    'jam_mulai' => '08:00:00',
                    'jam_selesai' => '10:30:00',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ]);
        }
    }
}
