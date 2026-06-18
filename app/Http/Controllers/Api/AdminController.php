<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pengguna;
use App\Models\Ruangan;
use App\Models\Matakuliah;
use App\Models\Jadwal;
use App\Models\Izin;
use App\Models\Pengaturan;
use App\Models\Peminatan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    // --- MANAJEMEN PENGGUNA ---
    public function getPengguna(Request $request)
    {
        $peran = $request->query('peran');
        $pengguna = Pengguna::when($peran, fn($q) => $peran == 'non-admin' ? $q->where('peran', '!=', 'admin') : $q->where('peran', $peran))->get();
        
        return response()->json([
            'status' => 'success',
            'data' => $pengguna
        ]);
    }

    public function storePengguna(Request $request)
    {
        $data = $request->validate([
            'nama' => 'required|string',
            'nomor_identitas' => 'required|string|unique:pengguna',
            'email' => 'required|email|unique:pengguna',
            'password' => 'required|min:6',
            'peran' => 'required|in:admin,dosen,mahasiswa',
        ]);

        $data['password'] = Hash::make($data['password']);
        $pengguna = Pengguna::create($data);

        return response()->json([
            'status' => 'success',
            'message' => 'Pengguna berhasil dibuat',
            'data' => $pengguna
        ], 201);
    }

    public function updatePengguna(Request $request, $id)
    {
        $pengguna = Pengguna::findOrFail($id);

        $data = $request->validate([
            'nama' => 'sometimes|string',
            'nomor_identitas' => 'sometimes|string|unique:pengguna,nomor_identitas,'.$id,
            'email' => 'sometimes|email|unique:pengguna,email,'.$id,
            'password' => 'sometimes|min:6',
            'peran' => 'sometimes|in:admin,dosen,mahasiswa',
        ]);

        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        $pengguna->update($data);

        return response()->json([
            'status' => 'success',
            'message' => 'Pengguna berhasil diperbarui',
            'data' => $pengguna
        ]);
    }

    public function destroyPengguna($id)
    {
        $pengguna = Pengguna::findOrFail($id);
        $pengguna->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Pengguna berhasil dihapus',
        ]);
    }

    // --- MANAJEMEN RUANGAN ---
    public function getRuangan()
    {
        return response()->json([
            'status' => 'success',
            'data' => Ruangan::all()
        ]);
    }

    public function storeRuangan(Request $request)
    {
        $data = $request->validate([
            'nama_ruangan' => 'required|string',
            'kapasitas' => 'required|integer',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'radius_meter' => 'required|integer',
        ]);

        $ruangan = Ruangan::create($data);

        return response()->json([
            'status' => 'success',
            'message' => 'Ruangan berhasil dibuat',
            'data' => $ruangan
        ], 201);
    }

    public function updateRuangan(Request $request, $id)
    {
        $ruangan = Ruangan::findOrFail($id);

        $data = $request->validate([
            'nama_ruangan' => 'sometimes|string',
            'kapasitas' => 'sometimes|integer',
            'latitude' => 'sometimes|numeric',
            'longitude' => 'sometimes|numeric',
            'radius_meter' => 'sometimes|integer',
        ]);

        $ruangan->update($data);

        return response()->json([
            'status' => 'success',
            'message' => 'Ruangan berhasil diperbarui',
            'data' => $ruangan
        ]);
    }

    public function destroyRuangan($id)
    {
        $ruangan = Ruangan::findOrFail($id);
        $ruangan->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Ruangan berhasil dihapus',
        ]);
    }

    // --- MANAJEMEN MATAKULIAH ---
    public function getMatakuliah()
    {
        return response()->json([
            'status' => 'success',
            'data' => Matakuliah::all()
        ]);
    }

    public function storeMatakuliah(Request $request)
    {
        $data = $request->validate([
            'kode_matkul' => 'required|string|unique:matakuliah',
            'nama_matkul' => 'required|string',
            'sks' => 'required|integer',
        ]);

        $matkul = Matakuliah::create($data);

        return response()->json([
            'status' => 'success',
            'message' => 'Matakuliah berhasil dibuat',
            'data' => $matkul
        ], 201);
    }

    public function updateMatakuliah(Request $request, $id)
    {
        $matkul = Matakuliah::findOrFail($id);

        $data = $request->validate([
            'kode_matkul' => 'sometimes|string|unique:matakuliah,kode_matkul,'.$id,
            'nama_matkul' => 'sometimes|string',
            'sks' => 'sometimes|integer',
        ]);

        $matkul->update($data);

        return response()->json([
            'status' => 'success',
            'message' => 'Matakuliah berhasil diperbarui',
            'data' => $matkul
        ]);
    }

    public function destroyMatakuliah($id)
    {
        $matkul = Matakuliah::findOrFail($id);
        $matkul->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Matakuliah berhasil dihapus',
        ]);
    }

    // --- MANAJEMEN JADWAL ---
    public function getJadwal()
    {
        $jadwal = Jadwal::with(['matakuliah', 'ruangan', 'dosen'])->get();
        return response()->json([
            'status' => 'success',
            'data' => $jadwal
        ]);
    }

    public function storeJadwal(Request $request)
    {
        $data = $request->validate([
            'matakuliah_id' => 'required|exists:matakuliah,id',
            'ruangan_id' => 'required|exists:ruangan,id',
            'dosen_id' => 'required|exists:pengguna,id',
            'hari' => 'required|string',
            'jam_mulai' => 'required',
            'jam_selesai' => 'required',
        ]);

        $jadwal = Jadwal::create($data);

        return response()->json([
            'status' => 'success',
            'message' => 'Jadwal berhasil dibuat',
            'data' => $jadwal
        ], 201);
    }

    public function updateJadwal(Request $request, $id)
    {
        $jadwal = Jadwal::findOrFail($id);

        $data = $request->validate([
            'matakuliah_id' => 'sometimes|exists:matakuliah,id',
            'ruangan_id' => 'sometimes|exists:ruangan,id',
            'dosen_id' => 'sometimes|exists:pengguna,id',
            'hari' => 'sometimes|string',
            'jam_mulai' => 'sometimes',
            'jam_selesai' => 'sometimes',
        ]);

        $jadwal->update($data);

        return response()->json([
            'status' => 'success',
            'message' => 'Jadwal berhasil diperbarui',
            'data' => $jadwal
        ]);
    }

    public function destroyJadwal($id)
    {
        $jadwal = Jadwal::findOrFail($id);
        $jadwal->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Jadwal berhasil dihapus',
        ]);
    }

    public function getIzin()
    {
        return response()->json([
            'status' => 'success',
            'data' => Izin::with('pengguna')->get()
        ]);
    }

    public function updateIzinStatus(Request $request, $id)
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
            'message' => 'Status izin berhasil diperbarui',
        ]);
    }

    // --- PENGATURAN & PEMINATAN ---
    public function toggleMasaPeminatan(Request $request)
    {
        $request->validate([
            'is_aktif' => 'required|boolean',
        ]);

        $pengaturan = Pengaturan::updateOrCreate(
            ['kunci' => 'is_masa_peminatan'],
            ['nilai' => $request->is_aktif ? 'true' : 'false']
        );

        return response()->json([
            'status' => 'success',
            'message' => 'Masa peminatan ' . ($request->is_aktif ? 'diaktifkan' : 'dinonaktifkan'),
            'data' => $pengaturan
        ]);
    }

    public function getKelolaPeminatan(Request $request)
    {
        $peminatan = Peminatan::with(['mahasiswa', 'matakuliah'])->get();
        return response()->json([
            'status' => 'success',
            'data' => $peminatan
        ]);
    }

    public function updateStatusPeminatan(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:disetujui,ditolak',
        ]);

        $peminatan = Peminatan::findOrFail($id);
        $peminatan->update(['status' => $request->status]);

        return response()->json([
            'status' => 'success',
            'message' => 'Status peminatan berhasil diperbarui',
        ]);
    }
}
