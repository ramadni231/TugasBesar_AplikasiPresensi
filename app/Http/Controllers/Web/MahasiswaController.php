<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Jadwal;
use App\Models\SesiAktif;
use App\Models\Presensi;
use App\Models\Izin;
use App\Models\Matakuliah;
use App\Models\Pengaturan;
use App\Models\Peminatan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MahasiswaController extends Controller
{
    public function dashboard()
    {
        $today = now()->toDateString();
        $hari_ini = \Carbon\Carbon::parse($today)->locale('id')->isoFormat('dddd');
        
        $matakuliahDisetujuiIds = \App\Models\Peminatan::where('mahasiswa_id', Auth::id())
            ->where('status', 'disetujui')
            ->pluck('matakuliah_id');

        $allJadwal = Jadwal::with(['matakuliah', 'ruangan', 'dosen', 'sesiAktif'])
            ->whereIn('matakuliah_id', $matakuliahDisetujuiIds)
            ->get();

        $jadwalHariIni = [];

        foreach ($allJadwal as $j) {
            $hasMeetingToday = false;
            $overrideSesi = null;

            $sesiRescheduleHariIni = \App\Models\SesiAktif::with('ruanganReschedule')
                ->where('jadwal_id', $j->id)
                ->where('tanggal_reschedule', $today)
                ->first();

            if ($sesiRescheduleHariIni) {
                $hasMeetingToday = true;
                $overrideSesi = $sesiRescheduleHariIni;
            } else {
                if (strtolower($j->hari) === strtolower($hari_ini)) {
                    for ($p = 1; $p <= 16; $p++) {
                        if ($this->hitungTanggalPertemuan($j->hari, $p) === $today) {
                            $sesiLain = \App\Models\SesiAktif::where('jadwal_id', $j->id)
                                ->where('pertemuan_ke', $p)
                                ->first();
                            
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
                    $j->setRelation('sesiAktif', $overrideSesi);
                }
                $jadwalHariIni[] = $j;
            }
        }
            
        return view('mahasiswa.dashboard', compact('jadwalHariIni'));
    }
        public function riwayatDetail(Request $request, $id)
    {
        $matakuliahDisetujuiIds = Peminatan::where('mahasiswa_id', Auth::id())
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
            
            $presensi = Presensi::where('jadwal_id', $jadwal->id)
                ->where('mahasiswa_id', Auth::id())
                ->where('pertemuan_ke', $p)
                ->first();

            $status = 'belum_dimulai';
            $jam_masuk = null;

            if ($presensi) {
                $status = $presensi->status;
                $jam_masuk = $presensi->jam_masuk;
            } else {
                if ($tanggal < $today) {
                    $status = 'alpa';
                }
            }

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

        return view('mahasiswa.riwayat_detail', compact('jadwal', 'listPertemuan'));
    }

    private function hitungTanggalPertemuan($hari, $pertemuan_ke)
    {
        $pengaturan = Pengaturan::where('kunci', 'tanggal_mulai_semester')->first();
        $baseDateString = $pengaturan ? $pengaturan->nilai : '2026-02-23';
        $baseDate = \Carbon\Carbon::parse($baseDateString);

        $startDayOfWeek = $baseDate->dayOfWeek;
        $targetDayOfWeek = [
            'Senin' => 1, 'Selasa' => 2, 'Rabu' => 3, 'Kamis' => 4,
            'Jumat' => 5, 'Sabtu' => 6, 'Minggu' => 0,
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
        if ($pertemuan_ke >= 1 && $pertemuan_ke <= 7) return "Pertemuan " . $pertemuan_ke;
        if ($pertemuan_ke == 8) return "UTS";
        if ($pertemuan_ke >= 9 && $pertemuan_ke <= 15) return "Pertemuan " . ($pertemuan_ke - 1);
        if ($pertemuan_ke == 16) return "UAS";
        return "Pertemuan " . $pertemuan_ke;
    }
    public function riwayat()
    {
        $matakuliahDisetujuiIds = Peminatan::where('mahasiswa_id', Auth::id())
            ->where('status', 'disetujui')
            ->pluck('matakuliah_id');

        $jadwal = Jadwal::with(['matakuliah', 'ruangan', 'dosen'])
            ->whereIn('matakuliah_id', $matakuliahDisetujuiIds)
            ->get();

        return view('mahasiswa.riwayat', compact('jadwal'));
    }

    public function pindai()
    {
        return view('mahasiswa.pindai');
    }

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
            return back()->withErrors(['token_qr' => 'QR Code tidak valid atau sudah kedaluwarsa.']);
        }

        $jadwal = $sesi->jadwal;
        $ruangan = $jadwal->ruangan;
        $jarak = 0;

        if ($jadwal->metode === 'luring') {
            $jarak = $this->hitungJarak(
                $request->lat, $request->lng,
                $ruangan->latitude, $ruangan->longitude
            );

            if ($jarak > $ruangan->radius_meter) {
                return back()->withErrors(['lokasi' => 'Posisi Anda terlalu jauh dari ruangan kelas. Jarak: ' . round($jarak) . 'm. Presensi Luring gagal.']);
            }
        }

        $sudahAbsen = Presensi::where('jadwal_id', $jadwal->id)
            ->where('mahasiswa_id', Auth::id())
            ->where('pertemuan_ke', $sesi->pertemuan_ke)
            ->exists();

        if ($sudahAbsen) {
            return back()->withErrors(['presensi' => 'Anda sudah melakukan presensi untuk pertemuan ini.']);
        }

        Presensi::create([
            'jadwal_id' => $jadwal->id,
            'pertemuan_ke' => $sesi->pertemuan_ke,
            'mahasiswa_id' => Auth::id(),
            'tanggal' => now()->toDateString(),
            'jam_masuk' => now()->toTimeString(),
            'status' => 'hadir',
            'lat_scan' => $request->lat,
            'lng_scan' => $request->lng,
        ]);

        return redirect()->route('mahasiswa.riwayat')->with('success', 'Presensi berhasil dicatat. Jarak: ' . round($jarak) . 'm');
    }

    public function izin()
    {
        $riwayatIzin = Izin::where('pengguna_id', Auth::id())->orderBy('created_at', 'desc')->get();
        return view('mahasiswa.izin', compact('riwayatIzin'));
    }

    public function ajukanIzin(Request $request)
    {
        $request->validate([
            'tipe_izin' => 'required|in:sakit,izin',
            'tanggal' => 'required|date',
            'alasan' => 'required|string',
            'lampiran' => 'required|image|max:2048',
        ]);

        $path = $request->file('lampiran')->store('izin', 'public');

        Izin::create([
            'pengguna_id' => Auth::id(),
            'tipe_izin' => $request->tipe_izin,
            'tanggal' => $request->tanggal,
            'alasan' => $request->alasan,
            'jalur_lampiran' => $path,
            'status_persetujuan' => 'menunggu',
        ]);

        return back()->with('success', 'Pengajuan izin berhasil dikirim.');
    }

    public function batalIzin($id)
    {
        $izin = Izin::where('id', $id)->where('pengguna_id', Auth::id())->firstOrFail();
        
        if ($izin->status_persetujuan !== 'menunggu') {
            return back()->with('error', 'Izin yang sudah diproses tidak dapat dibatalkan.');
        }

        if ($izin->jalur_lampiran && \Storage::disk('public')->exists($izin->jalur_lampiran)) {
            \Storage::disk('public')->delete($izin->jalur_lampiran);
        }

        $izin->delete();

        return back()->with('success', 'Pengajuan izin berhasil dibatalkan');
    }

    public function peminatan()
    {
        // Ambil semua matakuliah
        $matakuliah = Matakuliah::all();
        
        // Ambil peminatan/KRS mahasiswa ini
        $peminatan = Peminatan::with('matakuliah')
            ->where('mahasiswa_id', Auth::id())
            ->get();
            
        return view('mahasiswa.peminatan', compact('matakuliah', 'peminatan'));
    }

    public function storePeminatan(Request $request)
    {
        $request->validate([
            'matakuliah_id' => 'required|exists:matakuliah,id'
        ]);

        // Cek apakah sudah ambil
        $exists = Peminatan::where('mahasiswa_id', Auth::id())
            ->where('matakuliah_id', $request->matakuliah_id)
            ->exists();

        if ($exists) {
            return back()->with('error', 'Mata kuliah ini sudah ada di KRS Anda.');
        }

        Peminatan::create([
            'mahasiswa_id' => Auth::id(),
            'matakuliah_id' => $request->matakuliah_id,
            'status' => 'menunggu' // menunggu persetujuan admin/dosen
        ]);

        return back()->with('success', 'Mata kuliah berhasil ditambahkan ke KRS (Menunggu Persetujuan).');
    }

    public function destroyPeminatan($id)
    {
        $peminatan = Peminatan::where('id', $id)
            ->where('mahasiswa_id', Auth::id())
            ->firstOrFail();

        $peminatan->delete();

        return back()->with('success', 'Mata kuliah dibatalkan dari KRS.');
    }

    public function semuaJadwal()
    {
        // Ambil jadwal yang disetujui (opsional: atau semua jadwal dari matkul disetujui)
        $matakuliahDisetujuiIds = Peminatan::where('mahasiswa_id', Auth::id())
            ->where('status', 'disetujui')
            ->pluck('matakuliah_id');

        $jadwal = Jadwal::with(['matakuliah', 'ruangan', 'dosen'])
            ->whereIn('matakuliah_id', $matakuliahDisetujuiIds)
            ->get();

        return view('mahasiswa.semua_jadwal', compact('jadwal'));
    }

    private function hitungJarak($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371000;

        $latDelta = deg2rad($lat2 - $lat1);
        $lonDelta = deg2rad($lon2 - $lon1);

        $a = sin($latDelta / 2) * sin($latDelta / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($lonDelta / 2) * sin($lonDelta / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }
}
