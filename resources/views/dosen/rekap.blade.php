@extends('layouts.dosen')

@section('title', 'Rekap Presensi')

@section('content')
<div class="mb-6">
    <h2 class="text-2xl font-bold">Rekap Presensi Kelas</h2>
    <p class="text-slate-500 dark:text-[#F8FAFC]/70 mt-1">Pilih jadwal matakuliah untuk melihat rekap kehadiran mahasiswa Anda.</p>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    @foreach($jadwal as $j)
    <a href="{{ route('dosen.rekap.detail', $j->id) }}" class="relative overflow-hidden bg-white dark:bg-[#0F172A] rounded-xl border border-gray-200 dark:border-[#F8FAFC] p-6 flex flex-col hover:border-blue-500 dark:hover:border-blue-500 hover:shadow-md transition-all group block">

        <div class="flex items-center gap-3 mb-4">
            <div class="p-3 bg-indigo-100 text-indigo-600 rounded-lg group-hover:bg-indigo-600 group-hover:text-white transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
            </div>
            <div>
                <h3 class="font-bold text-slate-900 dark:text-[#F8FAFC] text-xl">{{ $j->matakuliah->nama_matkul }}</h3>
                <p class="text-base font-bold text-blue-600 dark:text-[#0062FF] bg-blue-50 dark:bg-[#1E3A8A]/30 inline-block px-4 py-1.5 rounded-full mt-1">{{ $j->matakuliah->kode_matkul }}</p>
            </div>
        </div>
        
        <div class="bg-white/50 dark:bg-[#1E293B]/50 p-3 rounded-lg text-sm text-slate-600 dark:text-[#F8FAFC]/80 flex-1 space-y-2">
            <div class="flex justify-between">
                <span class="text-slate-500 dark:text-[#F8FAFC]/70 flex items-center gap-1"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg> Ruangan:</span>
                <span class="font-medium text-slate-900 dark:text-[#F8FAFC]">{{ $j->ruangan->nama_ruangan }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-slate-500 dark:text-[#F8FAFC]/70 flex items-center gap-1"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg> Jadwal:</span>
                <span class="font-medium text-slate-900 dark:text-[#F8FAFC]">{{ ucfirst($j->hari) }}, {{ substr($j->jam_mulai, 0, 5) }}</span>
            </div>
        </div>
        
        <div class="mt-4 flex items-center justify-between text-blue-600 dark:text-[#0062FF] font-medium text-sm">
            Lihat Rekap
            <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        </div>
    </a>
    @endforeach
</div>

@if($jadwal->isEmpty())
<div class="bg-white dark:bg-[#0F172A] rounded-xl border border-gray-200 dark:border-[#F8FAFC]/20 p-8 text-center">
    <div class="mx-auto w-16 h-16 bg-blue-50 dark:bg-[#1E3A8A]/30 text-blue-600 dark:text-[#0062FF] rounded-full flex items-center justify-center mb-4">
        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg>
    </div>
    <h3 class="text-lg font-bold text-slate-900 dark:text-[#F8FAFC] mb-1">Belum ada kelas</h3>
    <p class="text-slate-500 dark:text-[#F8FAFC]/70 text-sm">Anda belum memiliki jadwal mengajar.</p>
</div>
@endif
@endsection
