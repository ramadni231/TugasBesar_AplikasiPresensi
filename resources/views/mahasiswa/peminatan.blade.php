@extends('layouts.mahasiswa')

@section('title', 'KRS & Peminatan Mata Kuliah')

@section('content')
<div class="mb-6">
    <h2 class="text-2xl font-bold text-slate-900 dark:text-[#F8FAFC]">KRS / Peminatan</h2>
    <p class="text-slate-500 dark:text-[#F8FAFC]/70 mt-1">Pilih mata kuliah yang ingin Anda ambil untuk semester ini.</p>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Keranjang KRS -->
    <div class="lg:col-span-1 lg:order-2">
        <div class="bg-white dark:bg-[#0F172A] rounded-xl border border-gray-200 dark:border-[#F8FAFC] p-6 shadow-sm sticky top-6">
            <h3 class="font-bold text-lg mb-4 text-slate-900 dark:text-[#F8FAFC] flex items-center gap-2">
                <svg class="w-5 h-5 text-blue-600 dark:text-[#0062FF]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                Keranjang KRS Anda
            </h3>
            
            @if($peminatan->isEmpty())
                <div class="text-center py-6">
                    <p class="text-sm text-slate-500 dark:text-[#F8FAFC]/70">Belum ada mata kuliah yang Anda pilih.</p>
                </div>
            @else
                <div class="space-y-3">
                    @php $totalSKS = 0; @endphp
                    @foreach($peminatan as $krs)
                    @php $totalSKS += $krs->matakuliah->sks; @endphp
                    <div class="p-3 border border-gray-100 dark:border-[#F8FAFC]/20 rounded-lg flex items-start justify-between bg-slate-50/50 dark:bg-[#1E293B]/30">
                        <div>
                            <h4 class="font-bold text-sm text-slate-900 dark:text-[#F8FAFC]">{{ $krs->matakuliah->nama_matkul }}</h4>
                            <p class="text-xs text-slate-500 dark:text-[#F8FAFC]/70 mt-0.5">{{ $krs->matakuliah->kode_matkul }} &bull; {{ $krs->matakuliah->sks }} SKS</p>
                            <span class="inline-block mt-2 px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider
                                {{ $krs->status == 'disetujui' ? 'bg-green-100 text-green-700' : 
                                   ($krs->status == 'ditolak' ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700') }}">
                                {{ $krs->status }}
                            </span>
                        </div>
                        @if($krs->status == 'menunggu')
                        <form action="{{ route('mahasiswa.peminatan.destroy', $krs->id) }}" method="POST" onsubmit="return confirm('Batalkan pilihan mata kuliah ini?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="p-1.5 text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-md transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </form>
                        @endif
                    </div>
                    @endforeach
                    
                    <div class="pt-3 mt-3 border-t border-gray-200 dark:border-[#F8FAFC]/20 flex justify-between items-center">
                        <span class="font-medium text-sm text-slate-600 dark:text-[#F8FAFC]/80">Total SKS</span>
                        <span class="font-bold text-lg text-slate-900 dark:text-[#F8FAFC]">{{ $totalSKS }} SKS</span>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Daftar Mata Kuliah -->
    <div class="lg:col-span-2 lg:order-1" x-data="{ search: '' }">
        <div class="bg-white dark:bg-[#0F172A] rounded-xl border border-gray-200 dark:border-[#F8FAFC] p-6 shadow-sm">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
                <h3 class="font-bold text-lg text-slate-900 dark:text-[#F8FAFC]">Daftar Mata Kuliah Tersedia</h3>
                <div class="relative w-full sm:w-64">
                    <input x-model="search" type="text" placeholder="Cari mata kuliah..." class="w-full rounded-xl border border-gray-200 dark:border-[#F8FAFC]/20 bg-slate-50 dark:bg-[#1E293B] text-sm px-4 py-2 pl-10 focus:ring-blue-600 focus:border-blue-600 dark:text-[#F8FAFC]">
                    <svg class="w-4 h-4 absolute left-3.5 top-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
            </div>

            <div class="space-y-4">
                @foreach($matakuliah as $mk)
                @php
                    // Cek apakah mahasiswa ini sudah memilih matkul ini
                    $isTaken = $peminatan->where('matakuliah_id', $mk->id)->first();
                @endphp
                <div x-show="search === '' || '{{ strtolower($mk->nama_matkul) }}'.includes(search.toLowerCase()) || '{{ strtolower($mk->kode_matkul) }}'.includes(search.toLowerCase())" class="border border-gray-200 dark:border-[#F8FAFC]/20 rounded-xl p-4 flex flex-col sm:flex-row justify-between items-start sm:items-center hover:border-blue-300 transition-colors gap-4">
                    <div>
                        <div class="flex items-center gap-2 mb-1">
                            <span class="text-xs font-bold text-blue-600 dark:text-[#0062FF] bg-blue-50 dark:bg-[#1E3A8A]/30 px-2 py-0.5 rounded">{{ $mk->kode_matkul }}</span>
                            <span class="text-xs font-bold text-slate-600 dark:text-[#F8FAFC]/80 bg-slate-100 dark:bg-[#1E293B] px-2 py-0.5 rounded">{{ $mk->sks }} SKS</span>
                        </div>
                        <h4 class="font-bold text-slate-900 dark:text-[#F8FAFC]">{{ $mk->nama_matkul }}</h4>
                    </div>
                    
                    @if($isTaken)
                        <button disabled class="w-full sm:w-auto px-4 py-2 rounded-lg bg-gray-100 dark:bg-[#1E293B] text-gray-500 dark:text-[#F8FAFC]/60 text-sm font-medium cursor-not-allowed">
                            Telah Dipilih
                        </button>
                    @else
                        <form action="{{ route('mahasiswa.peminatan.store') }}" method="POST" class="w-full sm:w-auto">
                            @csrf
                            <input type="hidden" name="matakuliah_id" value="{{ $mk->id }}">
                            <button type="submit" class="w-full sm:w-auto px-4 py-2 rounded-lg bg-[#0062FF] hover:bg-blue-700 dark:bg-[#1E3A8A] dark:hover:bg-[#1E3A8A]/80 text-white text-sm font-medium transition-colors">
                                Ambil
                            </button>
                        </form>
                    @endif
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection
