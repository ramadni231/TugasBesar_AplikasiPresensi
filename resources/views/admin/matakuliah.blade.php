@extends('layouts.admin')

@section('title', 'Manajemen Matakuliah')

@section('content')
<div x-data="{ 
    search: '',
    showModal: false, 
    editMode: false,
    form: {
        id: '',
        kode_matkul: '',
        nama_matkul: '',
        sks: ''
    },
    openModal(matkul = null) {
        if (matkul) {
            this.editMode = true;
            this.form.id = matkul.id;
            this.form.kode_matkul = matkul.kode_matkul;
            this.form.nama_matkul = matkul.nama_matkul;
            this.form.sks = matkul.sks;
        } else {
            this.editMode = false;
            this.form.id = '';
            this.form.kode_matkul = '';
            this.form.nama_matkul = '';
            this.form.sks = '';
        }
        this.showModal = true;
    }
}">

    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
        <h2 class="text-2xl font-bold">Manajemen Mata Kuliah</h2>
        <div class="flex items-center gap-3 w-full sm:w-auto">
            <div class="relative flex-1 sm:w-64">
                <input x-model="search" type="text" placeholder="Cari matakuliah..." class="w-full rounded-xl border border-gray-200 dark:border-[#F8FAFC]/20 bg-white dark:bg-[#0F172A] text-sm px-4 py-2 pl-10 focus:ring-blue-600 focus:border-blue-600 dark:focus:ring-[#1E3A8A] dark:text-[#F8FAFC]">
                <svg class="w-4 h-4 absolute left-3.5 top-3 text-slate-400 dark:text-[#F8FAFC]/60" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>
            <button @click="openModal()" class="bg-blue-600 hover:bg-blue-700 dark:bg-blue-600 dark:hover:bg-blue-500 text-white px-4 py-2 rounded-xl text-sm font-medium transition-colors flex items-center gap-2 whitespace-nowrap">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Tambah
            </button>
        </div>
    </div>

    <!-- Grid List Matakuliah -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($matakuliah as $m)
        <div x-show="search === '' || '{{ strtolower($m->nama_matkul) }}'.includes(search.toLowerCase()) || '{{ strtolower($m->kode_matkul) }}'.includes(search.toLowerCase())" class="bg-white dark:bg-[#0F172A] rounded-xl border border-gray-200 dark:border-[#F8FAFC] p-6 flex flex-col hover:border-blue-500 dark:hover:border-blue-500 hover:shadow-md transition-all">
            <div class="flex items-center gap-3 mb-4">
                <div class="p-3 bg-rose-100 text-rose-600 rounded-lg">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                </div>
                <div>
                    <h3 class="font-bold text-slate-900 dark:text-[#F8FAFC] text-xl">{{ $m->nama_matkul }}</h3>
                    <p class="text-base font-bold text-blue-600 dark:text-[#0062FF] bg-blue-50 dark:bg-[#1E3A8A]/30 inline-block px-4 py-1.5 rounded-full mt-2">{{ $m->kode_matkul }}</p>
                </div>
            </div>
            
            <div class="bg-white/50 dark:bg-[#1E293B]/50 p-3 rounded-lg text-sm text-slate-600 dark:text-[#F8FAFC]/80 mb-4 flex-1">
                <div class="flex justify-between items-center">
                    <span class="text-slate-500 dark:text-[#F8FAFC]/70">Bobot SKS:</span>
                    <span class="font-bold text-slate-900 dark:text-[#F8FAFC] text-2xl">{{ $m->sks }}</span>
                </div>
            </div>

            <div class="flex items-center justify-end gap-2 mt-2">
                <button @click='openModal({!! json_encode($m, JSON_HEX_APOS | JSON_HEX_QUOT) !!})' class="text-blue-600 dark:text-[#0062FF] hover:text-blue-800 p-2 rounded-lg hover:bg-blue-50 dark:bg-[#1E3A8A]/30 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                </button>
                <form action="{{ route('admin.matakuliah.destroy', $m->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus matakuliah ini?');" class="inline">
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
                <form :action="editMode ? '/admin/matakuliah/' + form.id : '{{ route('admin.matakuliah.store') }}'" method="POST">
                    @csrf
                    <input type="hidden" name="_method" value="PUT" x-bind:disabled="!editMode">
                    
                    <div class="bg-transparent px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <h3 class="text-lg leading-6 font-medium text-slate-900 dark:text-[#F8FAFC] mb-4" id="modal-title" x-text="editMode ? 'Edit Matakuliah' : 'Tambah Matakuliah'"></h3>
                        
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-slate-900 dark:text-[#F8FAFC] mb-1">Kode Matakuliah</label>
                                <input type="text" name="kode_matkul" x-model="form.kode_matkul" required class="w-full rounded-xl border-gray-200 dark:border-[#F8FAFC] focus:ring-blue-600 dark:focus:ring-[#1E3A8A] focus:border-blue-600 dark:focus:border-[#1E3A8A] px-4 py-2 border bg-slate-50 dark:bg-[#0F172A] text-slate-900 dark:text-[#F8FAFC]">
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-slate-900 dark:text-[#F8FAFC] mb-1">Nama Matakuliah</label>
                                <input type="text" name="nama_matkul" x-model="form.nama_matkul" required class="w-full rounded-xl border-gray-200 dark:border-[#F8FAFC] focus:ring-blue-600 dark:focus:ring-[#1E3A8A] focus:border-blue-600 dark:focus:border-[#1E3A8A] px-4 py-2 border bg-slate-50 dark:bg-[#0F172A] text-slate-900 dark:text-[#F8FAFC]">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-slate-900 dark:text-[#F8FAFC] mb-1">SKS</label>
                                <input type="number" name="sks" x-model="form.sks" required min="1" max="6" class="w-full rounded-xl border-gray-200 dark:border-[#F8FAFC] focus:ring-blue-600 dark:focus:ring-[#1E3A8A] focus:border-blue-600 dark:focus:border-[#1E3A8A] px-4 py-2 border bg-slate-50 dark:bg-[#0F172A] text-slate-900 dark:text-[#F8FAFC]">
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
