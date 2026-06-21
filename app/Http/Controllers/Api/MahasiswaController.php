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
        $today = $request->query('tanggal', now()->toDateString());
        $hari_ini = \Carbon\Carbon::parse($today)->locale('id')->isoFormat('dddd');
        
        // Ambil ID matakuliah yang sudah disetujui untuk mahasiswa ini
        $matakuliahDisetujuiIds = Peminatan::where('mahasiswa_id', $request->user()->id)
            ->where('status', 'disetujui')
            ->pluck('matakuliah_id');

        $allJadwal = Jadwal::with(['matakuliah', 'ruangan', 'dosen', 'sesiAktif'])
            ->whereIn('matakuliah_id', $matakuliahDisetujuiIds)
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

    /**
     * Get All Student Schedules
     */
    public function getJadwal(Request $request)
    {
        // Ambil ID matakuliah yang sudah disetujui untuk mahasiswa ini
        $matakuliahDisetujuiIds = Peminatan::where('mahasiswa_id', $request->user()->id)
            ->where('status', 'disetujui')
            ->pluck('matakuliah_id');

        $jadwal = Jadwal::with(['matakuliah', 'ruangan', 'dosen', 'sesiAktif'])
            ->whereIn('matakuliah_id', $matakuliahDisetujuiIds)
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
        $presensi = Presensi::with(['mahasiswa', 'jadwal.matakuliah', 'jadwal.ruangan', 'jadwal.dosen'])
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
            'lat' => 'sometimes|numeric',
            'lng' => 'sometimes|numeric',
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
        $jarak = null;

        // Hanya cek jarak jika metode kelas adalah luring
        if ($jadwal->metode === 'luring') {
            if (!$request->has('lat') || !$request->has('lng')) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Lokasi GPS Anda diperlukan untuk presensi kelas luring.',
                ], 400);
            }

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
            'lat_scan' => $jadwal->metode === 'luring' ? $request->lat : null,
            'lng_scan' => $jadwal->metode === 'luring' ? $request->lng : null,
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
            'lampiran' => 'required|image|max:10240', // 10MB max
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

    public function getRiwayatPeminatan(Request $request)
    {
        $peminatan = Peminatan::with('matakuliah')
            ->where('mahasiswa_id', $request->user()->id)
            ->where('status', 'menunggu')
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $peminatan
        ]);
    }

    public function batalPeminatan(Request $request, $id)
    {
        $peminatan = Peminatan::where('mahasiswa_id', $request->user()->id)
            ->where('status', 'menunggu')
            ->findOrFail($id);
            
        $peminatan->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Pengajuan peminatan berhasil dibatalkan',
        ]);
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

    public function getJadwalDetail(Request $request, $id)
    {
        // Pastikan mahasiswa disetujui mengambil matakuliah jadwal ini
        $matakuliahDisetujuiIds = Peminatan::where('mahasiswa_id', $request->user()->id)
            ->where('status', 'disetujui')
            ->pluck('matakuliah_id');

        $jadwal = Jadwal::with(['matakuliah', 'ruangan', 'dosen', 'sesiAktif'])
            ->whereIn('matakuliah_id', $matakuliahDisetujuiIds)
            ->findOrFail($id);

        $listPertemuan = [];
        $today = now()->toDateString();

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
            
            // Cari data presensi mahasiswa untuk pertemuan ini
            $presensi = Presensi::where('jadwal_id', $jadwal->id)
                ->where('mahasiswa_id', $request->user()->id)
                ->where('pertemuan_ke', $p)
                ->first();

            $status = 'belum_dimulai';
            $jam_masuk = null;

            if ($presensi) {
                $status = $presensi->status;
                $jam_masuk = $presensi->jam_masuk;
            } else {
                // Cek apakah pertemuan sudah lewat
                if ($tanggal < $today) {
                    $status = 'alpa';
                }
            }

            // Apakah sesi ini sedang aktif?
            $isSesiAktif = false;
            $tokenQr = null;
            if ($sesiJadwal && $sesiJadwal->is_aktif && \Carbon\Carbon::parse($sesiJadwal->berakhir_pada)->isFuture()) {
                $isSesiAktif = true;
                $tokenQr = $sesiJadwal->token_qr;
            }

            $listPertemuan[] = [
                'pertemuan_ke' => $p,
                'label' => $label,
                'tanggal' => $tanggal,
                'jam_mulai' => $jamMulai,
                'jam_selesai' => $jamSelesai,
                'ruangan_nama' => $ruanganNama,
                'status' => $status,
                'jam_masuk' => $jam_masuk,
                'is_sesi_aktif' => $isSesiAktif,
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
