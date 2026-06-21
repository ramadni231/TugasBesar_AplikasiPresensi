<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\AuthController;

Route::get('/', function () {
    return redirect('/login');
});

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware(['auth'])->group(function () {
    Route::get('/profil', [AuthController::class, 'showProfil'])->name('profil.index');
    Route::post('/profil', [AuthController::class, 'updateProfil'])->name('profil.update');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/profile', [App\Http\Controllers\Web\ProfileController::class, 'index'])->name('profile');
    Route::put('/profile/password', [App\Http\Controllers\Web\ProfileController::class, 'updatePassword'])->name('profile.updatePassword');

    Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\Web\AdminController::class, 'dashboard'])->name('dashboard');
    Route::post('/set-awal-semester', [\App\Http\Controllers\Web\AdminController::class, 'setAwalSemester'])->name('setAwalSemester');
    
    // Manajemen Pengguna
    Route::get('/pengguna', [\App\Http\Controllers\Web\AdminController::class, 'pengguna'])->name('pengguna.index');
    Route::post('/pengguna', [\App\Http\Controllers\Web\AdminController::class, 'storePengguna'])->name('pengguna.store');
    Route::put('/pengguna/{id}', [\App\Http\Controllers\Web\AdminController::class, 'updatePengguna'])->name('pengguna.update');
    Route::delete('/pengguna/{id}', [\App\Http\Controllers\Web\AdminController::class, 'destroyPengguna'])->name('pengguna.destroy');

    // Manajemen Ruangan
    Route::get('/ruangan', [\App\Http\Controllers\Web\AdminController::class, 'ruangan'])->name('ruangan.index');
    Route::post('/ruangan', [\App\Http\Controllers\Web\AdminController::class, 'storeRuangan'])->name('ruangan.store');
    Route::put('/ruangan/{id}', [\App\Http\Controllers\Web\AdminController::class, 'updateRuangan'])->name('ruangan.update');
    Route::delete('/ruangan/{id}', [\App\Http\Controllers\Web\AdminController::class, 'destroyRuangan'])->name('ruangan.destroy');

    // Manajemen Matakuliah
    Route::get('/matakuliah', [\App\Http\Controllers\Web\AdminController::class, 'matakuliah'])->name('matakuliah.index');
    Route::post('/matakuliah', [\App\Http\Controllers\Web\AdminController::class, 'storeMatakuliah'])->name('matakuliah.store');
    Route::put('/matakuliah/{id}', [\App\Http\Controllers\Web\AdminController::class, 'updateMatakuliah'])->name('matakuliah.update');
    Route::delete('/matakuliah/{id}', [\App\Http\Controllers\Web\AdminController::class, 'destroyMatakuliah'])->name('matakuliah.destroy');

    // Manajemen Jadwal
    Route::get('/jadwal', [\App\Http\Controllers\Web\AdminController::class, 'jadwal'])->name('jadwal.index');
    Route::post('/jadwal', [\App\Http\Controllers\Web\AdminController::class, 'storeJadwal'])->name('jadwal.store');
    Route::put('/jadwal/{id}', [\App\Http\Controllers\Web\AdminController::class, 'updateJadwal'])->name('jadwal.update');
    Route::delete('/jadwal/{id}', [\App\Http\Controllers\Web\AdminController::class, 'destroyJadwal'])->name('jadwal.destroy');
    Route::get('/jadwal/{id}/detail', [\App\Http\Controllers\Web\AdminController::class, 'jadwalDetail'])->name('jadwal.detail');
    Route::post('/jadwal/{id}/reschedule', [\App\Http\Controllers\Web\AdminController::class, 'reschedulePertemuan'])->name('jadwal.reschedule');
    Route::get('/pengaturan', [\App\Http\Controllers\Web\AdminController::class, 'pengaturan'])->name('pengaturan');
    Route::post('/set-awal-semester', [\App\Http\Controllers\Web\AdminController::class, 'setAwalSemester'])->name('setAwalSemester');

    // Rekap Presensi
    Route::get('/rekap', [\App\Http\Controllers\Web\AdminController::class, 'rekap'])->name('rekap.index');
    Route::get('/rekap/{jadwal_id}', [\App\Http\Controllers\Web\AdminController::class, 'getRekapDetail'])->name('rekap.detail');
    Route::get('/rekap/{jadwal_id}/export', [\App\Http\Controllers\Web\AdminController::class, 'exportRekapExcel'])->name('rekap.export');

    // Manajemen Peminatan
    Route::get('/peminatan', [\App\Http\Controllers\Web\AdminController::class, 'peminatan'])->name('peminatan.index');
    Route::get('/peminatan/{mahasiswa_id}', [\App\Http\Controllers\Web\AdminController::class, 'peminatanDetail'])->name('peminatan.detail');
    Route::post('/peminatan/{id}/status', [\App\Http\Controllers\Web\AdminController::class, 'updateStatusPeminatan'])->name('peminatan.updateStatus');
    Route::post('/peminatan/toggle', [\App\Http\Controllers\Web\AdminController::class, 'toggleMasaPeminatan'])->name('peminatan.toggle');

    });
});

// Dosen routes
Route::middleware('auth')->prefix('dosen')->name('dosen.')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\Web\DosenController::class, 'dashboard'])->name('dashboard');
    Route::get('/jadwal', [\App\Http\Controllers\Web\DosenController::class, 'jadwal'])->name('jadwal');
    Route::get('/jadwal/{id}', [\App\Http\Controllers\Web\DosenController::class, 'jadwalDetail'])->name('jadwal.detail');
    Route::post('/sesi/aktifkan', [\App\Http\Controllers\Web\DosenController::class, 'aktifkanSesi'])->name('sesi.aktifkan');
    Route::post('/sesi/{id}/hentikan', [\App\Http\Controllers\Web\DosenController::class, 'hentikanSesi'])->name('sesi.hentikan');
    Route::get('/rekap', [\App\Http\Controllers\Web\DosenController::class, 'rekap'])->name('rekap');
    Route::get('/rekap/{id}', [\App\Http\Controllers\Web\DosenController::class, 'rekapDetail'])->name('rekap.detail');
    Route::get('/rekap/{id}/export', [\App\Http\Controllers\Web\DosenController::class, 'exportRekapExcel'])->name('rekap.export');

});

// Mahasiswa routes
Route::middleware('auth')->prefix('mahasiswa')->name('mahasiswa.')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\Web\MahasiswaController::class, 'dashboard'])->name('dashboard');
    Route::get('/riwayat', [\App\Http\Controllers\Web\MahasiswaController::class, 'riwayat'])->name('riwayat');
    Route::get('/riwayat/{id}', [\App\Http\Controllers\Web\MahasiswaController::class, 'riwayatDetail'])->name('riwayat.detail');
    Route::get('/pindai', [\App\Http\Controllers\Web\MahasiswaController::class, 'pindai'])->name('pindai');
    Route::post('/pindai', [\App\Http\Controllers\Web\MahasiswaController::class, 'pindaiPresensi'])->name('pindai.presensi');
    Route::get('/peminatan', [\App\Http\Controllers\Web\MahasiswaController::class, 'peminatan'])->name('peminatan');
    Route::post('/peminatan', [\App\Http\Controllers\Web\MahasiswaController::class, 'storePeminatan'])->name('peminatan.store');
    Route::delete('/peminatan/{id}', [\App\Http\Controllers\Web\MahasiswaController::class, 'destroyPeminatan'])->name('peminatan.destroy');
    Route::get('/semua-jadwal', [\App\Http\Controllers\Web\MahasiswaController::class, 'semuaJadwal'])->name('semua_jadwal');

});
