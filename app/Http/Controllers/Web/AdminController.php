<?php

namespace App\Http\Controllers\Web;

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
    // --- DASHBOARD ---
    public function dashboard()
    {
        $totalPengguna = Pengguna::count();
        $totalDosen = Pengguna::where('peran', 'dosen')->count();
        $totalMahasiswa = Pengguna::where('peran', 'mahasiswa')->count();
        $totalRuangan = Ruangan::count();
        $totalMatkul = Matakuliah::count();
        $totalJadwal = Jadwal::count();
        $awalSemester = \App\Models\Pengaturan::where('kunci', 'awal_semester')->value('nilai');

        return view('admin.dashboard', compact(
            'totalPengguna', 'totalDosen', 'totalMahasiswa',
            'totalRuangan', 'totalMatkul', 'totalJadwal', 'awalSemester'
        ));
    }
    
    public function pengaturan()
    {
        $awalSemester = \App\Models\Pengaturan::where('kunci', 'awal_semester')->value('nilai');
        return view('admin.pengaturan', compact('awalSemester'));
    }
    public function setAwalSemester(Request $request)
    {
        $request->validate(['tanggal' => 'required|date']);
        \App\Models\Pengaturan::updateOrCreate(
            ['kunci' => 'awal_semester'],
            ['nilai' => $request->tanggal]
        );
        return back()->with('success', 'Tanggal awal semester berhasil diperbarui.');
    }

    // --- MANAJEMEN PENGGUNA ---
    public function pengguna(Request $request)
    {
        $peran = $request->query('peran');
        $pengguna = Pengguna::when($peran, fn($q) => $peran == 'non-admin' ? $q->where('peran', '!=', 'admin') : $q->where('peran', $peran))->get();
        return view('admin.pengguna', compact('pengguna'));
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
        Pengguna::create($data);

        return back()->with('success', 'Pengguna berhasil dibuat');
    }

    public function updatePengguna(Request $request, $id)
    {
        $pengguna = Pengguna::findOrFail($id);

        $data = $request->validate([
            'nama' => 'sometimes|string',
            'nomor_identitas' => 'sometimes|string|unique:pengguna,nomor_identitas,'.$id,
            'email' => 'sometimes|email|unique:pengguna,email,'.$id,
            'password' => 'nullable|min:6',
            'peran' => 'sometimes|in:admin,dosen,mahasiswa',
        ]);

        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $pengguna->update($data);

        return back()->with('success', 'Pengguna berhasil diperbarui');
    }

    public function destroyPengguna($id)
    {
        $pengguna = Pengguna::findOrFail($id);
        $pengguna->delete();
        return back()->with('success', 'Pengguna berhasil dihapus');
    }

    // --- MANAJEMEN RUANGAN ---
    public function ruangan()
    {
        $ruangan = Ruangan::all();
        return view('admin.ruangan', compact('ruangan'));
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

        Ruangan::create($data);
        return back()->with('success', 'Ruangan berhasil dibuat');
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
        return back()->with('success', 'Ruangan berhasil diperbarui');
    }

    public function destroyRuangan($id)
    {
        $ruangan = Ruangan::findOrFail($id);
        $ruangan->delete();
        return back()->with('success', 'Ruangan berhasil dihapus');
    }

    // --- MANAJEMEN MATAKULIAH ---
    public function matakuliah()
    {
        $matakuliah = Matakuliah::all();
        return view('admin.matakuliah', compact('matakuliah'));
    }

    public function storeMatakuliah(Request $request)
    {
        $data = $request->validate([
            'kode_matkul' => 'required|string|unique:matakuliah',
            'nama_matkul' => 'required|string',
            'sks' => 'required|integer',
        ]);

        Matakuliah::create($data);
        return back()->with('success', 'Matakuliah berhasil dibuat');
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
        return back()->with('success', 'Matakuliah berhasil diperbarui');
    }

    public function destroyMatakuliah($id)
    {
        $matkul = Matakuliah::findOrFail($id);
        $matkul->delete();
        return back()->with('success', 'Matakuliah berhasil dihapus');
    }

    // --- MANAJEMEN JADWAL ---
    public function jadwal()
    {
        $jadwal = Jadwal::with(['matakuliah', 'dosen', 'ruangan'])->get();
        $matakuliah = Matakuliah::all();
        $dosen = Pengguna::where('peran', 'dosen')->get();
        $ruangan = Ruangan::all();
        $awalSemester = \App\Models\Pengaturan::where('kunci', 'awal_semester')->value('nilai');
        
        return view('admin.jadwal', compact('jadwal', 'matakuliah', 'dosen', 'ruangan', 'awalSemester'));
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
            'metode' => 'required|in:luring,daring',
        ]);

        Jadwal::create($data);
        return back()->with('success', 'Jadwal berhasil dibuat');
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
            'metode' => 'sometimes|in:luring,daring',
        ]);

        $jadwal->update($data);
        return back()->with('success', 'Jadwal berhasil diperbarui');
    }

    public function destroyJadwal($id)
    {
        $jadwal = Jadwal::findOrFail($id);
        $jadwal->delete();
        return back()->with('success', 'Jadwal berhasil dihapus');
    }

    // --- PENGATURAN & PEMINATAN ---
    public function peminatan()
    {
        // Ambil mahasiswa yang memiliki peminatan, KECUALI yang sudah disetujui
        $mahasiswaIds = Peminatan::where('status', '!=', 'disetujui')
            ->select('mahasiswa_id')
            ->distinct()
            ->pluck('mahasiswa_id');
            
        $mahasiswaList = Pengguna::whereIn('id', $mahasiswaIds)->where('peran', 'mahasiswa')->get();
        
        $is_masa_peminatan = Pengaturan::where('kunci', 'is_masa_peminatan')->value('nilai') === 'true';
        return view('admin.peminatan', compact('mahasiswaList', 'is_masa_peminatan'));
    }

    public function peminatanDetail($mahasiswa_id)
    {
        $mahasiswa = Pengguna::findOrFail($mahasiswa_id);
        $peminatan = Peminatan::with('matakuliah')
            ->where('mahasiswa_id', $mahasiswa_id)
            ->where('status', '!=', 'disetujui')
            ->get();
            
        $is_masa_peminatan = Pengaturan::where('kunci', 'is_masa_peminatan')->value('nilai') === 'true';
        return view('admin.peminatan_detail', compact('mahasiswa', 'peminatan', 'is_masa_peminatan'));
    }

    public function toggleMasaPeminatan(Request $request)
    {
        $request->validate(['is_aktif' => 'required|boolean']);
        Pengaturan::updateOrCreate(
            ['kunci' => 'is_masa_peminatan'],
            ['nilai' => $request->is_aktif ? 'true' : 'false']
        );
        return back()->with('success', 'Masa peminatan ' . ($request->is_aktif ? 'diaktifkan' : 'dinonaktifkan'));
    }

    public function updateStatusPeminatan(Request $request, $id)
    {
        $request->validate(['status' => 'required|in:disetujui,ditolak']);
        $peminatan = Peminatan::findOrFail($id);
        $peminatan->update(['status' => $request->status]);
        return back()->with('success', 'Status peminatan berhasil diperbarui');
    }

    // --- REKAP PRESENSI ---

    public function jadwalDetail($id)
    {
        $jadwal = Jadwal::with(['matakuliah', 'ruangan', 'dosen', 'sesiAktif'])->findOrFail($id);
        $ruangan = Ruangan::all();

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

            $ruanganId = ($sesiJadwal && $sesiJadwal->ruangan_id_reschedule)
                ? $sesiJadwal->ruangan_id_reschedule
                : $jadwal->ruangan_id;

            $ruanganNama = ($sesiJadwal && $sesiJadwal->ruangan_id_reschedule && $sesiJadwal->ruanganReschedule)
                ? $sesiJadwal->ruanganReschedule->nama_ruangan
                : $jadwal->ruangan->nama_ruangan;

            $label = $this->dapatkanLabelPertemuan($p);

            $listPertemuan[] = [
                'pertemuan_ke' => $p,
                'label' => $label,
                'tanggal' => $tanggal,
                'jam_mulai' => $jamMulai,
                'jam_selesai' => $jamSelesai,
                'ruangan_id' => $ruanganId,
                'ruangan_nama' => $ruanganNama,
                'sesi_id' => $sesiJadwal ? $sesiJadwal->id : null,
            ];
        }

        return view('admin.jadwal_detail', compact('jadwal', 'listPertemuan', 'ruangan'));
    }

    public function reschedulePertemuan(Request $request, $id)
    {
        $request->validate([
            'pertemuan_ke' => 'required|integer|min:1|max:16',
            'tanggal' => 'required|date',
            'jam_mulai' => 'required',
            'jam_selesai' => 'required',
            'ruangan_id' => 'required|exists:ruangan,id',
        ]);

        $jadwal = Jadwal::findOrFail($id);

        $sesi = SesiAktif::updateOrCreate(
            [
                'jadwal_id' => $jadwal->id,
                'pertemuan_ke' => $request->pertemuan_ke,
            ],
            [
                'tanggal_reschedule' => $request->tanggal,
                'jam_mulai_reschedule' => $request->jam_mulai,
                'jam_selesai_reschedule' => $request->jam_selesai,
                'ruangan_id_reschedule' => $request->ruangan_id,
                'dosen_pengganti_id' => null, // Optional if we want to allow substituting lecturers later
            ]
        );

        return back()->with('success', 'Jadwal pertemuan ke-' . $request->pertemuan_ke . ' berhasil di-reschedule!');
    }

    private function hitungTanggalPertemuan($hari, $pertemuan_ke)
    {
        $pengaturan = \App\Models\Pengaturan::where('kunci', 'tanggal_mulai_semester')->first();
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

    public function rekap()
    {
        $jadwal = Jadwal::with(['matakuliah', 'dosen'])->get();
        return view('admin.rekap', compact('jadwal'));
    }

    public function getRekapDetail($jadwal_id)
    {
        $jadwal = Jadwal::with(['matakuliah', 'dosen'])->findOrFail($jadwal_id);
        
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
            
        // Ambil semua mahasiswa yang terdaftar di kelas (atau minimal pernah presensi jika tabel pendaftaran belum ada)
        // Idealnya jika ada tabel 'peminatan' yang disetujui untuk kelas ini:
        $mahasiswaIds = \App\Models\Peminatan::where('matakuliah_id', $jadwal->matakuliah_id)->where('status', 'disetujui')->pluck('mahasiswa_id');
        if($mahasiswaIds->isEmpty()) {
             $mahasiswaIds = $presensi->keys(); // Fallback ke mahasiswa yg pernah tap in
        }
        $mahasiswaList = Pengguna::whereIn('id', $mahasiswaIds)->get();
        
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

        return view('admin.rekap_detail', compact('jadwal', 'rekapData', 'totalPertemuan'));
    }

    public function exportRekapExcel($jadwal_id)
    {
        $jadwal = Jadwal::with(['matakuliah', 'dosen'])->findOrFail($jadwal_id);
        
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
        $mahasiswaList = Pengguna::whereIn('id', $mahasiswaIds)->get();

        $filename = "Rekap_Presensi_{$jadwal->matakuliah->kode_matkul}_" . date('Ymd') . ".csv";
        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $callback = function() use($mahasiswaList, $presensi) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['No', 'NIM', 'Nama Mahasiswa', 'Hadir', 'Izin', 'Sakit', 'Total Pertemuan Efektif', 'Persentase']);

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
                
                // Persentase kehadiran SELALU dari total 14 pertemuan efektif
                $persentase = round(($hadir / 14) * 100);

                fputcsv($file, [
                    $no++,
                    $mhs->nomor_identitas,
                    $mhs->nama,
                    $hadir,
                    $izin,
                    $sakit,
                    $totalPertemuan,
                    $persentase . '%'
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
