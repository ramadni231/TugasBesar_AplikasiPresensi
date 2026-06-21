@extends('layouts.admin')

@section('title', 'Detail Peminatan Mahasiswa')

@section('content')
<div class="mb-6 md:mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
    <div>
        <div class="flex items-center gap-3 mb-2">
            <a href="{{ route('admin.peminatan.index') }}" class="w-8 h-8 rounded-full bg-slate-100 dark:bg-[#1E293B] text-slate-600 dark:text-[#F8FAFC]/80 flex items-center justify-center hover:bg-slate-200 dark:hover:bg-slate-700 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>
            <h1 class="text-3xl font-bold text-slate-900 dark:text-[#F8FAFC]">Peminatan Mata Kuliah</h1>
        </div>
        <p class="text-slate-600 dark:text-[#F8FAFC]/80 ml-11">
            Mahasiswa: <strong class="text-slate-900 dark:text-white">{{ $mahasiswa->nama }}</strong> ({{ $mahasiswa->nomor_identitas }})
        </p>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    @forelse($peminatan as $p)
    <div class="bg-white dark:bg-[#0F172A] rounded-xl border border-gray-200 dark:border-[#F8FAFC] p-6 flex flex-col hover:border-blue-500 dark:hover:border-blue-500 hover:shadow-md transition-all relative overflow-hidden">
        <div class="mb-4 pt-2">
            <h3 class="font-bold text-slate-900 dark:text-[#F8FAFC] text-xl">{{ $p->matakuliah->nama_matkul }}</h3>
            <p class="text-base font-bold text-blue-600 dark:text-blue-400 mt-1">{{ $p->matakuliah->kode_matkul }} &bull; {{ $p->matakuliah->sks }} SKS</p>
        </div>
        
        <div class="bg-white/50 dark:bg-[#1E293B]/50 p-4 rounded-lg text-sm mb-4 flex-1">
            <div class="flex items-center justify-between">
                <span class="text-slate-600 dark:text-slate-400 font-medium">Status Peminatan:</span>
                @if($p->status == 'menunggu')
                    <span class="px-3 py-1 rounded-full bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400 font-bold text-xs uppercase tracking-wider">Menunggu</span>
                @elseif($p->status == 'disetujui')
                    <span class="px-3 py-1 rounded-full bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400 font-bold text-xs uppercase tracking-wider">Disetujui</span>
                @else
                    <span class="px-3 py-1 rounded-full bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400 font-bold text-xs uppercase tracking-wider">Ditolak</span>
                @endif
            </div>
            <div class="text-xs text-slate-500 dark:text-slate-400 mt-2">
                Diajukan pada: {{ $p->created_at->format('d M Y, H:i') }}
            </div>
        </div>

        <div class="flex items-center gap-3 mt-auto pt-4 border-t border-slate-100 dark:border-slate-700/50">
            @if($p->status == 'menunggu' || $p->status == 'ditolak')
            <form action="{{ route('admin.peminatan.updateStatus', $p->id) }}" method="POST" class="flex-1">
                @csrf
                <input type="hidden" name="status" value="disetujui">
                <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-semibold py-2.5 px-4 rounded-xl transition-colors flex items-center justify-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Setujui
                </button>
            </form>
            @endif

            @if($p->status == 'menunggu' || $p->status == 'disetujui')
            <form action="{{ route('admin.peminatan.updateStatus', $p->id) }}" method="POST" class="flex-1">
                @csrf
                <input type="hidden" name="status" value="ditolak">
                <button type="submit" class="w-full bg-red-100 hover:bg-red-200 text-red-700 dark:bg-red-900/30 dark:text-red-400 dark:hover:bg-red-900/50 font-semibold py-2.5 px-4 rounded-xl transition-colors flex items-center justify-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    Tolak
                </button>
            </form>
            @endif
        </div>
    </div>
    @empty
    <div class="col-span-full">
        <div class="bg-white dark:bg-[#0F172A] rounded-xl border border-gray-200 dark:border-[#F8FAFC]/20 p-12 flex flex-col items-center justify-center text-center">
            <h3 class="text-xl font-bold text-slate-900 dark:text-[#F8FAFC] mb-2">Tidak Ada Data</h3>
            <p class="text-slate-500 dark:text-[#F8FAFC]/60">Mahasiswa ini tidak memiliki riwayat peminatan mata kuliah.</p>
        </div>
    </div>
    @endforelse
</div>
@endsection
