@extends('layouts.mahasiswa')

@section('title', 'Pengajuan Izin')

@section('content')
<div class="mb-6">
    <h2 class="text-2xl font-bold">Pengajuan Izin</h2>
    <p class="text-slate-500 dark:text-[#F8FAFC]/70 mt-1">Ajukan surat izin atau sakit untuk tidak hadir perkuliahan.</p>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Form Pengajuan -->
    <div class="lg:col-span-1">
        <div class="bg-white dark:bg-[#0F172A] rounded-xl border border-gray-200 dark:border-[#F8FAFC] p-6 shadow-sm">
            <h3 class="font-bold text-lg mb-4">Buat Pengajuan Baru</h3>
            <form action="{{ route('mahasiswa.izin.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-900 dark:text-[#F8FAFC] mb-1">Tipe Izin</label>
                        <select name="tipe_izin" required class="w-full rounded-xl border-gray-200 dark:border-[#F8FAFC] focus:ring-blue-600 dark:focus:ring-[#1E3A8A] focus:border-blue-600 dark:focus:border-[#1E3A8A] px-4 py-2 border bg-white/50 dark:bg-[#0F172A]/50 text-slate-900 dark:text-[#F8FAFC]">
                            <option value="sakit">Sakit</option>
                            <option value="izin">Izin</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-900 dark:text-[#F8FAFC] mb-1">Tanggal</label>
                        <input type="date" name="tanggal" required class="w-full rounded-xl border-gray-200 dark:border-[#F8FAFC] focus:ring-blue-600 dark:focus:ring-[#1E3A8A] focus:border-blue-600 dark:focus:border-[#1E3A8A] px-4 py-2 border bg-white/50 dark:bg-[#0F172A]/50 text-slate-900 dark:text-[#F8FAFC]" min="{{ date('Y-m-d') }}">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-900 dark:text-[#F8FAFC] mb-1">Alasan</label>
                        <textarea name="alasan" required rows="3" class="w-full rounded-xl border-gray-200 dark:border-[#F8FAFC] focus:ring-blue-600 dark:focus:ring-[#1E3A8A] focus:border-blue-600 dark:focus:border-[#1E3A8A] px-4 py-2 border bg-white/50 dark:bg-[#0F172A]/50 text-slate-900 dark:text-[#F8FAFC]"></textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-900 dark:text-[#F8FAFC] mb-1">Lampiran Bukti (Gambar)</label>
                        <input type="file" name="lampiran" required accept="image/*" class="w-full text-sm text-slate-500 dark:text-[#F8FAFC]/70 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-blue-50 dark:bg-[#1E3A8A]/30 file:text-blue-600 dark:text-[#0062FF] hover:file:bg-blue-100">
                    </div>

                    <button type="submit" class="w-full bg-[#0062FF] hover:bg-blue-700 dark:bg-[#1E3A8A] dark:hover:bg-[#1E3A8A]/80 text-white font-medium py-2.5 px-4 rounded-xl transition-colors text-sm">
                        Kirim Pengajuan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Riwayat Pengajuan -->
    <div class="lg:col-span-2">
        <div class="bg-white dark:bg-[#0F172A] rounded-xl border border-gray-200 dark:border-[#F8FAFC] p-6 shadow-sm">
            <h3 class="font-bold text-lg mb-4">Riwayat Pengajuan</h3>
            
            @if(count($riwayatIzin) == 0)
                <div class="text-center py-8">
                    <p class="text-slate-500 dark:text-[#F8FAFC]/70">Anda belum pernah mengajukan izin.</p>
                </div>
            @else
                <div class="space-y-4">
                    @foreach($riwayatIzin as $i)
                    <div class="border border-gray-200 dark:border-[#F8FAFC] rounded-xl p-4 flex flex-col sm:flex-row gap-4 justify-between items-start">
                        <div>
                            <div class="flex items-center gap-3 mb-2">
                                <span class="px-2.5 py-1 rounded-full text-xs font-medium {{ $i->tipe_izin == 'sakit' ? 'bg-red-100 text-red-700' : 'bg-orange-100 text-orange-700' }}">
                                    {{ ucfirst($i->tipe_izin) }}
                                </span>
                                <span class="text-sm font-medium text-slate-900 dark:text-[#F8FAFC]">{{ \Carbon\Carbon::parse($i->tanggal)->locale('id')->isoFormat('D MMMM Y') }}</span>
                            </div>
                            <p class="text-sm text-slate-600 dark:text-[#F8FAFC]/80">{{ $i->alasan }}</p>
                        </div>
                        <div class="flex flex-col items-end gap-2 shrink-0 w-full sm:w-auto">
                            <span class="px-3 py-1 rounded-full text-xs font-medium uppercase tracking-wider
                                {{ $i->status_persetujuan == 'disetujui' ? 'bg-green-100 text-green-700' : 
                                   ($i->status_persetujuan == 'ditolak' ? 'bg-red-100 text-red-700' : 'bg-slate-100 dark:bg-[#1E3A8A] text-slate-900 dark:text-[#F8FAFC]') }}">
                                {{ $i->status_persetujuan }}
                            </span>
                            @if($i->status_persetujuan == 'menunggu')
                                <form action="{{ route('mahasiswa.izin.batal', $i->id) }}" method="POST" onsubmit="return confirm('Batalkan pengajuan ini?');" class="w-full sm:w-auto mt-2 sm:mt-0">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="w-full sm:w-auto text-xs font-medium text-red-600 hover:text-red-800 bg-red-50 hover:bg-red-100 px-3 py-1.5 rounded-lg transition-colors">
                                        Batalkan
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
