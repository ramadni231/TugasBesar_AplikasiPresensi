@extends('layouts.admin')

@section('title', 'Detail Rekap Presensi')

@section('content')
<div class="mb-6" x-data="{ search: '' }">
    <div class="flex items-center justify-between mb-4 flex-wrap gap-4">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.rekap.index') }}" class="p-2 bg-white dark:bg-[#0F172A] rounded-xl text-slate-500 dark:text-[#F8FAFC]/70 hover:text-slate-900 dark:hover:text-[#F8FAFC] hover:bg-slate-50 dark:hover:bg-[#1E293B] border border-gray-200 dark:border-[#F8FAFC]/20 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>
            <div>
                <h2 class="text-2xl font-bold text-slate-900 dark:text-[#F8FAFC]">Detail Rekap: {{ $jadwal->matakuliah->nama_matkul }}</h2>
                <p class="text-slate-500 dark:text-[#F8FAFC]/70 mt-1">Dosen: {{ $jadwal->dosen->nama }} | Jadwal: {{ ucfirst($jadwal->hari) }}, {{ substr($jadwal->jam_mulai, 0, 5) }}</p>
            </div>
        </div>
        <div class="flex items-center gap-3 w-full sm:w-auto">
            <div class="relative flex-1 sm:w-64">
                <input x-model="search" type="text" placeholder="Cari nama atau NIM..." class="w-full rounded-xl border border-gray-200 dark:border-[#F8FAFC]/20 bg-white dark:bg-[#0F172A] text-sm px-4 py-2 pl-10 focus:ring-blue-600 focus:border-blue-600 dark:focus:ring-[#1E3A8A] dark:text-[#F8FAFC]">
                <svg class="w-4 h-4 absolute left-3.5 top-3 text-slate-400 dark:text-[#F8FAFC]/60" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>
            <a href="{{ route('admin.rekap.export', $jadwal->id) }}" class="flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-xl text-sm font-medium transition-colors whitespace-nowrap">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Export CSV
            </a>
        </div>
    </div>

    <div class="bg-white dark:bg-[#0F172A] rounded-xl border border-gray-200 dark:border-[#F8FAFC]/20 overflow-hidden shadow-lg">
        <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-white/50 dark:bg-[#1E293B]/50 border-b border-gray-200 dark:border-[#F8FAFC]/20 text-slate-900 dark:text-[#F8FAFC] text-sm">
                    <th class="p-4 font-semibold w-12 text-center">No</th>
                    <th class="p-4 font-semibold">NIM</th>
                    <th class="p-4 font-semibold">Nama Mahasiswa</th>
                    <th class="p-4 font-semibold text-center text-green-500">Hadir</th>
                    <th class="p-4 font-semibold text-center text-yellow-500">Izin</th>
                    <th class="p-4 font-semibold text-center text-blue-500">Sakit</th>
                    <th class="p-4 font-semibold text-center">Total</th>
                    <th class="p-4 font-semibold text-center">Persentase</th>
                </tr>
            </thead>
            <tbody class="text-sm text-slate-600 dark:text-[#F8FAFC]/80 divide-y divide-[#F8FAFC]/10">
                @forelse($rekapData as $idx => $mhs)
                @php
                    // Persentase kehadiran SELALU dari total 14 pertemuan efektif
                    $persentase = round(($mhs['total_hadir'] / 14) * 100);
                @endphp
                <tr x-show="search === '' || '{{ strtolower($mhs['nama']) }}'.includes(search.toLowerCase()) || '{{ strtolower($mhs['nomor_identitas']) }}'.includes(search.toLowerCase())" class="hover:bg-slate-50 dark:hover:bg-[#1E293B]/50 transition-colors">
                    <td class="p-4 text-center text-slate-400 dark:text-[#F8FAFC]/60">{{ $idx + 1 }}</td>
                    <td class="p-4 font-medium text-slate-900 dark:text-[#F8FAFC]">{{ $mhs['nomor_identitas'] }}</td>
                    <td class="p-4">{{ $mhs['nama'] }}</td>
                    <td class="p-4 text-center font-bold text-green-500">{{ $mhs['total_hadir'] }}</td>
                    <td class="p-4 text-center font-bold text-yellow-500">{{ $mhs['total_izin'] }}</td>
                    <td class="p-4 text-center font-bold text-blue-500">{{ $mhs['total_sakit'] }}</td>
                    <td class="p-4 text-center font-bold">{{ $mhs['total_pertemuan'] }}</td>
                    <td class="p-4 text-center">
                        <div class="flex items-center justify-center gap-2">
                            <div class="w-16 bg-white/50 dark:bg-[#1E293B]/50 rounded-full h-1.5 overflow-hidden">
                                <div class="bg-green-500 h-1.5 rounded-full" style="width: {{ $persentase }}%"></div>
                            </div>
                            <span class="text-xs font-bold {{ $persentase >= 75 ? 'text-green-500' : 'text-red-500' }}">{{ $persentase }}%</span>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="p-8 text-center text-slate-900 dark:text-[#F8FAFC]/50">
                        Belum ada data presensi untuk kelas ini.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
