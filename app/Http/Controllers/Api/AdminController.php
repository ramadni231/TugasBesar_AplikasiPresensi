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
use App\Models\SesiAktif;
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
            'semester' => 'required|integer',
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
            'semester' => 'sometimes|integer',
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
        $jadwal = Jadwal::with(['matakuliah', 'ruangan', 'dosen', 'sesiAktif'])->get();
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
            'metode' => 'sometimes|in:luring,daring',
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

        $request->validate([
            'tipe' => 'sometimes|in:satu_pertemuan,selamanya',
            // fields for satu_pertemuan:
            'pertemuan_ke' => 'required_if:tipe,satu_pertemuan|integer|min:1|max:16',
            'tanggal_reschedule' => 'required_if:tipe,satu_pertemuan|date',
            'jam_mulai_reschedule' => 'required_if:tipe,satu_pertemuan',
            'jam_selesai_reschedule' => 'required_if:tipe,satu_pertemuan',
            'ruangan_id_reschedule' => 'nullable|exists:ruangan,id',
            // fields for selamanya:
            'matakuliah_id' => 'sometimes|exists:matakuliah,id',
            'ruangan_id' => 'sometimes|exists:ruangan,id',
            'dosen_id' => 'sometimes|exists:pengguna,id',
            'hari' => 'sometimes|string',
            'jam_mulai' => 'sometimes',
            'jam_selesai' => 'sometimes',
            'metode' => 'sometimes|in:luring,daring',
        ]);

        $tipe = $request->input('tipe', 'selamanya');

        if ($tipe === 'satu_pertemuan') {
            $sesi = SesiAktif::updateOrCreate(
                [
                    'jadwal_id' => $jadwal->id,
                    'pertemuan_ke' => $request->pertemuan_ke,
                ],
                [
                    'tanggal_reschedule' => $request->tanggal_reschedule,
                    'jam_mulai_reschedule' => $request->jam_mulai_reschedule,
                    'jam_selesai_reschedule' => $request->jam_selesai_reschedule,
                    'ruangan_id_reschedule' => $request->ruangan_id_reschedule,
                ]
            );

            return response()->json([
                'status' => 'success',
                'message' => 'Reschedule pertemuan berhasil disimpan',
                'data' => $sesi
            ]);
        } else {
            $data = $request->only([
                'matakuliah_id',
                'ruangan_id',
                'dosen_id',
                'hari',
                'jam_mulai',
                'jam_selesai',
                'metode',
            ]);

            $jadwal->update($data);

            return response()->json([
                'status' => 'success',
                'message' => 'Jadwal berhasil diperbarui',
                'data' => $jadwal
            ]);
        }
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

        if ($request->status_persetujuan === 'disetujui') {
            // Find all classes (Jadwal) where the student is enrolled
            $matakuliahDisetujuiIds = \App\Models\Peminatan::where('mahasiswa_id', $izin->pengguna_id)
                ->where('status', 'disetujui')
                ->pluck('matakuliah_id');

            $jadwals = \App\Models\Jadwal::whereIn('matakuliah_id', $matakuliahDisetujuiIds)->get();

            foreach ($jadwals as $jadwal) {
                for ($p = 1; $p <= 16; $p++) {
                    $sesiJadwal = \App\Models\SesiAktif::where('jadwal_id', $jadwal->id)
                        ->where('pertemuan_ke', $p)
                        ->first();

                    $tanggalPertemuan = ($sesiJadwal && $sesiJadwal->tanggal_reschedule) 
                        ? $sesiJadwal->tanggal_reschedule 
                        : $this->hitungTanggalPertemuan($jadwal->hari, $p);

                    if ($tanggalPertemuan === $izin->tanggal) {
                        \App\Models\Presensi::updateOrCreate(
                            [
                                'jadwal_id' => $jadwal->id,
                                'mahasiswa_id' => $izin->pengguna_id,
                                'pertemuan_ke' => $p,
                            ],
                            [
                                'status' => $izin->tipe_izin,
                                'jam_masuk' => now()->toTimeString(),
                            ]
                        );
                    }
                }
            }
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Status izin berhasil diperbarui',
        ]);
    }

    // --- PENGATURAN & PEMINATAN ---
    public function getMasaPeminatanStatus()
    {
        $pengaturan = Pengaturan::where('kunci', 'is_masa_peminatan')->first();
        $isAktif = $pengaturan ? $pengaturan->nilai === 'true' : false;

        return response()->json([
            'status' => 'success',
            'data' => [
                'is_aktif' => $isAktif,
            ]
        ]);
    }

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

    public function getTanggalMulaiSemester()
    {
        $pengaturan = Pengaturan::where('kunci', 'tanggal_mulai_semester')->first();
        $tanggal = $pengaturan ? $pengaturan->nilai : '2026-02-23';

        return response()->json([
            'status' => 'success',
            'data' => [
                'nilai' => $tanggal,
            ]
        ]);
    }

    public function setTanggalMulaiSemester(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
        ]);

        $pengaturan = Pengaturan::updateOrCreate(
            ['kunci' => 'tanggal_mulai_semester'],
            ['nilai' => $request->tanggal]
        );

        return response()->json([
            'status' => 'success',
            'message' => 'Tanggal mulai semester berhasil diatur',
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

    public function getJadwalRekap(Request $request, $id)
    {
        $jadwal = Jadwal::with(['matakuliah', 'ruangan', 'dosen'])->findOrFail($id);

        $today = now()->toDateString();

        // Ambil semua mahasiswa yang disetujui peminatannya untuk matakuliah ini
        $peminatans = Peminatan::with('mahasiswa')
            ->where('matakuliah_id', $jadwal->matakuliah_id)
            ->where('status', 'disetujui')
            ->get();

        // Buat detail untuk 16 pertemuan
        $listPertemuanInfo = [];
        for ($p = 1; $p <= 16; $p++) {
            $sesiJadwal = \App\Models\SesiAktif::where('jadwal_id', $jadwal->id)
                ->where('pertemuan_ke', $p)
                ->first();

            $tanggal = ($sesiJadwal && $sesiJadwal->tanggal_reschedule) 
                ? $sesiJadwal->tanggal_reschedule 
                : $this->hitungTanggalPertemuan($jadwal->hari, $p);

            $listPertemuanInfo[$p] = [
                'pertemuan_ke' => $p,
                'label' => $this->dapatkanLabelPertemuan($p),
                'tanggal' => $tanggal,
            ];
        }

        $rekapMahasiswa = [];

        foreach ($peminatans as $peminatan) {
            $mahasiswa = $peminatan->mahasiswa;
            if (!$mahasiswa) {
                continue;
            }

            $kehadiran = [];
            $totalHadir = 0;
            $totalSakit = 0;
            $totalIzin = 0;
            $totalAlpa = 0;

            for ($p = 1; $p <= 16; $p++) {
                $tanggal = $listPertemuanInfo[$p]['tanggal'];
                
                $presensi = \App\Models\Presensi::where('jadwal_id', $jadwal->id)
                    ->where('mahasiswa_id', $mahasiswa->id)
                    ->where('pertemuan_ke', $p)
                    ->first();

                $status = 'belum_dimulai';
                if ($presensi) {
                    $status = $presensi->status;
                } else {
                    if ($tanggal < $today) {
                        $status = 'alpa';
                    }
                }

                if ($status === 'hadir') {
                    $totalHadir++;
                } elseif ($status === 'sakit') {
                    $totalSakit++;
                } elseif ($status === 'izin') {
                    $totalIzin++;
                } elseif ($status === 'alpa') {
                    $totalAlpa++;
                }

                $kehadiran[$p] = [
                    'pertemuan_ke' => $p,
                    'status' => $status,
                ];
            }

            $persentase = 16 > 0 ? round(($totalHadir / 16) * 100, 2) : 0;

            $rekapMahasiswa[] = [
                'mahasiswa' => $mahasiswa,
                'kehadiran' => array_values($kehadiran),
                'ringkasan' => [
                    'hadir' => $totalHadir,
                    'sakit' => $totalSakit,
                    'izin' => $totalIzin,
                    'alpa' => $totalAlpa,
                    'persentase' => $persentase,
                ],
            ];
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'jadwal' => $jadwal,
                'pertemuan' => array_values($listPertemuanInfo),
                'rekap' => $rekapMahasiswa,
            ]
        ]);
    }

    private function hitungTanggalPertemuan($hari, $pertemuan_ke)
    {
        $pengaturan = Pengaturan::where('kunci', 'tanggal_mulai_semester')->first();
        $baseDateString = $pengaturan ? $pengaturan->nilai : '2026-02-23';
        $baseDate = \Carbon\Carbon::parse($baseDateString);

        $startDayOfWeek = $baseDate->dayOfWeek;
        $targetDayOfWeek = [
            'Senin' => 1,
            'Selasa' => 2,
            'Rabu' => 3,
            'Kamis' => 4,
            'Jumat' => 5,
            'Sabtu' => 6,
            'Minggu' => 0,
        ][$hari] ?? 1;

        $dayDiff = $targetDayOfWeek - $startDayOfWeek;
        if ($dayDiff < 0) {
            $dayDiff += 7;
        }
        $firstMeetingDate = $baseDate->copy()->addDays($dayDiff);
        return $firstMeetingDate->addWeeks($pertemuan_ke - 1)->toDateString();
    }

    private function dapatkanLabelPertemuan($pertemuan_ke)
    {
        if ($pertemuan_ke >= 1 && $pertemuan_ke <= 7) {
            return "Pertemuan " . $pertemuan_ke;
        } elseif ($pertemuan_ke == 8) {
            return "UTS";
        } elseif ($pertemuan_ke >= 9 && $pertemuan_ke <= 15) {
            return "Pertemuan " . ($pertemuan_ke - 1);
        } elseif ($pertemuan_ke == 16) {
            return "UAS";
        }
        return "Pertemuan " . $pertemuan_ke;
    }
}
