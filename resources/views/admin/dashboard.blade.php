@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
<div class="mb-6">
    <h2 class="text-2xl font-bold text-slate-900 dark:text-[#F8FAFC]">Halo, Admin</h2>
    <p class="text-sm font-medium text-slate-500 dark:text-[#F8FAFC]/70 mt-1">Selamat datang di Dashboard Admin. Berikut ringkasan statistik hari ini.</p>
</div>


<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
    <!-- Card 1 -->
    <div class="bg-white dark:bg-[#0F172A] rounded-xl border border-gray-200 dark:border-[#F8FAFC] p-6 flex items-center gap-4 hover:border-blue-500 dark:hover:border-blue-500 hover:shadow-md transition-all">
        <div class="p-3 bg-blue-100 text-blue-600 dark:text-[#0062FF] rounded-lg">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
        </div>
        <div>
            <p class="text-sm font-medium text-slate-500 dark:text-[#F8FAFC]/70">Total Pengguna</p>
            <p class="text-2xl font-bold text-slate-900 dark:text-[#F8FAFC]">{{ $totalPengguna }}</p>
        </div>
    </div>

    <!-- Card 2 -->
    <div class="bg-white dark:bg-[#0F172A] rounded-xl border border-gray-200 dark:border-[#F8FAFC] p-6 flex items-center gap-4 hover:border-blue-500 dark:hover:border-blue-500 hover:shadow-md transition-all">
        <div class="p-3 bg-indigo-100 text-indigo-600 rounded-lg">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
        </div>
        <div>
            <p class="text-sm font-medium text-slate-500 dark:text-[#F8FAFC]/70">Dosen</p>
            <p class="text-2xl font-bold text-slate-900 dark:text-[#F8FAFC]">{{ $totalDosen }}</p>
        </div>
    </div>

    <!-- Card 3 -->
    <div class="bg-white dark:bg-[#0F172A] rounded-xl border border-gray-200 dark:border-[#F8FAFC] p-6 flex items-center gap-4 hover:border-blue-500 dark:hover:border-blue-500 hover:shadow-md transition-all">
        <div class="p-3 bg-green-100 text-green-600 rounded-lg">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
        </div>
        <div>
            <p class="text-sm font-medium text-slate-500 dark:text-[#F8FAFC]/70">Mahasiswa</p>
            <p class="text-2xl font-bold text-slate-900 dark:text-[#F8FAFC]">{{ $totalMahasiswa }}</p>
        </div>
    </div>

    <!-- Card 4 -->
    <div class="bg-white dark:bg-[#0F172A] rounded-xl border border-gray-200 dark:border-[#F8FAFC] p-6 flex items-center gap-4 hover:border-blue-500 dark:hover:border-blue-500 hover:shadow-md transition-all">
        <div class="p-3 bg-purple-100 text-purple-600 rounded-lg">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
        </div>
        <div>
            <p class="text-sm font-medium text-slate-500 dark:text-[#F8FAFC]/70">Ruangan</p>
            <p class="text-2xl font-bold text-slate-900 dark:text-[#F8FAFC]">{{ $totalRuangan }}</p>
        </div>
    </div>

    <!-- Card 5 -->
    <div class="bg-white dark:bg-[#0F172A] rounded-xl border border-gray-200 dark:border-[#F8FAFC] p-6 flex items-center gap-4 hover:border-blue-500 dark:hover:border-blue-500 hover:shadow-md transition-all">
        <div class="p-3 bg-rose-100 text-rose-600 rounded-lg">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
        </div>
        <div>
            <p class="text-sm font-medium text-slate-500 dark:text-[#F8FAFC]/70">Mata Kuliah</p>
            <p class="text-2xl font-bold text-slate-900 dark:text-[#F8FAFC]">{{ $totalMatkul }}</p>
        </div>
    </div>

    <!-- Card 6 -->
    <div class="bg-white dark:bg-[#0F172A] rounded-xl border border-gray-200 dark:border-[#F8FAFC] p-6 flex items-center gap-4 hover:border-blue-500 dark:hover:border-blue-500 hover:shadow-md transition-all">
        <div class="p-3 bg-yellow-100 text-yellow-600 rounded-lg">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
        </div>
        <div>
            <p class="text-sm font-medium text-slate-500 dark:text-[#F8FAFC]/70">Jadwal Kelas</p>
            <p class="text-2xl font-bold text-slate-900 dark:text-[#F8FAFC]">{{ $totalJadwal }}</p>
        </div>
    </div>
</div>
@endsection
