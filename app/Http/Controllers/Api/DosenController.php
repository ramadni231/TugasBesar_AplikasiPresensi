<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Jadwal;
use App\Models\SesiAktif;
use App\Models\Presensi;
use App\Models\Izin;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class DosenController extends Controller
{
    /**
     * Get Teaching Schedules
     */
    public function getJadwal(Request $request)
    {
        $jadwal = Jadwal::with(['matakuliah', 'ruangan'])
            ->where('dosen_id', $request->user()->id)
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $jadwal
        ]);
    }

    public function getKelasAktifHariIni(Request $request)
    {
        $hari_ini = now()->locale('id')->isoFormat('dddd'); // e.g. Senin, Selasa
        $jadwal = Jadwal::with(['matakuliah', 'ruangan'])
            ->where('dosen_id', $request->user()->id)
            ->where('hari', $hari_ini)
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $jadwal
        ]);
    }

    public function rescheduleJadwal(Request $request, $id)
    {
        $jadwal = Jadwal::where('dosen_id', $request->user()->id)->findOrFail($id);

        $data = $request->validate([
            'hari' => 'required|string',
            'jam_mulai' => 'required',
            'jam_selesai' => 'required',
            'ruangan_id' => 'sometimes|exists:ruangan,id',
        ]);

        $jadwal->update($data);

        return response()->json([
            'status' => 'success',
            'message' => 'Jadwal berhasil di-reschedule',
            'data' => $jadwal
        ]);
    }

    /**
     * Activate Session (Buka Presensi)
     */
    public function aktifkanSesi(Request $request)
    {
        $request->validate([
            'jadwal_id' => 'required|exists:jadwal,id',
            'pertemuan_ke' => 'required|integer',
        ]);

        // Pastikan jadwal ini milik dosen yang login
        Jadwal::where('dosen_id', $request->user()->id)->findOrFail($request->jadwal_id);

        // Nonaktifkan sesi sebelumnya untuk jadwal ini
        SesiAktif::where('jadwal_id', $request->jadwal_id)->update(['is_aktif' => false]);

        $sesi = SesiAktif::create([
            'jadwal_id' => $request->jadwal_id,
            'pertemuan_ke' => $request->pertemuan_ke,
            'token_qr' => Str::random(32),
            'berakhir_pada' => now()->addMinutes(15),
            'is_aktif' => true,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Sesi diaktifkan. QR Code berlaku 15 menit.',
            'data' => $sesi
        ]);
    }

    /**
     * Stop Session
     */
    public function hentikanSesi($id)
    {
        $sesi = SesiAktif::findOrFail($id);
        $sesi->update(['is_aktif' => false]);

        return response()->json([
            'status' => 'success',
            'message' => 'Sesi berhasil dihentikan.',
        ]);
    }

    /**
     * Get Real-time Attendances
     */
    public function getPresensiRealtime($jadwal_id)
    {
        $presensi = Presensi::with('mahasiswa')
            ->where('jadwal_id', $jadwal_id)
            ->where('tanggal', now()->toDateString())
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $presensi
        ]);
    }

    /**
     * Get Leaves for Validation
     */
    public function getIzin(Request $request)
    {
        $izin = Izin::with('pengguna')
            ->where('status_persetujuan', 'menunggu')
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $izin
        ]);
    }

    /**
     * Update Leave Status
     */
    public function updateStatusIzin(Request $request, $id)
    {
        $request->validate([
            'status_persetujuan' => 'required|in:disetujui,ditolak',
        ]);

        $izin = Izin::findOrFail($id);
        $izin->update([
            'status_persetujuan' => $request->status_persetujuan,
            'disetujui_oleh' => $request->user()->id,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Status izin diperbarui menjadi ' . $request->status_persetujuan,
        ]);
    }
}
