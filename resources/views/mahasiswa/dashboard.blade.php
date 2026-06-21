@extends('layouts.mahasiswa')

@section('title', 'Jadwal Hari Ini')

@section('content')
<div class="mb-6">
    <div x-data="{ search: \'\' }">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
        <div>
            <h2 class="text-2xl font-bold text-slate-900 dark:text-[#F8FAFC]">Halo, {{ Auth::user()->nama }}</h2>
            <p class="text-sm font-medium text-slate-500 dark:text-[#F8FAFC]/70 mt-1">Daftar mata kuliah Anda untuk hari ini.</p>
        </div>
        <div class="relative w-full sm:w-64">
            <input x-model="search" type="text" placeholder="Cari jadwal..." class="w-full rounded-xl border border-gray-200 dark:border-[#F8FAFC]/20 bg-white dark:bg-[#0F172A] text-sm px-4 py-2 pl-10 focus:ring-blue-600 focus:border-blue-600 dark:focus:ring-[#1E3A8A] dark:text-[#F8FAFC]">
            <svg class="w-4 h-4 absolute left-3.5 top-3 text-slate-400 dark:text-[#F8FAFC]/60" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
        </div>
    </div>
</div>

@if(count($jadwalHariIni) == 0)
    <div class="bg-white dark:bg-[#0F172A] rounded-xl border border-gray-200 dark:border-[#F8FAFC] p-8 text-center">
        <div class="w-16 h-16 bg-white/50 dark:bg-[#1E293B]/50 text-slate-400 dark:text-[#F8FAFC]/60 rounded-full flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
        </div>
        <h3 class="text-lg font-medium text-slate-900 dark:text-[#F8FAFC] mb-1">Tidak Ada Jadwal</h3>
        <p class="text-slate-500 dark:text-[#F8FAFC]/70">Anda tidak memiliki jadwal perkuliahan hari ini.</p>
    </div>
@else
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        @foreach($jadwalHariIni as $j)
        @php
            $sesiAktif = \App\Models\SesiAktif::where('jadwal_id', $j->id)->where('is_aktif', true)->first();
        @endphp
        <div class="bg-white dark:bg-[#0F172A] rounded-xl border border-gray-200 dark:border-[#F8FAFC] p-6 shadow-sm relative overflow-hidden hover:border-blue-500 dark:hover:border-blue-500 hover:shadow-md transition-all">
            @if($sesiAktif)
                <div class="absolute top-0 right-0 bg-green-500 text-white px-3 py-1 rounded-bl-xl font-medium text-xs shadow-sm flex items-center gap-1">
                    <span class="w-2 h-2 bg-white dark:bg-[#0F172A] rounded-full animate-pulse"></span> Sesi Aktif
                </div>
            @endif

            <div class="flex items-start justify-between mb-4 mt-2">
                <div>
                    <h3 class="font-bold text-slate-900 dark:text-[#F8FAFC] text-lg">{{ $j->matakuliah->nama_matkul }}</h3>
                    <p class="text-sm text-slate-500 dark:text-[#F8FAFC]/70 mt-1">{{ $j->matakuliah->kode_matkul }} • {{ $j->matakuliah->sks }} SKS</p>
                </div>
                <span class="px-2.5 py-1 rounded-full text-xs font-medium uppercase tracking-wider bg-slate-100 dark:bg-[#1E3A8A] text-slate-900 dark:text-[#F8FAFC]">
                    {{ $j->metode }}
                </span>
            </div>

            <div class="space-y-3 mb-6 bg-white/50 dark:bg-[#1E293B]/50 p-4 rounded-xl text-sm">
                <div class="flex items-center gap-3 text-slate-900 dark:text-[#F8FAFC]">
                    <svg class="w-5 h-5 text-slate-400 dark:text-[#F8FAFC]/60" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span class="font-medium">{{ substr($j->jam_mulai, 0, 5) }} - {{ substr($j->jam_selesai, 0, 5) }}</span>
                </div>
                <div class="flex items-center gap-3 text-slate-900 dark:text-[#F8FAFC]">
                    <svg class="w-5 h-5 text-slate-400 dark:text-[#F8FAFC]/60" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    <span class="font-medium">{{ $j->ruangan->nama_ruangan }}</span>
                </div>
                <div class="flex items-center gap-3 text-slate-900 dark:text-[#F8FAFC]">
                    <svg class="w-5 h-5 text-slate-400 dark:text-[#F8FAFC]/60" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    <span class="font-medium">{{ $j->dosen->nama }}</span>
                </div>
            </div>

            <div class="pt-4 border-t border-gray-100">
                @if($sesiAktif)
                    <a href="{{ route('mahasiswa.pindai') }}" class="block text-center w-full bg-[#0062FF] hover:bg-blue-700 dark:bg-[#1E3A8A] dark:hover:bg-[#1E3A8A]/80 text-white font-medium py-2.5 px-4 rounded-xl transition-colors text-sm">
                        Lakukan Presensi Sekarang
                    </a>
                @else
                    <button disabled class="w-full bg-slate-100 dark:bg-[#1E3A8A] text-slate-400 dark:text-[#F8FAFC]/60 font-medium py-2.5 px-4 rounded-xl text-sm cursor-not-allowed">
                        Sesi Presensi Belum Dibuka
                    </button>
                @endif
            </div>
        </div>
        @endforeach
    </div>
@endif
</div>
@endsection
