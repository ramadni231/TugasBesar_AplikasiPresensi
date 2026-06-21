<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sesi_aktif', function (Blueprint $table) {
            $table->date('tanggal_reschedule')->nullable()->after('is_aktif');
            $table->time('jam_mulai_reschedule')->nullable()->after('tanggal_reschedule');
            $table->time('jam_selesai_reschedule')->nullable()->after('jam_mulai_reschedule');
            $table->foreignId('ruangan_id_reschedule')->nullable()->after('jam_selesai_reschedule')->constrained('ruangan')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('sesi_aktif', function (Blueprint $table) {
            $table->dropForeign(['ruangan_id_reschedule']);
            $table->dropColumn([
                'tanggal_reschedule',
                'jam_mulai_reschedule',
                'jam_selesai_reschedule',
                'ruangan_id_reschedule'
            ]);
        });
    }
};
