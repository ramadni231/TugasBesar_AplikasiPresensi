@extends('layouts.admin')

@section('title', 'Manajemen Ruangan')

@section('content')
<div x-data="{ 
    search: '',
    showModal: false, 
    editMode: false,
    form: {
        id: '',
        nama_ruangan: '',
        kapasitas: '',
        latitude: '',
        longitude: '',
        radius_meter: ''
    },
    openModal(ruangan = null) {
        if (ruangan) {
            this.editMode = true;
            this.form.id = ruangan.id;
            this.form.nama_ruangan = ruangan.nama_ruangan;
            this.form.kapasitas = ruangan.kapasitas;
            this.form.latitude = ruangan.latitude;
            this.form.longitude = ruangan.longitude;
            this.form.radius_meter = ruangan.radius_meter;
        } else {
            this.editMode = false;
            this.form.id = '';
            this.form.nama_ruangan = '';
            this.form.kapasitas = '';
            this.form.latitude = '';
            this.form.longitude = '';
            this.form.radius_meter = '';
        }
        this.showModal = true;
    }
}">

    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
        <h2 class="text-2xl font-bold">Manajemen Ruangan</h2>
        <div class="flex items-center gap-3 w-full sm:w-auto">
            <div class="relative flex-1 sm:w-64">
                <input x-model="search" type="text" placeholder="Cari ruangan..." class="w-full rounded-xl border border-gray-200 dark:border-[#F8FAFC]/20 bg-white dark:bg-[#0F172A] text-sm px-4 py-2 pl-10 focus:ring-blue-600 focus:border-blue-600 dark:focus:ring-[#1E3A8A] dark:text-[#F8FAFC]">
                <svg class="w-4 h-4 absolute left-3.5 top-3 text-slate-400 dark:text-[#F8FAFC]/60" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>
            <button @click="openModal()" class="bg-blue-600 hover:bg-blue-700 dark:bg-blue-600 dark:hover:bg-blue-500 text-white px-4 py-2 rounded-xl text-sm font-medium transition-colors flex items-center gap-2 whitespace-nowrap">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Tambah Ruangan
            </button>
        </div>
    </div>

    <!-- Grid List Ruangan -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($ruangan as $r)
        <div x-show="search === '' || '{{ strtolower($r->nama_ruangan) }}'.includes(search.toLowerCase())" class="bg-white dark:bg-[#0F172A] rounded-xl border border-gray-200 dark:border-[#F8FAFC] p-6 flex flex-col hover:border-blue-500 dark:hover:border-blue-500 hover:shadow-md transition-all">
            <div class="flex items-center gap-3 mb-4">
                <div class="p-3 bg-purple-100 text-purple-600 rounded-lg">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                </div>
                <div>
                    <h3 class="font-bold text-slate-900 dark:text-[#F8FAFC] text-lg">{{ $r->nama_ruangan }}</h3>
                    <p class="text-sm font-medium text-slate-600 dark:text-[#F8FAFC]/80">Kapasitas: {{ $r->kapasitas }} Orang</p>
                </div>
            </div>
            
            <div class="bg-white/50 dark:bg-[#1E293B]/50 p-3 rounded-lg text-sm text-slate-600 dark:text-[#F8FAFC]/80 mb-4 flex-1 space-y-2">
                <div class="flex justify-between">
                    <span class="text-slate-400 dark:text-[#F8FAFC]/60">Lat:</span>
                    <span class="font-medium text-slate-900 dark:text-[#F8FAFC]">{{ $r->latitude }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-400 dark:text-[#F8FAFC]/60">Long:</span>
                    <span class="font-medium text-slate-900 dark:text-[#F8FAFC]">{{ $r->longitude }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-400 dark:text-[#F8FAFC]/60">Radius:</span>
                    <span class="font-medium text-slate-900 dark:text-[#F8FAFC]">{{ $r->radius_meter }} Meter</span>
                </div>
            </div>

            <div class="flex items-center justify-end gap-2 mt-2">
                <button @click='openModal({!! json_encode($r, JSON_HEX_APOS | JSON_HEX_QUOT) !!})' class="text-blue-600 dark:text-[#0062FF] hover:text-blue-800 p-2 rounded-lg hover:bg-blue-50 dark:bg-[#1E3A8A]/30 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                </button>
                <form action="{{ route('admin.ruangan.destroy', $r->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus ruangan ini?');" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-red-600 hover:text-red-800 p-2 rounded-lg hover:bg-red-50 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    </button>
                </form>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Modal Form -->
    <div x-show="showModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="showModal" x-transition.opacity class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm transition-opacity" @click="showModal = false"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div x-show="showModal" x-transition.scale class="relative z-10 inline-block align-bottom bg-white/95 dark:bg-[#1E293B]/95 backdrop-blur-xl rounded-xl text-left overflow-hidden shadow-2xl border border-white/20 dark:border-[#F8FAFC]/20 transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
                <form :action="editMode ? '/admin/ruangan/' + form.id : '{{ route('admin.ruangan.store') }}'" method="POST">
                    @csrf
                    <input type="hidden" name="_method" value="PUT" x-bind:disabled="!editMode">
                    
                    <div class="bg-transparent px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <h3 class="text-lg leading-6 font-medium text-slate-900 dark:text-[#F8FAFC] mb-4" id="modal-title" x-text="editMode ? 'Edit Ruangan' : 'Tambah Ruangan'"></h3>
                        
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-slate-900 dark:text-[#F8FAFC] mb-1">Nama Ruangan</label>
                                <input type="text" name="nama_ruangan" x-model="form.nama_ruangan" required class="w-full rounded-xl border-gray-200 dark:border-[#F8FAFC] focus:ring-blue-600 dark:focus:ring-[#1E3A8A] focus:border-blue-600 dark:focus:border-[#1E3A8A] px-4 py-2 border bg-slate-50 dark:bg-[#0F172A] text-slate-900 dark:text-[#F8FAFC]">
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-slate-900 dark:text-[#F8FAFC] mb-1">Kapasitas (Orang)</label>
                                <input type="number" name="kapasitas" x-model="form.kapasitas" required class="w-full rounded-xl border-gray-200 dark:border-[#F8FAFC] focus:ring-blue-600 dark:focus:ring-[#1E3A8A] focus:border-blue-600 dark:focus:border-[#1E3A8A] px-4 py-2 border bg-slate-50 dark:bg-[#0F172A] text-slate-900 dark:text-[#F8FAFC]">
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-slate-900 dark:text-[#F8FAFC] mb-1">Latitude</label>
                                    <input type="number" step="any" name="latitude" x-model="form.latitude" required class="w-full rounded-xl border-gray-200 dark:border-[#F8FAFC] focus:ring-blue-600 dark:focus:ring-[#1E3A8A] focus:border-blue-600 dark:focus:border-[#1E3A8A] px-4 py-2 border bg-slate-50 dark:bg-[#0F172A] text-slate-900 dark:text-[#F8FAFC]">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-slate-900 dark:text-[#F8FAFC] mb-1">Longitude</label>
                                    <input type="number" step="any" name="longitude" x-model="form.longitude" required class="w-full rounded-xl border-gray-200 dark:border-[#F8FAFC] focus:ring-blue-600 dark:focus:ring-[#1E3A8A] focus:border-blue-600 dark:focus:border-[#1E3A8A] px-4 py-2 border bg-slate-50 dark:bg-[#0F172A] text-slate-900 dark:text-[#F8FAFC]">
                                </div>
                            </div>
                            
                            <button type="button" @click="navigator.geolocation.getCurrentPosition(pos => { form.latitude = pos.coords.latitude; form.longitude = pos.coords.longitude; }, err => alert('Gagal mengambil lokasi GPS'))" class="w-full bg-slate-100 hover:bg-slate-200 dark:bg-white/50 dark:bg-[#1E293B]/50 dark:hover:bg-blue-50 dark:bg-[#1E3A8A]/30 text-slate-700 dark:text-slate-900 dark:text-[#F8FAFC] px-4 py-2 rounded-xl text-sm font-medium transition-colors flex items-center justify-center gap-2 border border-slate-200 dark:border-[#F8FAFC]/20">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                Ambil GPS Lokasi Saat Ini
                            </button>

                            <div>
                                <label class="block text-sm font-medium text-slate-900 dark:text-[#F8FAFC] mb-1">Radius Scan (Meter)</label>
                                <input type="number" name="radius_meter" x-model="form.radius_meter" required class="w-full rounded-xl border-gray-200 dark:border-[#F8FAFC] focus:ring-blue-600 dark:focus:ring-[#1E3A8A] focus:border-blue-600 dark:focus:border-[#1E3A8A] px-4 py-2 border bg-slate-50 dark:bg-[#0F172A] text-slate-900 dark:text-[#F8FAFC]">
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
