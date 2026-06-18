<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Jadwal;
use App\Models\SesiAktif;
use App\Models\Presensi;
use App\Models\Izin;
use App\Models\Matakuliah;
use App\Models\Pengaturan;
use App\Models\Peminatan;
use Illuminate\Http\Request;

class MahasiswaController extends Controller
{
    /**
     * Get Today's Schedules
     */
    public function getJadwalHariIni(Request $request)
    {
        $hari_ini = now()->locale('id')->isoFormat('dddd'); // Senin, Selasa
        $jadwal = Jadwal::with(['matakuliah', 'ruangan', 'dosen'])
            ->where('hari', $hari_ini)
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $jadwal
        ]);
    }

    /**
     * Get Attendance History
     */
    public function getRiwayatPresensi(Request $request)
    {
        $presensi = Presensi::with(['jadwal.matakuliah', 'jadwal.dosen'])
            ->where('mahasiswa_id', $request->user()->id)
            ->orderBy('tanggal', 'desc')
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $presensi
        ]);
    }

    /**
     * Scan Presence (CORE LOGIC)
     */
    public function pindaiPresensi(Request $request)
    {
        $request->validate([
            'token_qr' => 'required',
            'lat' => 'required|numeric',
            'lng' => 'required|numeric',
        ]);

        $sesi = SesiAktif::where('token_qr', $request->token_qr)
            ->where('is_aktif', true)
            ->where('berakhir_pada', '>', now())
            ->first();

        if (!$sesi) {
            return response()->json([
                'status' => 'error',
                'message' => 'QR Code tidak valid atau sudah kedaluwarsa.',
            ], 400);
        }

        $jadwal = $sesi->jadwal;
        $ruangan = $jadwal->ruangan;

        // Haversine Formula Logic
        $jarak = $this->hitungJarak(
            $request->lat, $request->lng,
            $ruangan->latitude, $ruangan->longitude
        );

        if ($jarak > $ruangan->radius_meter) {
            return response()->json([
                'status' => 'error',
                'message' => 'Posisi Anda terlalu jauh dari ruangan kelas. Jarak: ' . round($jarak) . 'm',
                'jarak_meter' => round($jarak)
            ], 403);
        }

        // Cek jika sudah absen
        $sudahAbsen = Presensi::where('jadwal_id', $jadwal->id)
            ->where('mahasiswa_id', $request->user()->id)
            ->where('pertemuan_ke', $sesi->pertemuan_ke)
            ->exists();

        if ($sudahAbsen) {
            return response()->json([
                'status' => 'error',
                'message' => 'Anda sudah melakukan presensi untuk pertemuan ini.',
            ], 400);
        }

        // Catat Presensi
        $presensi = Presensi::create([
            'jadwal_id' => $jadwal->id,
            'pertemuan_ke' => $sesi->pertemuan_ke,
            'mahasiswa_id' => $request->user()->id,
            'tanggal' => now()->toDateString(),
            'jam_masuk' => now()->toTimeString(),
            'status' => 'hadir',
            'lat_scan' => $request->lat,
            'lng_scan' => $request->lng,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Presensi berhasil dicatat.',
            'jarak_meter' => round($jarak),
            'data' => $presensi
        ], 201);
    }

    public function batalIzin(Request $request, $id)
    {
        $izin = Izin::where('pengguna_id', $request->user()->id)
            ->where('status_persetujuan', 'menunggu')
            ->findOrFail($id);
            
        $izin->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Pengajuan izin berhasil dibatalkan',
        ]);
    }

    /**
     * Submit Leave
     */
    public function ajukanIzin(Request $request)
    {
        $request->validate([
            'tipe_izin' => 'required|in:sakit,izin',
            'tanggal' => 'required|date',
            'alasan' => 'required|string',
            'lampiran' => 'required|image|max:2048', // 2MB max
        ]);

        $path = $request->file('lampiran')->store('izin', 'public');

        $izin = Izin::create([
            'pengguna_id' => $request->user()->id,
            'tipe_izin' => $request->tipe_izin,
            'tanggal' => $request->tanggal,
            'alasan' => $request->alasan,
            'jalur_lampiran' => $path,
            'status_persetujuan' => 'menunggu',
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Pengajuan izin berhasil dikirim.',
            'data' => $izin
        ], 201);
    }

    // --- PEMINATAN MATAKULIAH ---
    public function getMatakuliahPeminatan(Request $request)
    {
        $pengaturan = Pengaturan::where('kunci', 'is_masa_peminatan')->first();
        if (!$pengaturan || $pengaturan->nilai !== 'true') {
            return response()->json([
                'status' => 'error',
                'message' => 'Masa peminatan sedang ditutup.',
            ], 403);
        }

        $semester = $request->query('semester');
        $matakuliah = Matakuliah::when($semester, fn($q) => $q->where('semester', $semester))->get();
        
        return response()->json([
            'status' => 'success',
            'data' => $matakuliah
        ]);
    }

    public function ajukanPeminatan(Request $request)
    {
        $pengaturan = Pengaturan::where('kunci', 'is_masa_peminatan')->first();
        if (!$pengaturan || $pengaturan->nilai !== 'true') {
            return response()->json([
                'status' => 'error',
                'message' => 'Masa peminatan sedang ditutup.',
            ], 403);
        }

        $request->validate([
            'matakuliah_id' => 'required|exists:matakuliah,id',
        ]);

        $sudahAda = Peminatan::where('mahasiswa_id', $request->user()->id)
            ->where('matakuliah_id', $request->matakuliah_id)
            ->exists();

        if ($sudahAda) {
            return response()->json([
                'status' => 'error',
                'message' => 'Anda sudah memilih matakuliah ini.',
            ], 400);
        }

        $peminatan = Peminatan::create([
            'mahasiswa_id' => $request->user()->id,
            'matakuliah_id' => $request->matakuliah_id,
            'status' => 'menunggu',
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Matakuliah berhasil ditambahkan ke peminatan.',
            'data' => $peminatan
        ], 201);
    }

    /**
     * Helper: Haversine Distance Calculation
     */
    private function hitungJarak($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371000; // meters

        $latDelta = deg2rad($lat2 - $lat1);
        $lonDelta = deg2rad($lon2 - $lon1);

        $a = sin($latDelta / 2) * sin($latDelta / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($lonDelta / 2) * sin($lonDelta / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }
}
