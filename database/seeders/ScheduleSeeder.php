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
        // Get IDs of needed relations
        $mobileSubject = DB::table('subjects')->where('name', 'Pemrograman Mobile')->first();
        $labRoom = DB::table('rooms')->where('name', 'Lab Terpadu 1')->first();
        $dosenTuriman = DB::table('users')->where('name', 'Turiman, M.Kom.')->first();

        if ($mobileSubject && $labRoom && $dosenTuriman) {
            DB::table('schedules')->insert([
                [
                    'subject_id' => $mobileSubject->id,
                    'room_id' => $labRoom->id,
                    'lecturer_id' => $dosenTuriman->id,
                    'day' => 'Senin',
                    'start_time' => '08:00:00',
                    'end_time' => '10:30:00',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ]);
        }
    }
}
