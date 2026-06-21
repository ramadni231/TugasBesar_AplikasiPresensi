<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Presensi Kampus</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-100 dark:bg-[#1E3A8A] min-h-screen flex items-center justify-center font-sans antialiased">
    <div x-data="{ 
            identitas: '', 
            password: '', 
            showPassword: false,
            loading: false
         }" 
         class="bg-white dark:bg-[#0F172A] rounded-xl shadow-lg p-8 w-full max-w-md mx-4">
        
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-gradient-to-tr from-blue-700 to-blue-500 text-white shadow-lg shadow-blue-500/30 mb-5">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
            </div>
            <h1 class="text-3xl font-medium text-slate-900 dark:text-[#F8FAFC] mb-2">Selamat Datang di My Presensiku</h1>
            <p class="text-slate-500 dark:text-[#F8FAFC]/70">Silakan masuk ke akun Anda</p>
        </div>

        @if($errors->any())
            <div class="bg-red-50 text-red-600 p-4 rounded-xl mb-6 border border-red-100 text-sm">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ url('/login') }}" @submit="loading = true">
            @csrf
            <div class="mb-5">
                <label class="block text-sm font-medium text-slate-900 dark:text-[#F8FAFC] mb-2">Nomor Identitas</label>
                <input type="text" name="nomor_identitas" x-model="identitas"
                       class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-[#F8FAFC] focus:outline-none focus:ring-2 focus:ring-blue-600 dark:focus:ring-[#1E3A8A] focus:border-transparent transition-all"
                       placeholder="NIM / NIDN / Admin ID" required>
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium text-slate-900 dark:text-[#F8FAFC] mb-2">Password</label>
                <div class="relative">
                    <input :type="showPassword ? 'text' : 'password'" name="password" x-model="password"
                           class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-[#F8FAFC] focus:outline-none focus:ring-2 focus:ring-blue-600 dark:focus:ring-[#1E3A8A] focus:border-transparent transition-all"
                           placeholder="••••••••" required>
                    <button type="button" @click="showPassword = !showPassword"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-slate-600 dark:text-[#F8FAFC]/80">
                        <svg x-show="!showPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        <svg x-show="showPassword" style="display: none;" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                    </button>
                </div>
            </div>

            <button type="submit" 
                    class="w-full bg-[#0062FF] hover:bg-blue-700 dark:bg-[#1E3A8A] dark:hover:bg-[#1E3A8A]/80 text-white font-medium py-3 px-4 rounded-xl transition-colors flex justify-center items-center gap-2"
                    :class="{ 'opacity-70 cursor-not-allowed': loading }"
                    :disabled="loading">
                <svg x-show="loading" style="display: none;" class="animate-spin -ml-1 mr-2 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span x-text="loading ? 'Memproses...' : 'Masuk'">Masuk</span>
            </button>
        </form>
    </div>
</body>
</html>
