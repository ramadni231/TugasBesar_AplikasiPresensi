@extends('layouts.admin')

@section('title', 'Detail Jadwal Mengajar')

@section('content')
<div class="mb-6">
    <div class="flex items-center gap-4 mb-6">
        <a href="{{ route('admin.jadwal.index') }}" class="p-2 bg-white dark:bg-[#0F172A] rounded-xl border border-gray-200 dark:border-[#F8FAFC]/20 text-slate-500 hover:text-blue-600 dark:text-[#F8FAFC]/70 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        </a>
        <div>
            <h2 class="text-2xl font-bold text-slate-900 dark:text-[#F8FAFC]">{{ $jadwal->matakuliah->nama_matkul }}</h2>
            <p class="text-sm font-medium text-slate-500 dark:text-[#F8FAFC]/70 mt-1">{{ $jadwal->matakuliah->kode_matkul }} &bull; {{ $jadwal->matakuliah->sks }} SKS &bull; {{ $jadwal->dosen->nama }}</p>
        </div>
    </div>
</div>

<div class="bg-white dark:bg-[#0F172A] rounded-xl border border-gray-200 dark:border-[#F8FAFC] shadow-sm overflow-hidden" x-data="{ 
    showModal: false, 
    form: {
        pertemuan_ke: '',
        tanggal: '',
        jam_mulai: '',
        jam_selesai: '',
        ruangan_id: ''
    },
    openRescheduleModal(p) {
        this.form.pertemuan_ke = p.pertemuan_ke;
        this.form.tanggal = p.tanggal;
        this.form.jam_mulai = p.jam_mulai;
        this.form.jam_selesai = p.jam_selesai;
        this.form.ruangan_id = p.ruangan_id;
        this.showModal = true;
    }
}">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50 dark:bg-[#1E293B] border-b border-gray-200 dark:border-[#F8FAFC]/20">
                    <th class="px-6 py-4 font-bold text-sm text-slate-900 dark:text-[#F8FAFC]">Pertemuan</th>
                    <th class="px-6 py-4 font-bold text-sm text-slate-900 dark:text-[#F8FAFC]">Tanggal</th>
                    <th class="px-6 py-4 font-bold text-sm text-slate-900 dark:text-[#F8FAFC]">Jam & Ruangan</th>
                    <th class="px-6 py-4 font-bold text-sm text-slate-900 dark:text-[#F8FAFC]">Status Reschedule</th>
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
                    <td class="px-6 py-4 text-sm">
                        @if($p['sesi_id'])
                            <span class="px-2.5 py-1 bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400 rounded-full text-xs font-medium">Rescheduled</span>
                        @else
                            <span class="text-slate-400 dark:text-[#F8FAFC]/50 text-sm">Reguler</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-right">
                        <button type="button" @click='openRescheduleModal(@json($p))' class="px-4 py-2 bg-slate-100 hover:bg-slate-200 dark:bg-[#1E293B] dark:hover:bg-[#334155] text-slate-700 dark:text-[#F8FAFC] rounded-lg text-sm font-medium transition-colors shadow-sm">
                            Ubah Sesi
                        </button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Modal Reschedule -->
    <div x-show="showModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
            <div x-show="showModal" x-transition.opacity class="fixed inset-0 transition-opacity bg-slate-900/50 backdrop-blur-sm" @click="showModal = false"></div>

            <div x-show="showModal" x-transition class="relative inline-block w-full max-w-md p-6 overflow-hidden text-left align-middle transition-all transform bg-white dark:bg-[#0F172A] border border-gray-200 dark:border-[#F8FAFC] shadow-xl rounded-2xl">
                <h3 class="text-lg font-bold text-slate-900 dark:text-[#F8FAFC] mb-4">
                    Reschedule Pertemuan <span x-text="form.pertemuan_ke"></span>
                </h3>
                
                <form action="{{ route('admin.jadwal.reschedule', $jadwal->id) }}" method="POST">
                    @csrf
                    <input type="hidden" name="pertemuan_ke" x-model="form.pertemuan_ke">
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-900 dark:text-[#F8FAFC] mb-1">Tanggal</label>
                            <input type="date" name="tanggal" x-model="form.tanggal" required class="w-full rounded-xl border-gray-200 dark:border-[#F8FAFC]/20 bg-slate-50 dark:bg-[#1E293B] text-sm px-4 py-2 focus:ring-blue-600 focus:border-blue-600 dark:focus:ring-[#1E3A8A] dark:text-[#F8FAFC]">
                        </div>
                        
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-slate-900 dark:text-[#F8FAFC] mb-1">Jam Mulai</label>
                                <input type="time" name="jam_mulai" x-model="form.jam_mulai" required class="w-full rounded-xl border-gray-200 dark:border-[#F8FAFC]/20 bg-slate-50 dark:bg-[#1E293B] text-sm px-4 py-2 focus:ring-blue-600 focus:border-blue-600 dark:focus:ring-[#1E3A8A] dark:text-[#F8FAFC]">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-900 dark:text-[#F8FAFC] mb-1">Jam Selesai</label>
                                <input type="time" name="jam_selesai" x-model="form.jam_selesai" required class="w-full rounded-xl border-gray-200 dark:border-[#F8FAFC]/20 bg-slate-50 dark:bg-[#1E293B] text-sm px-4 py-2 focus:ring-blue-600 focus:border-blue-600 dark:focus:ring-[#1E3A8A] dark:text-[#F8FAFC]">
                            </div>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-slate-900 dark:text-[#F8FAFC] mb-1">Ruangan</label>
                            <select name="ruangan_id" x-model="form.ruangan_id" required class="w-full rounded-xl border-gray-200 dark:border-[#F8FAFC]/20 bg-slate-50 dark:bg-[#1E293B] text-sm px-4 py-2 focus:ring-blue-600 focus:border-blue-600 dark:focus:ring-[#1E3A8A] dark:text-[#F8FAFC]">
                                @foreach($ruangan as $r)
                                    <option value="{{ $r->id }}">{{ $r->nama_ruangan }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end gap-3">
                        <button type="button" @click="showModal = false" class="bg-white dark:bg-[#0F172A] border border-gray-200 dark:border-[#F8FAFC] text-slate-900 dark:text-[#F8FAFC] hover:bg-slate-50 dark:hover:bg-[#1E293B] px-4 py-2 rounded-xl text-sm font-medium transition-colors">
                            Batal
                        </button>
                        <button type="submit" class="bg-[#0062FF] hover:bg-blue-700 dark:bg-[#1E3A8A] dark:hover:bg-[#1E3A8A]/80 text-white px-4 py-2 rounded-xl text-sm font-medium transition-colors">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
