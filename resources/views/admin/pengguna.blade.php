@extends('layouts.admin')

@section('title', 'Manajemen Pengguna')

@section('content')
<div x-data="{ 
    search: '',
    showModal: false, 
    editMode: false,
    form: {
        id: '',
        nama: '',
        nomor_identitas: '',
        email: '',
        peran: 'dosen',
        password: ''
    },
    openModal(pengguna = null) {
        if (pengguna) {
            this.editMode = true;
            this.form.id = pengguna.id;
            this.form.nama = pengguna.nama;
            this.form.nomor_identitas = pengguna.nomor_identitas;
            this.form.email = pengguna.email;
            this.form.peran = pengguna.peran;
            this.form.password = '';
        } else {
            this.editMode = false;
            this.form.id = '';
            this.form.nama = '';
            this.form.nomor_identitas = '';
            this.form.email = '';
            this.form.peran = 'dosen';
            this.form.password = '';
        }
        this.showModal = true;
    }
}">

    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
        <h2 class="text-2xl font-bold">Manajemen Pengguna</h2>
        <div class="flex items-center gap-3 w-full sm:w-auto">
            <div class="relative flex-1 sm:w-64">
                <input x-model="search" type="text" placeholder="Cari pengguna..." class="w-full rounded-xl border border-gray-200 dark:border-[#F8FAFC]/20 bg-white dark:bg-[#0F172A] text-sm px-4 py-2 pl-10 focus:ring-blue-600 focus:border-blue-600 dark:focus:ring-[#1E3A8A] dark:text-[#F8FAFC]">
                <svg class="w-4 h-4 absolute left-3.5 top-3 text-slate-400 dark:text-[#F8FAFC]/60" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>
            <button @click="openModal()" class="bg-blue-600 hover:bg-blue-700 dark:bg-blue-600 dark:hover:bg-blue-500 text-white px-4 py-2 rounded-xl text-sm font-medium transition-colors flex items-center gap-2 whitespace-nowrap">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Tambah Pengguna
            </button>
        </div>
    </div>

    <!-- Filter -->
    <div class="bg-white dark:bg-[#0F172A] rounded-xl border border-gray-200 dark:border-[#F8FAFC] p-4 mb-6">
        <form action="{{ route('admin.pengguna.index') }}" method="GET" class="flex items-center gap-4">
            <label class="text-sm font-medium text-slate-900 dark:text-[#F8FAFC]">Filter Peran:</label>
            <select name="peran" onchange="this.form.submit()" class="rounded-xl border-gray-200 dark:border-[#F8FAFC] focus:ring-blue-600 dark:focus:ring-[#1E3A8A] focus:border-blue-600 dark:focus:border-[#1E3A8A] text-sm">
                <option value="">Semua</option>
                <option value="dosen" {{ request('peran') == 'dosen' ? 'selected' : '' }}>Dosen</option>
                <option value="mahasiswa" {{ request('peran') == 'mahasiswa' ? 'selected' : '' }}>Mahasiswa</option>
                <option value="admin" {{ request('peran') == 'admin' ? 'selected' : '' }}>Admin</option>
            </select>
        </form>
    </div>

    <!-- Grid List Pengguna -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($pengguna as $p)
        <div x-show="search === '' || '{{ strtolower($p->nama) }}'.includes(search.toLowerCase()) || '{{ strtolower($p->nomor_identitas) }}'.includes(search.toLowerCase())" class="bg-white dark:bg-[#0F172A] rounded-xl border border-gray-200 dark:border-[#F8FAFC] p-6 flex flex-col hover:border-blue-500 dark:hover:border-blue-500 hover:shadow-md transition-all">
            <div class="flex justify-between items-start mb-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-slate-100 dark:bg-[#1E3A8A] flex items-center justify-center text-slate-600 dark:text-[#F8FAFC]/80 font-bold">
                        {{ strtoupper(substr($p->nama, 0, 1)) }}
                    </div>
                    <div>
                        <h3 class="font-bold text-slate-900 dark:text-[#F8FAFC]">{{ $p->nama }}</h3>
                        <p class="text-xs text-slate-500 dark:text-[#F8FAFC]/70">{{ $p->nomor_identitas }}</p>
                    </div>
                </div>
                <span class="px-2.5 py-1 rounded-full text-xs font-medium 
                    {{ $p->peran == 'admin' ? 'bg-red-100 text-red-700' : ($p->peran == 'dosen' ? 'bg-blue-100 text-blue-600 dark:text-[#0062FF]' : 'bg-green-100 text-green-700') }}">
                    {{ ucfirst($p->peran) }}
                </span>
            </div>
            
            <div class="text-sm text-slate-600 dark:text-[#F8FAFC]/80 mb-4 flex-1">
                <p class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-slate-400 dark:text-[#F8FAFC]/60" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    {{ $p->email }}
                </p>
            </div>

            <div class="flex items-center justify-end gap-2 mt-4 pt-4 border-t border-gray-100">
                <button @click='openModal({!! json_encode($p, JSON_HEX_APOS | JSON_HEX_QUOT) !!})' class="text-blue-600 dark:text-[#0062FF] hover:text-blue-800 p-2 rounded-lg hover:bg-blue-50 dark:bg-[#1E3A8A]/30 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                </button>
                <form action="{{ route('admin.pengguna.destroy', $p->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus pengguna ini?');" class="inline">
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
                <form :action="editMode ? '/admin/pengguna/' + form.id : '{{ route('admin.pengguna.store') }}'" method="POST">
                    @csrf
                    <input type="hidden" name="_method" value="PUT" x-bind:disabled="!editMode">
                    
                    <div class="bg-transparent px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <h3 class="text-lg leading-6 font-medium text-slate-900 dark:text-[#F8FAFC] mb-4" id="modal-title" x-text="editMode ? 'Edit Pengguna' : 'Tambah Pengguna'"></h3>
                        
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-slate-900 dark:text-[#F8FAFC] mb-1">Nama Lengkap</label>
                                <input type="text" name="nama" x-model="form.nama" required class="w-full rounded-xl border-gray-200 dark:border-[#F8FAFC] focus:ring-blue-600 dark:focus:ring-[#1E3A8A] focus:border-blue-600 dark:focus:border-[#1E3A8A] px-4 py-2 border bg-slate-50 dark:bg-[#0F172A] text-slate-900 dark:text-[#F8FAFC]">
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-slate-900 dark:text-[#F8FAFC] mb-1">Nomor Identitas (NIM/NIP)</label>
                                <input type="text" name="nomor_identitas" x-model="form.nomor_identitas" required class="w-full rounded-xl border-gray-200 dark:border-[#F8FAFC] focus:ring-blue-600 dark:focus:ring-[#1E3A8A] focus:border-blue-600 dark:focus:border-[#1E3A8A] px-4 py-2 border bg-slate-50 dark:bg-[#0F172A] text-slate-900 dark:text-[#F8FAFC]">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-slate-900 dark:text-[#F8FAFC] mb-1">Email</label>
                                <input type="email" name="email" x-model="form.email" required class="w-full rounded-xl border-gray-200 dark:border-[#F8FAFC] focus:ring-blue-600 dark:focus:ring-[#1E3A8A] focus:border-blue-600 dark:focus:border-[#1E3A8A] px-4 py-2 border bg-slate-50 dark:bg-[#0F172A] text-slate-900 dark:text-[#F8FAFC]">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-slate-900 dark:text-[#F8FAFC] mb-1">Peran</label>
                                <select name="peran" x-model="form.peran" required class="w-full rounded-xl border-gray-200 dark:border-[#F8FAFC] focus:ring-blue-600 dark:focus:ring-[#1E3A8A] focus:border-blue-600 dark:focus:border-[#1E3A8A] px-4 py-2 border bg-slate-50 dark:bg-[#0F172A] text-slate-900 dark:text-[#F8FAFC]">
                                    <option value="dosen">Dosen</option>
                                    <option value="mahasiswa">Mahasiswa</option>
                                    <option value="admin">Admin</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-slate-900 dark:text-[#F8FAFC] mb-1">Password <span x-show="editMode" class="text-xs text-slate-400 dark:text-[#F8FAFC]/60 font-normal">(Kosongkan jika tidak diubah)</span></label>
                                <input type="password" name="password" x-model="form.password" :required="!editMode" class="w-full rounded-xl border-gray-200 dark:border-[#F8FAFC] focus:ring-blue-600 dark:focus:ring-[#1E3A8A] focus:border-blue-600 dark:focus:border-[#1E3A8A] px-4 py-2 border bg-slate-50 dark:bg-[#0F172A] text-slate-900 dark:text-[#F8FAFC]" placeholder="Minimal 6 karakter">
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
