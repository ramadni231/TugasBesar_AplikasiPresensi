@extends('layouts.dosen')

@section('title', 'Validasi Izin Mahasiswa')

@section('content')
<div class="mb-6">
    <h2 class="text-2xl font-bold">Validasi Izin Mahasiswa</h2>
    <p class="text-slate-500 dark:text-[#F8FAFC]/70 mt-1">Daftar pengajuan izin mahasiswa yang membutuhkan validasi.</p>
</div>

@if(count($izin) == 0)
    <div class="bg-white dark:bg-[#0F172A] rounded-xl border border-gray-200 dark:border-[#F8FAFC] p-8 text-center">
        <div class="w-16 h-16 bg-white/50 dark:bg-[#1E293B]/50 text-slate-400 dark:text-[#F8FAFC]/60 rounded-full flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <h3 class="text-lg font-medium text-slate-900 dark:text-[#F8FAFC] mb-1">Tidak Ada Pengajuan</h3>
        <p class="text-slate-500 dark:text-[#F8FAFC]/70">Semua pengajuan izin telah divalidasi.</p>
    </div>
@else
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        @foreach($izin as $i)
        <div class="bg-white dark:bg-[#0F172A] rounded-xl border border-gray-200 dark:border-[#F8FAFC] p-6 flex flex-col hover:shadow-md transition-shadow">
            <div class="flex justify-between items-start mb-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-slate-100 dark:bg-[#1E3A8A] flex items-center justify-center text-slate-600 dark:text-[#F8FAFC]/80 font-bold">
                        {{ strtoupper(substr($i->pengguna->nama, 0, 1)) }}
                    </div>
                    <div>
                        <h3 class="font-bold text-slate-900 dark:text-[#F8FAFC]">{{ $i->pengguna->nama }}</h3>
                        <p class="text-xs text-slate-500 dark:text-[#F8FAFC]/70">{{ $i->pengguna->nomor_identitas }}</p>
                    </div>
                </div>
                <span class="px-2.5 py-1 rounded-full text-xs font-medium {{ $i->tipe_izin == 'sakit' ? 'bg-red-100 text-red-700' : 'bg-orange-100 text-orange-700' }}">
                    {{ ucfirst($i->tipe_izin) }}
                </span>
            </div>
            
            <div class="bg-white/50 dark:bg-[#1E293B]/50 p-4 rounded-xl text-sm text-slate-900 dark:text-[#F8FAFC] mb-4 flex-1">
                <p><strong>Tanggal:</strong> {{ \Carbon\Carbon::parse($i->tanggal)->format('d M Y') }}</p>
                <p class="mt-2"><strong>Alasan:</strong><br>{{ $i->alasan }}</p>
                
                @if($i->jalur_lampiran)
                <div class="mt-4">
                    <a href="{{ asset('storage/' . $i->jalur_lampiran) }}" target="_blank" class="inline-flex items-center gap-2 text-blue-600 dark:text-[#0062FF] hover:text-blue-800 text-sm font-medium">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
                        Lihat Lampiran
                    </a>
                </div>
                @endif
            </div>

            <div class="flex items-center gap-3 mt-auto pt-4 border-t border-gray-100">
                <form action="{{ route('dosen.izin.status', $i->id) }}" method="POST" class="flex-1">
                    @csrf
                    <input type="hidden" name="status_persetujuan" value="disetujui">
                    <button type="submit" class="w-full bg-green-50 hover:bg-green-100 text-green-700 font-medium py-2 px-4 rounded-xl transition-colors border border-green-200">
                        Setujui
                    </button>
                </form>
                <form action="{{ route('dosen.izin.status', $i->id) }}" method="POST" class="flex-1">
                    @csrf
                    <input type="hidden" name="status_persetujuan" value="ditolak">
                    <button type="submit" class="w-full bg-red-50 hover:bg-red-100 text-red-700 font-medium py-2 px-4 rounded-xl transition-colors border border-red-200">
                        Tolak
                    </button>
                </form>
            </div>
        </div>
        @endforeach
    </div>
@endif
@endsection
