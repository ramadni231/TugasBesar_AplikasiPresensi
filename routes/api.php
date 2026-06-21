<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Api\DosenController;
use App\Http\Controllers\Api\MahasiswaController;
use Illuminate\Support\Facades\Route;

// Auth Routes (Public)
Route::post('/login', [AuthController::class, 'login']);

// Protected Routes (Sanctum)
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/profile', [AuthController::class, 'profile']);
    Route::put('/profile/password', [AuthController::class, 'updatePassword']);

    // Modul Admin
    Route::middleware('role:admin')->prefix('admin')->group(function () {
        Route::get('/pengguna', [AdminController::class, 'getPengguna']);
        Route::post('/pengguna', [AdminController::class, 'storePengguna']);
        Route::put('/pengguna/{id}', [AdminController::class, 'updatePengguna']);
        Route::delete('/pengguna/{id}', [AdminController::class, 'destroyPengguna']);

        Route::get('/ruangan', [AdminController::class, 'getRuangan']);
        Route::post('/ruangan', [AdminController::class, 'storeRuangan']);
        Route::put('/ruangan/{id}', [AdminController::class, 'updateRuangan']);
        Route::delete('/ruangan/{id}', [AdminController::class, 'destroyRuangan']);

        Route::get('/matakuliah', [AdminController::class, 'getMatakuliah']);
        Route::post('/matakuliah', [AdminController::class, 'storeMatakuliah']);
        Route::put('/matakuliah/{id}', [AdminController::class, 'updateMatakuliah']);
        Route::delete('/matakuliah/{id}', [AdminController::class, 'destroyMatakuliah']);

        Route::get('/jadwal', [AdminController::class, 'getJadwal']);
        Route::post('/jadwal', [AdminController::class, 'storeJadwal']);
        Route::put('/jadwal/{id}', [AdminController::class, 'updateJadwal']);
        Route::delete('/jadwal/{id}', [AdminController::class, 'destroyJadwal']);
        Route::get('/jadwal/{id}/rekap', [AdminController::class, 'getJadwalRekap']);
        
        Route::get('/izin', [AdminController::class, 'getIzin']);
        Route::put('/izin/{id}/status', [AdminController::class, 'updateIzinStatus']);

        Route::post('/pengaturan/peminatan', [AdminController::class, 'toggleMasaPeminatan']);
        Route::get('/pengaturan/peminatan', [AdminController::class, 'getMasaPeminatanStatus']);
        Route::post('/pengaturan/mulai-semester', [AdminController::class, 'setTanggalMulaiSemester']);
        Route::get('/pengaturan/mulai-semester', [AdminController::class, 'getTanggalMulaiSemester']);
        Route::get('/peminatan', [AdminController::class, 'getKelolaPeminatan']);
        Route::put('/peminatan/{id}/status', [AdminController::class, 'updateStatusPeminatan']);
    });

    // Modul Dosen
    Route::middleware('role:dosen')->prefix('dosen')->group(function () {
        Route::get('/jadwal', [DosenController::class, 'getJadwal']);
        Route::get('/jadwal/hari-ini', [DosenController::class, 'getKelasAktifHariIni']);
        Route::put('/jadwal/{id}/reschedule', [DosenController::class, 'rescheduleJadwal']);
        Route::get('/jadwal/{id}/detail', [DosenController::class, 'getJadwalDetail']);

        Route::post('/sesi/aktifkan', [DosenController::class, 'aktifkanSesi']);
        Route::post('/sesi/{id}/hentikan', [DosenController::class, 'hentikanSesi']);
        Route::put('/sesi/{id}/hentikan', [DosenController::class, 'hentikanSesi']);
        Route::get('/sesi/{jadwal_id}/presensi', [DosenController::class, 'getPresensiRealtime']);
        
        Route::get('/riwayat/jadwal/{jadwal_id}', [DosenController::class, 'getRiwayatSesi']);
        Route::get('/riwayat/jadwal/{jadwal_id}/pertemuan/{pertemuan_ke}', [DosenController::class, 'getPresensiSesi']);
        Route::post('/presensi/manual', [DosenController::class, 'updatePresensiManual']);
        
        Route::get('/izin', [DosenController::class, 'getIzin']);
        Route::put('/izin/{id}/status', [DosenController::class, 'updateStatusIzin']);
    });

    // Modul Mahasiswa
    Route::middleware('role:mahasiswa')->prefix('mahasiswa')->group(function () {
        Route::get('/jadwal', [MahasiswaController::class, 'getJadwal']);
        Route::get('/jadwal/hari-ini', [MahasiswaController::class, 'getJadwalHariIni']);
        Route::get('/jadwal/{id}/detail', [MahasiswaController::class, 'getJadwalDetail']);
        Route::get('/presensi/riwayat', [MahasiswaController::class, 'getRiwayatPresensi']);
        Route::post('/presensi/pindai', [MahasiswaController::class, 'pindaiPresensi']);
        Route::post('/izin/ajukan', [MahasiswaController::class, 'ajukanIzin']);
        Route::delete('/izin/{id}/batal', [MahasiswaController::class, 'batalIzin']);

        Route::get('/matakuliah', [MahasiswaController::class, 'getMatakuliahPeminatan']);
        Route::post('/peminatan', [MahasiswaController::class, 'ajukanPeminatan']);
        Route::get('/peminatan', [MahasiswaController::class, 'getRiwayatPeminatan']);
        Route::delete('/peminatan/{id}', [MahasiswaController::class, 'batalPeminatan']);
    });
});
