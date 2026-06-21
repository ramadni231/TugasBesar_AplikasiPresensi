<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Jadwal;
use App\Models\SesiAktif;
use App\Models\Presensi;
use App\Models\Izin;
use App\Models\Pengaturan;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class DosenController extends Controller
{
    public function dashboard()
    {
        $today = now()->toDateString();
        $hari_ini = \Carbon\Carbon::parse($today)->locale('id')->isoFormat('dddd');
        
        $allJadwal = Jadwal::with(['matakuliah', 'ruangan', 'dosen', 'sesiAktif'])
            ->where('dosen_id', Auth::id())
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
            
        return view('dosen.dashboard', compact('jadwalHariIni'));
    }
    
    public function jadwalDetail(Request $request, $id)
    {
        $jadwal = Jadwal::with(['matakuliah', 'ruangan', 'dosen', 'sesiAktif'])
            ->where('dosen_id', Auth::id())
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
            
            $jumlahHadir = Presensi::where('jadwal_id', $jadwal->id)->where('pertemuan_ke', $p)->where('status', 'hadir')->count();
            $jumlahSakit = Presensi::where('jadwal_id', $jadwal->id)->where('pertemuan_ke', $p)->where('status', 'sakit')->count();
            $jumlahIzin = Presensi::where('jadwal_id', $jadwal->id)->where('pertemuan_ke', $p)->where('status', 'izin')->count();
            $jumlahAlpa = Presensi::where('jadwal_id', $jadwal->id)->where('pertemuan_ke', $p)->where('status', 'alpa')->count();

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

        return view('dosen.jadwal_detail', compact('jadwal', 'listPertemuan'));
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

    public function jadwal()
    {
        $jadwal = Jadwal::with(['matakuliah', 'ruangan'])
            ->where('dosen_id', Auth::id())
            ->get();

        return view('dosen.jadwal', compact('jadwal'));
    }

    public function aktifkanSesi(Request $request)
    {
        $request->validate([
            'jadwal_id' => 'required|exists:jadwal,id',
            'pertemuan_ke' => 'required|integer',
        ]);

        $jadwal = Jadwal::where('dosen_id', Auth::id())->findOrFail($request->jadwal_id);

        SesiAktif::where('jadwal_id', $request->jadwal_id)->update(['is_aktif' => false]);

        $sesi = SesiAktif::where('jadwal_id', $request->jadwal_id)
            ->where('pertemuan_ke', $request->pertemuan_ke)
            ->first();

        $jam_selesai = ($sesi && $sesi->jam_selesai_reschedule) 
            ? $sesi->jam_selesai_reschedule 
            : $jadwal->jam_selesai;

        $tanggal_hari_ini = ($sesi && $sesi->tanggal_reschedule) 
            ? $sesi->tanggal_reschedule 
            : now()->toDateString();

        if ($sesi) {
            $sesi->update([
                'token_qr' => \Illuminate\Support\Str::random(32),
                'berakhir_pada' => $tanggal_hari_ini . ' ' . $jam_selesai,
                'is_aktif' => true,
            ]);
        } else {
            SesiAktif::create([
                'jadwal_id' => $request->jadwal_id,
                'pertemuan_ke' => $request->pertemuan_ke,
                'token_qr' => \Illuminate\Support\Str::random(32),
                'berakhir_pada' => $tanggal_hari_ini . ' ' . $jam_selesai,
                'is_aktif' => true,
            ]);
        }

        return back()->with('success', 'Sesi diaktifkan. QR Code berlaku hingga kelas selesai.');
    }

    public function hentikanSesi($id)
    {
        $sesi = SesiAktif::findOrFail($id);
        $sesi->update([
            'is_aktif' => false,
            'berakhir_pada' => \Carbon\Carbon::now(),
        ]);

        return back()->with('success', 'Sesi presensi berhasil dihentikan.');
    }

    public function rekap()
    {
        // Hanya tampilkan jadwal yang diajar oleh Dosen ini
        $jadwal = Jadwal::with(['matakuliah', 'ruangan'])->where('dosen_id', Auth::id())->get();
        return view('dosen.rekap', compact('jadwal'));
    }

    public function rekapDetail($jadwal_id)
    {
        $jadwal = Jadwal::with(['matakuliah', 'ruangan'])->where('dosen_id', Auth::id())->findOrFail($jadwal_id);
        
        $semuaPresensi = \App\Models\Presensi::where('jadwal_id', $jadwal_id)
            ->whereNotIn('pertemuan_ke', [8, 16])
            ->get();
            
        $totalPertemuan = $semuaPresensi->groupBy(function($item) {
            return $item->created_at->format('Y-m-d');
        })->count();

        $presensi = \App\Models\Presensi::with('mahasiswa')
            ->where('jadwal_id', $jadwal_id)
            ->get()
            ->groupBy('mahasiswa_id');
            
        // Ambil semua mahasiswa yang terdaftar di kelas
        $mahasiswaIds = \App\Models\Peminatan::where('matakuliah_id', $jadwal->matakuliah_id)->where('status', 'disetujui')->pluck('mahasiswa_id');
        if($mahasiswaIds->isEmpty()) {
             $mahasiswaIds = $presensi->keys(); // Fallback
        }
        $mahasiswaList = \App\Models\Pengguna::whereIn('id', $mahasiswaIds)->get();
        
        $rekapData = [];
        foreach ($mahasiswaList as $mhs) {
            $kehadiran = $presensi[$mhs->id] ?? collect();
            
            // Filter hanya pertemuan reguler (1-7 dan 9-15), kecualikan UTS(8) dan UAS(16)
            $kehadiranReguler = $kehadiran->filter(function($item) {
                return !in_array($item->pertemuan_ke, [8, 16]);
            });

            $kehadiranUnik = $kehadiranReguler->unique(function($item) {
                return $item->created_at->format('Y-m-d');
            });

            $rekapData[] = [
                'nama' => $mhs->nama,
                'nomor_identitas' => $mhs->nomor_identitas,
                'total_hadir' => $kehadiranUnik->where('status', 'hadir')->count(),
                'total_izin' => $kehadiranUnik->where('status', 'izin')->count(),
                'total_sakit' => $kehadiranUnik->where('status', 'sakit')->count(),
                'total_pertemuan' => $totalPertemuan // Hanya menghitung pertemuan reguler yg telah berjalan
            ];
        }

        return view('dosen.rekap_detail', compact('jadwal', 'rekapData', 'totalPertemuan'));
    }

    public function exportRekapExcel($jadwal_id)
    {
        $jadwal = Jadwal::with(['matakuliah', 'ruangan'])->where('dosen_id', Auth::id())->findOrFail($jadwal_id);
        
        $semuaPresensi = \App\Models\Presensi::where('jadwal_id', $jadwal_id)->get();
        $totalPertemuan = $semuaPresensi->groupBy(function($item) {
            return $item->created_at->format('Y-m-d');
        })->count();

        $presensi = \App\Models\Presensi::with('mahasiswa')
            ->where('jadwal_id', $jadwal_id)
            ->get()
            ->groupBy('mahasiswa_id');
            
        $mahasiswaIds = \App\Models\Peminatan::where('matakuliah_id', $jadwal->matakuliah_id)->where('status', 'disetujui')->pluck('mahasiswa_id');
        if($mahasiswaIds->isEmpty()) {
             $mahasiswaIds = $presensi->keys();
        }
        $mahasiswaList = \App\Models\Pengguna::whereIn('id', $mahasiswaIds)->get();

        $filename = "Rekap_Presensi_{$jadwal->matakuliah->kode_matkul}_" . date('Ymd') . ".csv";
        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $callback = function() use($mahasiswaList, $presensi, $totalPertemuan) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['No', 'NIM', 'Nama Mahasiswa', 'Hadir', 'Izin', 'Sakit', 'Alpa', 'Total Pertemuan Efektif', 'Persentase']);

            $no = 1;
            foreach ($mahasiswaList as $mhs) {
                $kehadiran = $presensi[$mhs->id] ?? collect();
                
                $kehadiranReguler = $kehadiran->filter(function($item) {
                    return !in_array($item->pertemuan_ke, [8, 16]);
                });

                $kehadiranUnik = $kehadiranReguler->unique(function($item) {
                    return $item->created_at->format('Y-m-d');
                });
                
                $hadir = $kehadiranUnik->where('status', 'hadir')->count();
                $izin = $kehadiranUnik->where('status', 'izin')->count();
                $sakit = $kehadiranUnik->where('status', 'sakit')->count();
                
                $totalKehadiran = $hadir + $izin + $sakit;
                $alpa = max(0, $totalPertemuan - $totalKehadiran);
                
                // Persentase kehadiran SELALU dari total 14 pertemuan efektif
                $persentase = round(($hadir / 14) * 100);

                fputcsv($file, [
                    $no++,
                    $mhs->nomor_identitas,
                    $mhs->nama,
                    $hadir,
                    $izin,
                    $sakit,
                    $alpa,
                    $totalPertemuan,
                    $persentase . '%'
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
