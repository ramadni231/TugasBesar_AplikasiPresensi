@extends('layouts.dosen')

@section('title', 'Detail Jadwal Mengajar')

@section('content')
@php
    $activeSesi = collect($listPertemuan)->firstWhere('is_sesi_aktif', true);
    $autoShowQr = session('success') && $activeSesi ? 'true' : 'false';
    $autoQrToken = $activeSesi ? $activeSesi['token_qr'] : '';
    $autoPertemuan = $activeSesi ? $activeSesi['pertemuan_ke'] : '';
@endphp
<div x-data="{ showQrModal: {{ $autoShowQr }}, activeQrToken: '{{ $autoQrToken }}', activePertemuan: '{{ $autoPertemuan }}' }" x-init="if(showQrModal) { $nextTick(() => generateQr(activeQrToken)) }">
<div class="mb-6">
    <div class="flex items-center gap-4 mb-6">
        <a href="{{ route('dosen.jadwal') }}" class="p-2 bg-white dark:bg-[#0F172A] rounded-xl border border-gray-200 dark:border-[#F8FAFC]/20 text-slate-500 hover:text-blue-600 dark:text-[#F8FAFC]/70 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        </a>
        <div>
            <h2 class="text-2xl font-bold text-slate-900 dark:text-[#F8FAFC]">{{ $jadwal->matakuliah->nama_matkul }}</h2>
            <p class="text-sm font-medium text-slate-500 dark:text-[#F8FAFC]/70 mt-1">{{ $jadwal->matakuliah->kode_matkul }} &bull; {{ $jadwal->matakuliah->sks }} SKS &bull; {{ $jadwal->ruangan->nama_ruangan }}</p>
        </div>
    </div>
</div>

