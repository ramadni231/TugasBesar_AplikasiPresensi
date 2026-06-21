@extends('layouts.admin')

@section('title', 'Pengaturan')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="bg-white dark:bg-[#0F172A] rounded-xl border border-gray-200 dark:border-[#F8FAFC] p-6 shadow-sm">
        <h2 class="text-xl font-bold text-slate-900 dark:text-[#F8FAFC] mb-2">Awal Semester</h2>
        <p class="text-sm text-slate-500 dark:text-[#F8FAFC]/70 mb-6">Atur tanggal awal semester untuk sinkronisasi perhitungan pertemuan kelas pada rekap presensi.</p>
        
        <form action="{{ route('admin.setAwalSemester') }}" method="POST" class="flex flex-col sm:flex-row items-start sm:items-end gap-4">
            @csrf
            <div class="w-full sm:w-auto">
                <label class="block text-sm font-medium text-slate-900 dark:text-[#F8FAFC] mb-2">Tanggal Awal Semester</label>
                <input type="date" name="tanggal" value="{{ $awalSemester ?? date('Y-m-d') }}" required class="w-full sm:w-64 rounded-xl border border-gray-200 dark:border-[#F8FAFC]/20 focus:ring-blue-600 dark:focus:ring-[#1E3A8A] focus:border-blue-600 dark:focus:border-[#1E3A8A] px-4 py-2 bg-white/50 dark:bg-[#1E293B]/50 text-slate-900 dark:text-[#F8FAFC]">
            </div>
            <button type="submit" class="w-full sm:w-auto bg-[#0062FF] hover:bg-blue-700 text-white px-6 py-2 rounded-xl font-medium transition-colors shadow-sm">
                Simpan Pengaturan
            </button>
        </form>
    </div>
</div>
@endsection
