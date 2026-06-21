<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dosen Dashboard') - Presensi Kampus</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script>
        if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>
</head>
<body class="bg-white dark:bg-[#0F172A] min-h-screen font-sans antialiased text-slate-900 dark:text-[#F8FAFC]" x-data="{ sidebarOpen: false, darkMode: localStorage.getItem('theme') === 'dark' }" x-init="$watch('darkMode', val => { localStorage.setItem('theme', val ? 'dark' : 'light'); if(val) document.documentElement.classList.add('dark'); else document.documentElement.classList.remove('dark'); }); if(darkMode) document.documentElement.classList.add('dark'); else document.documentElement.classList.remove('dark');">
    
    <!-- Navbar / Header -->
    <header class="bg-[#0062FF] dark:bg-[#1E3A8A] text-white shadow-md z-20 relative">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                <div class="flex items-center gap-4">
                    <button @click="sidebarOpen = !sidebarOpen" class="md:hidden p-2 rounded-md hover:bg-blue-700 dark:hover:bg-[#1E3A8A]/80 focus:outline-none">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    </button>
                    <span class="text-xl font-bold">Portal Dosen</span>
                </div>
                <div class="flex items-center gap-4">
                    
                    <button @click="darkMode = !darkMode" class="p-2 rounded-md hover:bg-blue-700 dark:hover:bg-blue-700 dark:hover:bg-[#1E3A8A]/80 focus:outline-none transition-colors">
                        <svg x-show="!darkMode" class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
                        <svg x-show="darkMode" class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    </button>
                    <div x-data="{ profileOpen: false }" class="relative">
                        <button @click="profileOpen = !profileOpen" @click.outside="profileOpen = false" class="flex items-center gap-2 hover:bg-blue-700 dark:hover:bg-[#1E3A8A]/80 p-2 rounded-xl transition-colors focus:outline-none">
                            <div class="w-8 h-8 rounded-full bg-white/20 flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            </div>
                            <span class="hidden sm:block text-sm font-medium text-white">{{ Auth::user()->nama }}</span>
                            <svg class="w-4 h-4 text-white/70 transition-transform" :class="{ 'rotate-180': profileOpen }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        
                        <!-- Dropdown Menu -->
                        <div x-show="profileOpen" style="display: none;"
                             x-transition:enter="transition ease-out duration-100"
                             x-transition:enter-start="transform opacity-0 scale-95"
                             x-transition:enter-end="transform opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-75"
                             x-transition:leave-start="transform opacity-100 scale-100"
                             x-transition:leave-end="transform opacity-0 scale-95"
                             class="absolute right-0 mt-2 w-48 bg-white dark:bg-[#1E293B] rounded-xl shadow-lg border border-gray-100 dark:border-[#F8FAFC]/10 overflow-hidden z-50">
                            <a href="{{ route('profile') }}" class="flex items-center gap-2 px-4 py-3 text-sm text-slate-700 dark:text-[#F8FAFC] hover:bg-slate-50 dark:hover:bg-[#0F172A] transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
                                Ubah Sandi
                            </a>
                            <div class="border-t border-gray-100 dark:border-[#F8FAFC]/10"></div>
                            <form method="POST" action="{{ route('logout') }}" class="block">
                                @csrf
                                <button type="submit" class="flex items-center gap-2 w-full px-4 py-3 text-sm text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors text-left">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                    Logout
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <div class="flex">
        <!-- Sidebar -->
        <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'" class="fixed md:relative md:translate-x-0 z-10 w-64 bg-white dark:bg-[#0F172A] border-r border-gray-200 dark:border-[#F8FAFC]/20 min-h-[calc(100vh-64px)] transition-transform duration-300 ease-in-out">
            <nav class="p-4 space-y-2">
                @php
                    $route = Route::currentRouteName();
                @endphp
                <a href="/dosen/dashboard" class="flex items-center gap-3 px-4 py-3 rounded-xl font-medium transition-colors border border-transparent dark:border-[#F8FAFC] hover:border-blue-500 dark:hover:border-blue-500 {{ $route == 'dosen.dashboard' ? 'bg-blue-50 dark:bg-[#1E3A8A]/30 text-blue-600 dark:text-[#0062FF]' : 'text-slate-600 dark:text-[#F8FAFC]/80 hover:bg-slate-50 dark:hover:bg-[#1E293B] hover:text-slate-900 dark:hover:text-[#F8FAFC]' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                    Kelas Hari Ini
                </a>
                <a href="/dosen/jadwal" class="flex items-center gap-3 px-4 py-3 rounded-xl font-medium transition-colors border border-transparent dark:border-[#F8FAFC] hover:border-blue-500 dark:hover:border-blue-500 {{ $route == 'dosen.jadwal' ? 'bg-blue-50 dark:bg-[#1E3A8A]/30 text-blue-600 dark:text-[#0062FF]' : 'text-slate-600 dark:text-[#F8FAFC]/80 hover:bg-slate-50 dark:hover:bg-[#1E293B] hover:text-slate-900 dark:hover:text-[#F8FAFC]' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    Jadwal Mengajar
                </a>
                <a href="/dosen/rekap" class="flex items-center gap-3 px-4 py-3 rounded-xl font-medium transition-colors border border-transparent dark:border-[#F8FAFC] hover:border-blue-500 dark:hover:border-blue-500 {{ $route == 'dosen.rekap' || $route == 'dosen.rekap.detail' ? 'bg-blue-50 dark:bg-[#1E3A8A]/30 text-blue-600 dark:text-[#0062FF]' : 'text-slate-600 dark:text-[#F8FAFC]/80 hover:bg-slate-50 dark:hover:bg-[#1E293B] hover:text-slate-900 dark:hover:text-[#F8FAFC]' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Rekap Presensi
                </a>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 p-6 md:p-8 overflow-x-hidden">
            @include('components.flash-modal')
            @if($errors->any())
                <div class="mb-4 bg-red-50 text-red-700 p-4 rounded-xl border border-red-100 flex items-center justify-between" x-data="{ show: true }" x-show="show">
                    <span>{{ $errors->first() }}</span>
                    <button @click="show = false" class="text-red-500 hover:text-red-700">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
            @endif

            @yield('content')
        </main>
    </div>
</body>
</html>
