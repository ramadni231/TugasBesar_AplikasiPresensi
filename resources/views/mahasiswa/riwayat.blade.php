@extends('layouts.mahasiswa')

@section('title', 'Riwayat Presensi')

@section('content')
<div x-data="{ search: '' }">
<div class="mb-6">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
        <div>
            <h2 class="text-2xl font-bold">Riwayat Presensi</h2>
            <p class="text-sm font-medium text-slate-500 dark:text-[#F8FAFC]/70 mt-1">Pilih mata kuliah untuk melihat detail 16 sesi presensi Anda.</p>
        </div>
        <div class="relative w-full sm:w-64">
            <input x-model="search" type="text" placeholder="Cari matakuliah..." class="w-full rounded-xl border border-gray-200 dark:border-[#F8FAFC]/20 bg-white dark:bg-[#0F172A] text-sm px-4 py-2 pl-10 focus:ring-blue-600 focus:border-blue-600 dark:focus:ring-[#1E3A8A] dark:text-[#F8FAFC]">
            <svg class="w-4 h-4 absolute left-3.5 top-3 text-slate-400 dark:text-[#F8FAFC]/60" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
        </div>
    </div>

@if($jadwal->isEmpty())
    <div class="bg-white dark:bg-[#0F172A] rounded-xl border border-gray-200 dark:border-[#F8FAFC]/20 p-12 text-center">
        <div class="w-16 h-16 bg-slate-100 dark:bg-[#1E293B] rounded-full flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
        </div>
        <h3 class="text-lg font-medium text-slate-900 dark:text-[#F8FAFC] mb-1">Belum Ada Kelas</h3>
        <p class="text-slate-500 dark:text-[#F8FAFC]/70">Anda belum disetujui dalam kelas apapun.</p>
    </div>
@else
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($jadwal as $j)
        <a href="{{ route('mahasiswa.riwayat.detail', $j->id) }}" x-show="search === '' || '{{ strtolower($j->matakuliah->nama_matkul) }}'.includes(search.toLowerCase()) || '{{ strtolower($j->matakuliah->kode_matkul) }}'.includes(search.toLowerCase())" class="bg-white dark:bg-[#0F172A] rounded-xl border border-gray-200 dark:border-[#F8FAFC] p-6 flex flex-col hover:border-blue-500 dark:hover:border-blue-500 hover:shadow-md transition-all relative overflow-hidden group">
            <div class="absolute top-0 right-0 bg-blue-600 dark:bg-[#0062FF] text-white px-5 py-2.5 rounded-bl-xl text-base font-bold shadow-sm">
                {{ $j->hari }}
            </div>
            
            <div class="mb-4 pt-2">
                <h3 class="font-bold text-slate-900 dark:text-[#F8FAFC] text-lg">{{ $j->matakuliah->nama_matkul }}</h3>
                <p class="text-sm font-semibold text-blue-600 dark:text-[#F8FAFC]/70 mt-1">{{ $j->matakuliah->kode_matkul }} &bull; {{ $j->matakuliah->sks }} SKS</p>
            </div>
            
            <div class="bg-white/50 dark:bg-[#1E293B]/50 p-3 rounded-lg text-sm text-slate-600 dark:text-[#F8FAFC]/80 flex-1 space-y-3">
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5 text-slate-400 dark:text-[#F8FAFC]/60" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span class="font-medium text-slate-900 dark:text-[#F8FAFC]">{{ substr($j->jam_mulai, 0, 5) }} - {{ substr($j->jam_selesai, 0, 5) }}</span>
                </div>
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5 text-slate-400 dark:text-[#F8FAFC]/60" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    <span class="font-medium text-slate-900 dark:text-[#F8FAFC]">{{ $j->dosen->nama }}</span>
                </div>
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5 text-slate-400 dark:text-[#F8FAFC]/60" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    <span class="font-medium text-slate-900 dark:text-[#F8FAFC]">{{ $j->ruangan->nama_ruangan }}</span>
                </div>
            </div>

            <div class="mt-4 pt-4 border-t border-gray-100 dark:border-[#F8FAFC]/20 flex justify-between items-center">
                <span class="px-2 py-1 bg-gray-100 text-gray-600 dark:bg-[#1E293B] dark:text-[#F8FAFC]/80 rounded text-xs font-bold uppercase tracking-wider">
                    {{ $j->metode }}
                </span>
                <span class="text-blue-600 dark:text-[#0062FF] text-sm font-medium flex items-center gap-1">
                    Lihat 16 Sesi <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </span>
            </div>
        </a>
        @endforeach
    </div>
@endif
</div>
@endsection
