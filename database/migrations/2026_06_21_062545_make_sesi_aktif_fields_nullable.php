<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('sesi_aktif', function (Blueprint $table) {
            $table->string('token_qr')->nullable()->change();
            $table->dateTime('berakhir_pada')->nullable()->change();
            $table->boolean('is_aktif')->default(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sesi_aktif', function (Blueprint $table) {
            $table->string('token_qr')->nullable(false)->change();
            $table->dateTime('berakhir_pada')->nullable(false)->change();
            $table->boolean('is_aktif')->default(true)->change();
        });
    }
};
