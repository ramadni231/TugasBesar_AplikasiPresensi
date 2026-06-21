@extends('layouts.admin')

@section('title', 'Manajemen Jadwal')

@section('content')
<div x-data="{ 
    search: '',
    showModal: false, 
    editMode: false,
    form: {
        id: '',
        matakuliah_id: '',
        ruangan_id: '',
        dosen_id: '',
        hari: 'Senin',
        jam_mulai: '',
        jam_selesai: '',
        metode: 'luring'
    },
    openModal(jadwal = null) {
        if (jadwal) {
            this.editMode = true;
            this.form.id = jadwal.id;
            this.form.matakuliah_id = jadwal.matakuliah_id;
            this.form.ruangan_id = jadwal.ruangan_id;
            this.form.dosen_id = jadwal.dosen_id;
            this.form.hari = jadwal.hari;
            this.form.jam_mulai = jadwal.jam_mulai;
            this.form.jam_selesai = jadwal.jam_selesai;
            this.form.metode = jadwal.metode || 'luring';
        } else {
            this.editMode = false;
            this.form.id = '';
            this.form.matakuliah_id = '';
            this.form.ruangan_id = '';
            this.form.dosen_id = '';
            this.form.hari = 'Senin';
            this.form.jam_mulai = '';
            this.form.jam_selesai = '';
            this.form.metode = 'luring';
        }
        this.showModal = true;
    }
}">

    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
        <h2 class="text-2xl font-bold">Manajemen Jadwal Kelas</h2>
        <div class="flex items-center gap-3 w-full sm:w-auto">
            <div class="relative flex-1 sm:w-64">
                <input x-model="search" type="text" placeholder="Cari jadwal/matkul/dosen..." class="w-full rounded-xl border border-gray-200 dark:border-[#F8FAFC]/20 bg-white dark:bg-[#0F172A] text-sm px-4 py-2 pl-10 focus:ring-blue-600 focus:border-blue-600 dark:focus:ring-[#1E3A8A] dark:text-[#F8FAFC]">
                <svg class="w-4 h-4 absolute left-3.5 top-3 text-slate-400 dark:text-[#F8FAFC]/60" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>
            <button @click="openModal()" class="bg-blue-600 hover:bg-blue-700 dark:bg-blue-600 dark:hover:bg-blue-500 text-white px-4 py-2 rounded-xl text-sm font-medium transition-colors flex items-center gap-2 whitespace-nowrap">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Tambah Jadwal
            </button>
        </div>
    </div>

    <!-- Grid List Jadwal -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($jadwal as $j)
        <div onclick="window.location.href='{{ route('admin.jadwal.detail', $j->id) }}'" x-show="search === '' || '{{ strtolower($j->matakuliah->nama_matkul) }}'.includes(search.toLowerCase()) || '{{ strtolower($j->dosen->nama) }}'.includes(search.toLowerCase()) || '{{ strtolower($j->hari) }}'.includes(search.toLowerCase())" class="bg-white dark:bg-[#0F172A] rounded-xl border border-gray-200 dark:border-[#F8FAFC] p-6 flex flex-col hover:border-blue-500 dark:hover:border-blue-500 hover:shadow-md transition-all relative overflow-hidden cursor-pointer">
            <div class="absolute top-0 right-0 bg-blue-600 dark:bg-[#0062FF] text-white px-5 py-2.5 rounded-bl-xl text-base font-bold shadow-sm">
                {{ $j->hari }}
            </div>
            
            <div class="mb-4 pt-2">
                <h3 class="font-bold text-slate-900 dark:text-[#F8FAFC] text-xl">{{ $j->matakuliah->nama_matkul }}</h3>
                <p class="text-base font-bold text-blue-600 dark:text-blue-400 mt-1">{{ $j->matakuliah->kode_matkul }} &bull; {{ $j->matakuliah->sks }} SKS</p>
            </div>
            
            <div class="bg-white/50 dark:bg-[#1E293B]/50 p-3 rounded-lg text-sm text-slate-600 dark:text-[#F8FAFC]/80 mb-4 flex-1 space-y-3">
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5 text-slate-400 dark:text-[#F8FAFC]/60" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span class="font-medium text-slate-900 dark:text-[#F8FAFC]">{{ substr($j->jam_mulai, 0, 5) }} - {{ substr($j->jam_selesai, 0, 5) }}</span>
                </div>
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5 text-slate-400 dark:text-[#F8FAFC]/60" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    <span class="font-medium text-slate-900 dark:text-[#F8FAFC]">{{ $j->ruangan->nama_ruangan }}</span>
                </div>
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5 text-slate-400 dark:text-[#F8FAFC]/60" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    <span class="font-medium text-slate-900 dark:text-[#F8FAFC]">{{ $j->dosen->nama }}</span>
                </div>
            </div>

            <div class="flex items-center justify-between mt-2 pt-4 border-t border-slate-100 dark:border-slate-700/50">
                <span class="px-3 py-1.5 bg-slate-200 dark:bg-slate-700 text-slate-800 dark:text-slate-200 rounded-md text-sm font-bold uppercase tracking-wider">
                    {{ $j->metode }}
                </span>
                <div class="flex items-center gap-2">
                    <button @click='event.stopPropagation(); openModal({!! json_encode($j, JSON_HEX_APOS | JSON_HEX_QUOT) !!})' class="text-blue-600 dark:text-[#0062FF] hover:text-blue-800 p-2 rounded-lg hover:bg-blue-50 dark:hover:bg-[#1E3A8A]/30 transition-colors" title="Edit Master Jadwal">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    </button>
                    <form action="{{ route('admin.jadwal.destroy', $j->id) }}" method="POST" onsubmit="event.stopPropagation(); return confirm('Yakin ingin menghapus jadwal ini?');" onclick="event.stopPropagation();" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-500 hover:text-red-700 p-2 rounded-lg hover:bg-red-50 dark:hover:bg-red-900/30 transition-colors" title="Hapus Jadwal">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Modal Form -->
    <div x-show="showModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="showModal" x-transition.opacity class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm transition-opacity" @click="showModal = false"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div x-show="showModal" x-transition.scale class="relative z-10 inline-block align-bottom bg-white dark:bg-[#0F172A] rounded-xl text-left overflow-hidden shadow-2xl border border-blue-500/30 transform transition-all sm:my-8 sm:align-middle sm:max-w-xl w-full">
                <form :action="editMode ? '/admin/jadwal/' + form.id : '{{ route('admin.jadwal.store') }}'" method="POST">
                    @csrf
                    <input type="hidden" name="_method" value="PUT" x-bind:disabled="!editMode">
                    
                    <div class="bg-transparent px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <h3 class="text-lg leading-6 font-medium text-slate-900 dark:text-[#F8FAFC] mb-4" id="modal-title" x-text="editMode ? 'Edit Jadwal' : 'Tambah Jadwal'"></h3>
                        
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-slate-900 dark:text-[#F8FAFC] mb-1">Mata Kuliah</label>
                                <select name="matakuliah_id" x-model="form.matakuliah_id" required class="w-full rounded-xl border-gray-200 dark:border-[#F8FAFC] focus:ring-blue-600 dark:focus:ring-[#1E3A8A] focus:border-blue-600 dark:focus:border-[#1E3A8A] px-4 py-2 border bg-slate-50 dark:bg-[#0F172A] text-slate-900 dark:text-[#F8FAFC]">
                                    <option value="">-- Pilih Mata Kuliah --</option>
                                    @foreach($matakuliah as $m)
                                        <option value="{{ $m->id }}">{{ $m->kode_matkul }} - {{ $m->nama_matkul }}</option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-slate-900 dark:text-[#F8FAFC] mb-1">Dosen Pengampu</label>
                                <select name="dosen_id" x-model="form.dosen_id" required class="w-full rounded-xl border-gray-200 dark:border-[#F8FAFC] focus:ring-blue-600 dark:focus:ring-[#1E3A8A] focus:border-blue-600 dark:focus:border-[#1E3A8A] px-4 py-2 border bg-slate-50 dark:bg-[#0F172A] text-slate-900 dark:text-[#F8FAFC]">
                                    <option value="">-- Pilih Dosen --</option>
                                    @foreach($dosen as $d)
                                        <option value="{{ $d->id }}">{{ $d->nama }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-slate-900 dark:text-[#F8FAFC] mb-1">Ruangan</label>
                                    <select name="ruangan_id" x-model="form.ruangan_id" required class="w-full rounded-xl border-gray-200 dark:border-[#F8FAFC] focus:ring-blue-600 dark:focus:ring-[#1E3A8A] focus:border-blue-600 dark:focus:border-[#1E3A8A] px-4 py-2 border bg-slate-50 dark:bg-[#0F172A] text-slate-900 dark:text-[#F8FAFC]">
                                        <option value="">-- Pilih Ruangan --</option>
                                        @foreach($ruangan as $r)
                                            <option value="{{ $r->id }}">{{ $r->nama_ruangan }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-slate-900 dark:text-[#F8FAFC] mb-1">Metode</label>
                                    <select name="metode" x-model="form.metode" required class="w-full rounded-xl border-gray-200 dark:border-[#F8FAFC] focus:ring-blue-600 dark:focus:ring-[#1E3A8A] focus:border-blue-600 dark:focus:border-[#1E3A8A] px-4 py-2 border bg-slate-50 dark:bg-[#0F172A] text-slate-900 dark:text-[#F8FAFC]">
                                        <option value="luring">Luring</option>
                                        <option value="daring">Daring</option>
                                    </select>
                                </div>
                            </div>

                            <div class="grid grid-cols-3 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-slate-900 dark:text-[#F8FAFC] mb-1">Hari</label>
                                    <select name="hari" x-model="form.hari" required class="w-full rounded-xl border-gray-200 dark:border-[#F8FAFC] focus:ring-blue-600 dark:focus:ring-[#1E3A8A] focus:border-blue-600 dark:focus:border-[#1E3A8A] px-4 py-2 border bg-slate-50 dark:bg-[#0F172A] text-slate-900 dark:text-[#F8FAFC]">
                                        <option value="Senin">Senin</option>
                                        <option value="Selasa">Selasa</option>
                                        <option value="Rabu">Rabu</option>
                                        <option value="Kamis">Kamis</option>
                                        <option value="Jumat">Jumat</option>
                                        <option value="Sabtu">Sabtu</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-slate-900 dark:text-[#F8FAFC] mb-1">Jam Mulai</label>
                                    <input type="time" name="jam_mulai" x-model="form.jam_mulai" required class="w-full rounded-xl border-gray-200 dark:border-[#F8FAFC] focus:ring-blue-600 dark:focus:ring-[#1E3A8A] focus:border-blue-600 dark:focus:border-[#1E3A8A] px-4 py-2 border bg-slate-50 dark:bg-[#0F172A] text-slate-900 dark:text-[#F8FAFC]">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-slate-900 dark:text-[#F8FAFC] mb-1">Jam Selesai</label>
                                    <input type="time" name="jam_selesai" x-model="form.jam_selesai" required class="w-full rounded-xl border-gray-200 dark:border-[#F8FAFC] focus:ring-blue-600 dark:focus:ring-[#1E3A8A] focus:border-blue-600 dark:focus:border-[#1E3A8A] px-4 py-2 border bg-slate-50 dark:bg-[#0F172A] text-slate-900 dark:text-[#F8FAFC]">
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-transparent px-4 py-3 sm:px-6 flex justify-end gap-3 rounded-b-xl border-t border-gray-200/50 dark:border-white/10">
                        <button type="button" @click="showModal = false" class="bg-transparent border border-gray-300 dark:border-slate-500 text-slate-700 dark:text-slate-200 hover:bg-gray-100 dark:hover:bg-slate-800 px-4 py-2 rounded-xl text-sm font-medium transition-colors">
                            Batal
                        </button>
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 dark:bg-blue-600 dark:hover:bg-blue-500 text-white px-4 py-2 rounded-xl text-sm font-medium transition-colors">
                            Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
