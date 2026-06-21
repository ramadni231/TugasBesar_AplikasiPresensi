@extends('layouts.mahasiswa')

@section('title', 'Detail Riwayat Presensi')

@section('content')
<div class="mb-6">
    <div class="flex items-center gap-4 mb-6">
        <a href="{{ route('mahasiswa.riwayat') }}" class="p-2 bg-white dark:bg-[#0F172A] rounded-xl border border-gray-200 dark:border-[#F8FAFC]/20 text-slate-500 hover:text-blue-600 dark:text-[#F8FAFC]/70 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        </a>
        <div>
            <h2 class="text-2xl font-bold text-slate-900 dark:text-[#F8FAFC]">{{ $jadwal->matakuliah->nama_matkul }}</h2>
            <p class="text-sm font-medium text-slate-500 dark:text-[#F8FAFC]/70 mt-1">{{ $jadwal->matakuliah->kode_matkul }} &bull; {{ $jadwal->matakuliah->sks }} SKS &bull; {{ $jadwal->ruangan->nama_ruangan }}</p>
        </div>
    </div>
</div>

<div class="bg-white dark:bg-[#0F172A] rounded-xl border border-gray-200 dark:border-[#F8FAFC] shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50 dark:bg-[#1E293B] border-b border-gray-200 dark:border-[#F8FAFC]/20">
                    <th class="px-6 py-4 font-bold text-sm text-slate-900 dark:text-[#F8FAFC]">Pertemuan</th>
                    <th class="px-6 py-4 font-bold text-sm text-slate-900 dark:text-[#F8FAFC]">Tanggal</th>
                    <th class="px-6 py-4 font-bold text-sm text-slate-900 dark:text-[#F8FAFC]">Jam & Ruangan</th>
                    <th class="px-6 py-4 font-bold text-sm text-slate-900 dark:text-[#F8FAFC]">Status</th>
                    <th class="px-6 py-4 font-bold text-sm text-slate-900 dark:text-[#F8FAFC] text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-[#F8FAFC]/20">
                @foreach($listPertemuan as $p)
                <tr class="hover:bg-slate-50/50 dark:hover:bg-[#1E3A8A]/10 transition-colors {{ \Carbon\Carbon::parse($p['tanggal'])->isToday() ? 'bg-blue-50/30 dark:bg-blue-900/10' : '' }}">
                    <td class="px-6 py-4">
                        <span class="font-semibold text-slate-900 dark:text-[#F8FAFC]">{{ $p['label'] }}</span>
                        @if(\Carbon\Carbon::parse($p['tanggal'])->isToday())
                        <span class="ml-2 px-2 py-0.5 bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-300 rounded text-xs font-bold">HARI INI</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-sm text-slate-600 dark:text-[#F8FAFC]/80">
                        {{ \Carbon\Carbon::parse($p['tanggal'])->locale('id')->isoFormat('D MMMM YYYY') }}
                    </td>
                    <td class="px-6 py-4 text-sm text-slate-600 dark:text-[#F8FAFC]/80">
                        <div class="font-medium">{{ $p['jam_mulai'] }} - {{ $p['jam_selesai'] }}</div>
                        <div class="text-xs mt-1 text-slate-400">{{ $p['ruangan_nama'] }}</div>
                    </td>
                    <td class="px-6 py-4">
                        @if($p['status'] === 'hadir')
                            <span class="px-3 py-1 bg-green-100 text-green-700 dark:bg-green-900/50 dark:text-green-400 rounded-full text-xs font-bold uppercase tracking-wider">Hadir</span>
                            @if($p['jam_masuk'])
                                <div class="text-xs text-slate-500 mt-1">{{ substr($p['jam_masuk'], 0, 5) }}</div>
                            @endif
                        @elseif($p['status'] === 'alpa')
                            <span class="px-3 py-1 bg-red-100 text-red-700 dark:bg-red-900/50 dark:text-red-400 rounded-full text-xs font-bold uppercase tracking-wider">Alpa</span>
                        @elseif($p['status'] === 'izin')
                            <span class="px-3 py-1 bg-yellow-100 text-yellow-700 dark:bg-yellow-900/50 dark:text-yellow-400 rounded-full text-xs font-bold uppercase tracking-wider">Izin</span>
                        @elseif($p['status'] === 'sakit')
                            <span class="px-3 py-1 bg-orange-100 text-orange-700 dark:bg-orange-900/50 dark:text-orange-400 rounded-full text-xs font-bold uppercase tracking-wider">Sakit</span>
                        @else
                            <span class="text-slate-400 dark:text-[#F8FAFC]/50 text-sm font-medium">Belum Dilaksanakan</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-right">
                        @if($p['status'] === 'belum_dimulai')
                            @if($p['is_sesi_aktif'])
                                <a href="{{ route('mahasiswa.pindai') }}" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-semibold transition-colors shadow-sm inline-block">
                                    Scan Presensi
                                </a>
                            @elseif(\Carbon\Carbon::parse($p['tanggal'])->isToday())
                                <span class="text-slate-400 dark:text-[#F8FAFC]/50 text-sm font-medium">Menunggu Dosen</span>
                            @endif
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