<div class="bg-white dark:bg-[#0F172A] rounded-xl border border-gray-200 dark:border-[#F8FAFC] shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50 dark:bg-[#1E293B] border-b border-gray-200 dark:border-[#F8FAFC]/20">
                    <th class="px-6 py-4 font-bold text-sm text-slate-900 dark:text-[#F8FAFC]">Pertemuan</th>
                    <th class="px-6 py-4 font-bold text-sm text-slate-900 dark:text-[#F8FAFC]">Tanggal</th>
                    <th class="px-6 py-4 font-bold text-sm text-slate-900 dark:text-[#F8FAFC]">Jam & Ruangan</th>
                    <th class="px-6 py-4 font-bold text-sm text-slate-900 dark:text-[#F8FAFC] text-center">Statistik</th>
                    <th class="px-6 py-4 font-bold text-sm text-slate-900 dark:text-[#F8FAFC] text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-[#F8FAFC]/20">
                @foreach($listPertemuan as $p)
                <tr class="hover:bg-slate-50/50 dark:hover:bg-[#1E3A8A]/10 transition-colors {{ \Carbon\Carbon::parse($p['tanggal'])->isToday() ? 'bg-blue-50/30 dark:bg-blue-900/10' : '' }}">
                    <td class="px-6 py-4">
                        <span class="font-semibold text-slate-900 dark:text-[#F8FAFC]">{{ $p['label'] }}</span>
                        @if(\Carbon\Carbon::parse($p['tanggal'])->isToday())
                        <span class="ml-2 px-2 py-0.5 bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-300 rounded text-xs font-bold">HARI INI</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-sm text-slate-600 dark:text-[#F8FAFC]/80">
                        {{ \Carbon\Carbon::parse($p['tanggal'])->locale('id')->isoFormat('D MMMM YYYY') }}
                    </td>
                    <td class="px-6 py-4 text-sm text-slate-600 dark:text-[#F8FAFC]/80">
                        <div class="font-medium">{{ $p['jam_mulai'] }} - {{ $p['jam_selesai'] }}</div>
                        <div class="text-xs mt-1 text-slate-400">{{ $p['ruangan_nama'] }}</div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex justify-center gap-3 text-xs">
                            <span class="text-green-600 dark:text-green-400 font-medium" title="Hadir">{{ $p['jumlah_hadir'] }} H</span>
                            <span class="text-red-600 dark:text-red-400 font-medium" title="Alpa">{{ $p['jumlah_alpa'] }} A</span>
                            <span class="text-yellow-600 dark:text-yellow-400 font-medium" title="Izin/Sakit">{{ $p['jumlah_izin'] + $p['jumlah_sakit'] }} I/S</span>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-right">
                        @if($p['is_sesi_aktif'])
                            <button @click="activeQrToken = '{{ $p['token_qr'] }}'; activePertemuan = '{{ $p['pertemuan_ke'] }}'; showQrModal = true; setTimeout(() => generateQr('{{ $p['token_qr'] }}'), 100)" class="px-4 py-2 bg-blue-100 text-blue-700 hover:bg-blue-200 dark:bg-blue-900/50 dark:text-blue-300 dark:hover:bg-blue-800/50 rounded-lg text-sm font-semibold transition-colors mr-2 shadow-sm">
                                Tampilkan QR
                            </button>
                            <form action="{{ route('dosen.sesi.hentikan', $p['sesi_aktif_id']) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white rounded-lg text-sm font-semibold transition-colors shadow-sm">
                                    Hentikan Sesi
                                </button>
                            </form>
                        @elseif(\Carbon\Carbon::parse($p['tanggal'])->isToday())
                            <form action="{{ route('dosen.sesi.aktifkan') }}" method="POST" class="inline">
                                @csrf
                                <input type="hidden" name="jadwal_id" value="{{ $jadwal->id }}">
                                <input type="hidden" name="pertemuan_ke" value="{{ $p['pertemuan_ke'] }}">
                                <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-semibold transition-colors shadow-sm">
                                    Buka Presensi
                                </button>
                            </form>
                        @elseif(\Carbon\Carbon::parse($p['tanggal'])->isPast())
                            <span class="text-slate-400 dark:text-[#F8FAFC]/50 text-sm font-medium">Selesai</span>
                        @else
                            <span class="text-slate-400 dark:text-[#F8FAFC]/50 text-sm font-medium">Belum Mulai</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<!-- QR Modal -->
    <div x-show="showQrModal" style="{{ $autoShowQr === 'true' ? '' : 'display: none;' }}" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="showQrModal" x-transition.opacity class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm transition-opacity" @click="showQrModal = false"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div x-show="showQrModal" x-transition class="relative z-10 inline-block align-bottom bg-white dark:bg-[#0F172A] rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md w-full border border-gray-200 dark:border-[#F8FAFC]">
                <div class="bg-blue-600 dark:bg-[#1E3A8A] px-6 py-4 flex justify-between items-center">
                    <h3 class="text-lg font-bold text-white">Scan QR Code Presensi</h3>
                    <button @click="showQrModal = false" class="text-white/80 hover:text-white">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                
                <div class="p-8 flex flex-col items-center">
                    <div class="text-center mb-6">
                        <h4 class="text-xl font-bold text-slate-900 dark:text-[#F8FAFC]">{{ $jadwal->matakuliah->nama_matkul }}</h4>
                        <p class="text-sm font-medium text-slate-500 dark:text-[#F8FAFC]/70 mt-1">Pertemuan <span x-text="activePertemuan"></span> &bull; {{ $jadwal->ruangan->nama_ruangan }}</p>
                    </div>

                    <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 flex items-center justify-center">
                        <div id="qrcode"></div>
                    </div>
                    
                    <p class="text-sm text-center text-slate-500 dark:text-[#F8FAFC]/70 mt-6 max-w-xs mx-auto">
                        Minta mahasiswa untuk menscan QR Code ini menggunakan aplikasi presensi kampus.
                    </p>
                </div>
                <div class="bg-gray-50 dark:bg-[#1E293B] px-6 py-4 flex justify-center border-t border-gray-100 dark:border-[#F8FAFC]/10">
                    <button @click="showQrModal = false" class="px-6 py-2.5 bg-slate-200 hover:bg-slate-300 dark:bg-slate-700 dark:hover:bg-slate-600 text-slate-800 dark:text-[#F8FAFC] font-medium rounded-xl transition-colors">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal QR was moved here inside the wrapper -->
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<script>
    function generateQr(token) {
        document.getElementById('qrcode').innerHTML = "";
        new QRCode(document.getElementById("qrcode"), {
            text: token,
            width: 250,
            height: 250,
            colorDark : "#0F172A",
            colorLight : "#ffffff",
            correctLevel : QRCode.CorrectLevel.H
        });
    }
</script>
@endsection
