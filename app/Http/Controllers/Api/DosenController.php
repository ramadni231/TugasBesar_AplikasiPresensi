<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Jadwal;
use App\Models\SesiAktif;
use App\Models\Presensi;
use App\Models\Izin;
use App\Models\Pengaturan;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class DosenController extends Controller
{
    /**
     * Get Teaching Schedules
     */
    public function getJadwal(Request $request)
    {
        $jadwal = Jadwal::with(['matakuliah', 'ruangan', 'dosen', 'sesiAktif'])
            ->withCount(['presensi' => function ($query) {
                $query->where('tanggal', now()->toDateString());
            }])
            ->where('dosen_id', $request->user()->id)
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $jadwal
        ]);
    }

    public function getKelasAktifHariIni(Request $request)
    {
        $today = $request->query('tanggal', now()->toDateString());
        $hari_ini = \Carbon\Carbon::parse($today)->locale('id')->isoFormat('dddd');
        
        $allJadwal = Jadwal::with(['matakuliah', 'ruangan', 'dosen', 'sesiAktif'])
            ->withCount(['presensi' => function ($query) use ($today) {
                $query->where('tanggal', $today);
            }])
            ->where('dosen_id', $request->user()->id)
            ->get();

        $filteredJadwal = [];

        foreach ($allJadwal as $j) {
            $hasMeetingToday = false;
            $overrideSesi = null;

            // 1. Cek apakah ada sesi reschedule untuk jadwal ini hari ini
            $sesiRescheduleHariIni = SesiAktif::with('ruanganReschedule')
                ->where('jadwal_id', $j->id)
                ->where('tanggal_reschedule', $today)
                ->first();

            if ($sesiRescheduleHariIni) {
                $hasMeetingToday = true;
                $overrideSesi = $sesiRescheduleHariIni;
            } else {
                // 2. Cek apakah hari ini regular day dan tidak ada reschedule keluar
                if (strtolower($j->hari) === strtolower($hari_ini)) {
                    // Cari index pertemuan hari ini
                    for ($p = 1; $p <= 16; $p++) {
                        if ($this->hitungTanggalPertemuan($j->hari, $p) === $today) {
                            $sesiLain = SesiAktif::where('jadwal_id', $j->id)
                                ->where('pertemuan_ke', $p)
                                ->first();
                            
                            // Jika tidak ada reschedule ke tanggal lain
                            if (!$sesiLain || !$sesiLain->tanggal_reschedule || $sesiLain->tanggal_reschedule === $today) {
                                $hasMeetingToday = true;
                                if ($sesiLain) {
                                    $overrideSesi = $sesiLain;
                                }
                            }
                            break;
                        }
                    }
                }
            }

            if ($hasMeetingToday) {
                if ($overrideSesi) {
                    if ($overrideSesi->jam_mulai_reschedule) {
                        $j->jam_mulai = $overrideSesi->jam_mulai_reschedule;
                    }
                    if ($overrideSesi->jam_selesai_reschedule) {
                        $j->jam_selesai = $overrideSesi->jam_selesai_reschedule;
                    }
                    if ($overrideSesi->ruangan_id_reschedule && $overrideSesi->ruanganReschedule) {
                        $j->ruangan = $overrideSesi->ruanganReschedule;
                    }
                    // Hubungkan sesi reschedule/aktif
                    $j->setRelation('sesiAktif', $overrideSesi);
                }
                $filteredJadwal[] = $j;
            }
        }

        return response()->json([
            'status' => 'success',
            'data' => $filteredJadwal
        ]);
    }

    public function rescheduleJadwal(Request $request, $id)
    {
        $jadwal = Jadwal::where('dosen_id', $request->user()->id)->findOrFail($id);

        $request->validate([
            'tipe' => 'required|in:satu_pertemuan,selamanya',
            // fields for satu_pertemuan:
            'pertemuan_ke' => 'required_if:tipe,satu_pertemuan|integer|min:1|max:16',
            'tanggal_reschedule' => 'required_if:tipe,satu_pertemuan|date',
            'jam_mulai_reschedule' => 'required_if:tipe,satu_pertemuan',
            'jam_selesai_reschedule' => 'required_if:tipe,satu_pertemuan',
            'ruangan_id_reschedule' => 'nullable|exists:ruangan,id',
            // fields for selamanya:
            'hari' => 'required_if:tipe,selamanya|string',
            'jam_mulai' => 'required_if:tipe,selamanya',
            'jam_selesai' => 'required_if:tipe,selamanya',
            'ruangan_id' => 'nullable|exists:ruangan,id',
        ]);

        if ($request->tipe === 'satu_pertemuan') {
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
            $jadwal->update([
                'hari' => $request->hari,
                'jam_mulai' => $request->jam_mulai,
                'jam_selesai' => $request->jam_selesai,
                'ruangan_id' => $request->ruangan_id ?? $jadwal->ruangan_id,
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Jadwal permanen berhasil di-reschedule',
                'data' => $jadwal
            ]);
        }
    }

    /**
     * Activate Session (Buka Presensi)
     */
    public function aktifkanSesi(Request $request)
    {
        $request->validate([
            'jadwal_id' => 'required|exists:jadwal,id',
            'pertemuan_ke' => 'sometimes|integer',
        ]);

        // Pastikan jadwal ini milik dosen yang login
        $jadwal = Jadwal::where('dosen_id', $request->user()->id)->findOrFail($request->jadwal_id);

        // Hitung pertemuan_ke jika tidak dikirim
        $pertemuan_ke = $request->pertemuan_ke;
        if (!$pertemuan_ke) {
            $maxPertemuan = SesiAktif::where('jadwal_id', $request->jadwal_id)->max('pertemuan_ke');
            $pertemuan_ke = $maxPertemuan ? $maxPertemuan + 1 : 1;
        }

        // Nonaktifkan sesi sebelumnya untuk jadwal ini
        SesiAktif::where('jadwal_id', $request->jadwal_id)->update(['is_aktif' => false]);

        // Cek apakah sudah ada rekaman sesi reschedule untuk pertemuan ini
        $sesi = SesiAktif::where('jadwal_id', $request->jadwal_id)
            ->where('pertemuan_ke', $pertemuan_ke)
            ->first();

        // Tentukan jam selesai kelas (gunakan reschedule jika ada)
        $jam_selesai = ($sesi && $sesi->jam_selesai_reschedule) 
            ? $sesi->jam_selesai_reschedule 
            : $jadwal->jam_selesai;

        // Tentukan tanggal berakhir (gunakan tanggal reschedule jika ada)
        $tanggal_hari_ini = ($sesi && $sesi->tanggal_reschedule) 
            ? $sesi->tanggal_reschedule 
            : now()->toDateString();

        if ($sesi) {
            $sesi->update([
                'token_qr' => Str::random(32),
                'berakhir_pada' => $tanggal_hari_ini . ' ' . $jam_selesai,
                'is_aktif' => true,
            ]);
        } else {
            $sesi = SesiAktif::create([
                'jadwal_id' => $request->jadwal_id,
                'pertemuan_ke' => $pertemuan_ke,
                'token_qr' => Str::random(32),
                'berakhir_pada' => $tanggal_hari_ini . ' ' . $jam_selesai,
                'is_aktif' => true,
            ]);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Sesi diaktifkan. QR Code berlaku hingga kelas selesai.',
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
        $presensi = Presensi::with(['mahasiswa', 'jadwal.matakuliah', 'jadwal.ruangan', 'jadwal.dosen'])
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

        if ($request->status_persetujuan === 'disetujui') {
            // Find all classes (Jadwal) where the student is enrolled
            $matakuliahDisetujuiIds = \App\Models\Peminatan::where('mahasiswa_id', $izin->pengguna_id)
                ->where('status', 'disetujui')
                ->pluck('matakuliah_id');

            $jadwals = \App\Models\Jadwal::whereIn('matakuliah_id', $matakuliahDisetujuiIds)->get();

            foreach ($jadwals as $jadwal) {
                for ($p = 1; $p <= 16; $p++) {
                    $sesiJadwal = SesiAktif::where('jadwal_id', $jadwal->id)
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
            'message' => 'Status izin diperbarui menjadi ' . $request->status_persetujuan,
        ]);
    }

    /**
     * Get unique active/history sessions for a schedule
     */
    public function getRiwayatSesi($jadwal_id)
    {
        $riwayat = SesiAktif::where('jadwal_id', $jadwal_id)
            ->orderBy('pertemuan_ke', 'desc')
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $riwayat
        ]);
    }

    /**
     * Get attendance details for a specific session/pertemuan (including absent students)
     */
    public function getPresensiSesi($jadwal_id, $pertemuan_ke)
    {
        $jadwal = Jadwal::with(['matakuliah', 'ruangan', 'dosen'])->findOrFail($jadwal_id);
        $today = now()->toDateString();

        // Ambil semua mahasiswa yang disetujui peminatannya untuk matakuliah ini
        $peminatans = \App\Models\Peminatan::with('mahasiswa')
            ->where('matakuliah_id', $jadwal->matakuliah_id)
            ->where('status', 'disetujui')
            ->get();

        $sesiJadwal = SesiAktif::where('jadwal_id', $jadwal->id)
            ->where('pertemuan_ke', $pertemuan_ke)
            ->first();

        $tanggal = ($sesiJadwal && $sesiJadwal->tanggal_reschedule) 
            ? $sesiJadwal->tanggal_reschedule 
            : $this->hitungTanggalPertemuan($jadwal->hari, $pertemuan_ke);

        $dataPresensi = [];

        foreach ($peminatans as $peminatan) {
            $mahasiswa = $peminatan->mahasiswa;
            if (!$mahasiswa) {
                continue;
            }

            $presensi = Presensi::where('jadwal_id', $jadwal_id)
                ->where('mahasiswa_id', $mahasiswa->id)
                ->where('pertemuan_ke', $pertemuan_ke)
                ->first();

            if ($presensi) {
                $dataPresensi[] = [
                    'id' => $presensi->id,
                    'jadwal' => $jadwal,
                    'mahasiswa' => $mahasiswa,
                    'tanggal' => $presensi->tanggal,
                    'jam_masuk' => $presensi->jam_masuk,
                    'status' => $presensi->status,
                    'lat_scan' => $presensi->lat_scan,
                    'lng_scan' => $presensi->lng_scan,
                ];
            } else {
                $statusDefault = 'belum_dimulai';
                if ($tanggal < $today) {
                    $statusDefault = 'alpa';
                }

                $dataPresensi[] = [
                    'id' => -$mahasiswa->id,
                    'jadwal' => $jadwal,
                    'mahasiswa' => $mahasiswa,
                    'tanggal' => $tanggal,
                    'jam_masuk' => null,
                    'status' => $statusDefault,
                    'lat_scan' => null,
                    'lng_scan' => null,
                ];
            }
        }

        return response()->json([
            'status' => 'success',
            'data' => $dataPresensi
        ]);
    }

    /**
     * Update attendance manually by Lecturer
     */
    public function updatePresensiManual(Request $request)
    {
        $request->validate([
            'jadwal_id' => 'required|exists:jadwal,id',
            'pertemuan_ke' => 'required|integer|between:1,16',
            'mahasiswa_id' => 'required|exists:pengguna,id',
            'status' => 'required|in:hadir,sakit,izin,alpa,belum_dimulai',
        ]);

        // Pastikan jadwal ini milik dosen yang login
        $jadwal = Jadwal::where('dosen_id', $request->user()->id)->findOrFail($request->jadwal_id);

        if ($request->status === 'belum_dimulai') {
            Presensi::where('jadwal_id', $request->jadwal_id)
                ->where('pertemuan_ke', $request->pertemuan_ke)
                ->where('mahasiswa_id', $request->mahasiswa_id)
                ->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Status presensi berhasil direset',
            ]);
        }

        $sesiJadwal = SesiAktif::where('jadwal_id', $request->jadwal_id)
            ->where('pertemuan_ke', $request->pertemuan_ke)
            ->first();

        $tanggal = ($sesiJadwal && $sesiJadwal->tanggal_reschedule)
            ? $sesiJadwal->tanggal_reschedule
            : $this->hitungTanggalPertemuan($jadwal->hari, $request->pertemuan_ke);

        $presensi = Presensi::updateOrCreate(
            [
                'jadwal_id' => $request->jadwal_id,
                'pertemuan_ke' => $request->pertemuan_ke,
                'mahasiswa_id' => $request->mahasiswa_id,
            ],
            [
                'tanggal' => $tanggal,
                'jam_masuk' => now()->toTimeString(),
                'status' => $request->status,
            ]
        );

        return response()->json([
            'status' => 'success',
            'message' => 'Status presensi berhasil diperbarui',
            'data' => $presensi->load('mahasiswa')
        ]);
    }

    public function getJadwalDetail(Request $request, $id)
    {
        $jadwal = Jadwal::with(['matakuliah', 'ruangan', 'dosen', 'sesiAktif'])
            ->where('dosen_id', $request->user()->id)
            ->findOrFail($id);

        $listPertemuan = [];
        $today = now()->toDateString();
        
        $totalMahasiswa = \App\Models\Peminatan::where('matakuliah_id', $jadwal->matakuliah_id)
            ->where('status', 'disetujui')
            ->count();

        for ($p = 1; $p <= 16; $p++) {
            $sesiJadwal = SesiAktif::with('ruanganReschedule')
                ->where('jadwal_id', $jadwal->id)
                ->where('pertemuan_ke', $p)
                ->first();

            $tanggal = ($sesiJadwal && $sesiJadwal->tanggal_reschedule) 
                ? $sesiJadwal->tanggal_reschedule 
                : $this->hitungTanggalPertemuan($jadwal->hari, $p);

            $jamMulai = ($sesiJadwal && $sesiJadwal->jam_mulai_reschedule)
                ? substr($sesiJadwal->jam_mulai_reschedule, 0, 5)
                : substr($jadwal->jam_mulai, 0, 5);

            $jamSelesai = ($sesiJadwal && $sesiJadwal->jam_selesai_reschedule)
                ? substr($sesiJadwal->jam_selesai_reschedule, 0, 5)
                : substr($jadwal->jam_selesai, 0, 5);

            $ruanganNama = ($sesiJadwal && $sesiJadwal->ruangan_id_reschedule && $sesiJadwal->ruanganReschedule)
                ? $sesiJadwal->ruanganReschedule->nama_ruangan
                : $jadwal->ruangan->nama_ruangan;

            $label = $this->dapatkanLabelPertemuan($p);
            
            // Hitung data presensi mahasiswa untuk pertemuan ini
            $jumlahHadir = Presensi::where('jadwal_id', $jadwal->id)
                ->where('pertemuan_ke', $p)
                ->where('status', 'hadir')
                ->count();
                
            $jumlahSakit = Presensi::where('jadwal_id', $jadwal->id)
                ->where('pertemuan_ke', $p)
                ->where('status', 'sakit')
                ->count();

            $jumlahIzin = Presensi::where('jadwal_id', $jadwal->id)
                ->where('pertemuan_ke', $p)
                ->where('status', 'izin')
                ->count();
                
            $jumlahAlpa = Presensi::where('jadwal_id', $jadwal->id)
                ->where('pertemuan_ke', $p)
                ->where('status', 'alpa')
                ->count();

            // Apakah sesi ini sedang aktif?
            $isSesiAktif = false;
            $tokenQr = null;
            $sesiAktifId = null;
            if ($sesiJadwal && $sesiJadwal->is_aktif && \Carbon\Carbon::parse($sesiJadwal->berakhir_pada)->isFuture()) {
                $isSesiAktif = true;
                $tokenQr = $sesiJadwal->token_qr;
                $sesiAktifId = $sesiJadwal->id;
            }

            $listPertemuan[] = [
                'pertemuan_ke' => $p,
                'label' => $label,
                'tanggal' => $tanggal,
                'jam_mulai' => $jamMulai,
                'jam_selesai' => $jamSelesai,
                'ruangan_nama' => $ruanganNama,
                'total_mahasiswa' => $totalMahasiswa,
                'jumlah_hadir' => $jumlahHadir,
                'jumlah_sakit' => $jumlahSakit,
                'jumlah_izin' => $jumlahIzin,
                'jumlah_alpa' => $jumlahAlpa,
                'is_sesi_aktif' => $isSesiAktif,
                'sesi_aktif_id' => $sesiAktifId,
                'token_qr' => $tokenQr,
            ];
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'jadwal' => $jadwal,
                'pertemuan' => $listPertemuan
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
