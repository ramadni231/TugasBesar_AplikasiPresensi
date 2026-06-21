@extends('layouts.admin')

@section('title', 'Kelola Peminatan Mahasiswa')

@section('content')
<div class="mb-6 md:mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
    <div>
        <h1 class="text-3xl font-bold text-slate-900 dark:text-[#F8FAFC]">Kelola Peminatan</h1>
        <p class="text-slate-600 dark:text-[#F8FAFC]/80 mt-1">Daftar mahasiswa yang mengajukan peminatan mata kuliah.</p>
    </div>
    <div class="flex items-center gap-3">
        <form action="{{ route('admin.peminatan.toggle') }}" method="POST">
            @csrf
            <input type="hidden" name="is_aktif" value="{{ $is_masa_peminatan ? 0 : 1 }}">
            <button type="submit" class="flex items-center gap-3 px-4 py-2 rounded-xl bg-white dark:bg-[#0F172A] border border-gray-200 dark:border-[#F8FAFC]/20 hover:bg-slate-50 dark:hover:bg-[#1E293B] transition-colors shadow-sm">
                <span class="text-sm font-medium {{ $is_masa_peminatan ? 'text-green-600 dark:text-green-400' : 'text-slate-500 dark:text-slate-400' }}">
                    {{ $is_masa_peminatan ? 'Status: Dibuka' : 'Status: Ditutup' }}
                </span>
                <div class="relative w-12 h-6 rounded-full transition-colors duration-300 {{ $is_masa_peminatan ? 'bg-green-500' : 'bg-slate-300 dark:bg-slate-600' }}">
                    <div class="absolute top-1 left-1 w-4 h-4 rounded-full bg-white transition-transform duration-300 {{ $is_masa_peminatan ? 'translate-x-6' : 'translate-x-0' }}"></div>
                </div>
            </button>
        </form>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
    @forelse($mahasiswaList as $mhs)
    <div onclick="window.location.href='{{ route('admin.peminatan.detail', $mhs->id) }}'" class="bg-white dark:bg-[#0F172A] rounded-xl border border-gray-200 dark:border-[#F8FAFC]/20 p-6 flex flex-col hover:border-blue-500 dark:hover:border-blue-500 hover:shadow-md transition-all cursor-pointer">
        <div class="w-16 h-16 rounded-full bg-blue-100 dark:bg-[#1E3A8A]/30 text-blue-600 dark:text-blue-400 flex items-center justify-center mx-auto mb-4 font-bold text-xl uppercase">
            {{ substr($mhs->nama, 0, 2) }}
        </div>
        <div class="text-center mb-4 flex-1">
            <h3 class="font-bold text-slate-900 dark:text-[#F8FAFC] text-lg">{{ $mhs->nama }}</h3>
            <p class="text-sm font-medium text-slate-500 dark:text-[#F8FAFC]/60 mt-1">{{ $mhs->nomor_identitas }}</p>
        </div>
        <div class="flex items-center justify-center border-t border-gray-100 dark:border-slate-700/50 pt-4">
            <span class="text-blue-600 dark:text-[#0062FF] font-semibold text-sm flex items-center gap-2 group-hover:gap-3 transition-all">
                Lihat Mata Kuliah
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </span>
        </div>
    </div>
    @empty
    <div class="col-span-full">
        <div class="bg-white dark:bg-[#0F172A] rounded-xl border border-gray-200 dark:border-[#F8FAFC]/20 p-12 flex flex-col items-center justify-center text-center">
            <div class="w-20 h-20 bg-slate-50 dark:bg-[#1E293B] rounded-full flex items-center justify-center mb-4">
                <svg class="w-10 h-10 text-slate-400 dark:text-[#F8FAFC]/40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
            </div>
            <h3 class="text-xl font-bold text-slate-900 dark:text-[#F8FAFC] mb-2">Belum Ada Peminatan</h3>
            <p class="text-slate-500 dark:text-[#F8FAFC]/60 max-w-sm">Belum ada mahasiswa yang mengajukan peminatan mata kuliah saat ini.</p>
        </div>
    </div>
    @endforelse
</div>
@endsection
