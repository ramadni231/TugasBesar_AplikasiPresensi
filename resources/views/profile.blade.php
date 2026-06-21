@extends($layout)

@section('title', 'Ubah Sandi')

@section('content')
<div class="max-w-xl mx-auto space-y-6 mt-8">
    <div class="bg-white dark:bg-[#0F172A] rounded-xl border border-gray-200 dark:border-[#F8FAFC]/20 p-6 shadow-sm">
        <h2 class="text-xl font-bold text-slate-900 dark:text-[#F8FAFC] mb-2 flex items-center gap-2">
            <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
            Ubah Kata Sandi
        </h2>
        <p class="text-sm text-slate-500 dark:text-[#F8FAFC]/70 mb-6">Pastikan akun Anda menggunakan kata sandi yang kuat dan aman.</p>
        
        <form action="{{ route('profile.updatePassword') }}" method="POST" class="space-y-4">
            @csrf
            @method('PUT')
            
            <div>
                <label class="block text-sm font-medium text-slate-900 dark:text-[#F8FAFC] mb-2">Password Lama</label>
                <input type="password" name="password_lama" required class="w-full rounded-xl border border-gray-200 dark:border-[#F8FAFC]/20 focus:ring-blue-600 dark:focus:ring-[#1E3A8A] focus:border-blue-600 dark:focus:border-[#1E3A8A] px-4 py-2 bg-white/50 dark:bg-[#1E293B]/50 text-slate-900 dark:text-[#F8FAFC]">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-slate-900 dark:text-[#F8FAFC] mb-2">Password Baru</label>
                <input type="password" name="password_baru" required class="w-full rounded-xl border border-gray-200 dark:border-[#F8FAFC]/20 focus:ring-blue-600 dark:focus:ring-[#1E3A8A] focus:border-blue-600 dark:focus:border-[#1E3A8A] px-4 py-2 bg-white/50 dark:bg-[#1E293B]/50 text-slate-900 dark:text-[#F8FAFC]">
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-900 dark:text-[#F8FAFC] mb-2">Konfirmasi Password Baru</label>
                <input type="password" name="password_baru_confirmation" required class="w-full rounded-xl border border-gray-200 dark:border-[#F8FAFC]/20 focus:ring-blue-600 dark:focus:ring-[#1E3A8A] focus:border-blue-600 dark:focus:border-[#1E3A8A] px-4 py-2 bg-white/50 dark:bg-[#1E293B]/50 text-slate-900 dark:text-[#F8FAFC]">
            </div>
            
            <div class="pt-4">
                <button type="submit" class="w-full bg-[#0062FF] hover:bg-blue-700 text-white px-6 py-3 rounded-xl font-medium transition-colors shadow-sm flex justify-center items-center gap-2">
                    Simpan Password Baru
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
