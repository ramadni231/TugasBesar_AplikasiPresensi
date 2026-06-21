@extends('layouts.mahasiswa')

@section('title', 'Pindai Presensi')

@section('content')
<div class="mb-6">
    <h2 class="text-2xl font-bold">Pindai Presensi</h2>
    <p class="text-slate-500 dark:text-[#F8FAFC]/70 mt-1">Masukkan Token QR dan izinkan akses lokasi Anda.</p>
</div>

<div class="max-w-md mx-auto bg-white dark:bg-[#0F172A] rounded-xl border border-gray-200 dark:border-[#F8FAFC] p-6 shadow-sm" x-data="scannerData()">
    <div x-show="!locationReady" class="text-center py-8">
        <div class="w-16 h-16 bg-blue-50 dark:bg-[#1E3A8A]/30 text-blue-600 dark:text-[#0062FF] rounded-full flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
        </div>
        <h3 class="text-lg font-bold mb-2">Mendapatkan Lokasi...</h3>
        <p class="text-sm text-slate-500 dark:text-[#F8FAFC]/70 mb-4">Mohon izinkan akses lokasi pada browser Anda agar bisa melakukan presensi.</p>
        <p class="text-xs text-red-500 font-medium" x-show="locationError" x-text="locationErrorMsg"></p>
        <button @click="getLocation()" class="mt-4 text-blue-600 dark:text-[#0062FF] text-sm font-medium hover:underline">Coba Lagi</button>
    </div>

    <div x-show="locationReady" style="display: none;">
        <div class="bg-green-50 text-green-700 p-3 rounded-xl border border-green-100 flex items-center gap-3 mb-6 text-sm">
            <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            Lokasi berhasil didapatkan.
        </div>

        <form id="presensiForm" action="{{ route('mahasiswa.pindai.presensi') }}" method="POST">
            @csrf
            <input type="hidden" name="lat" :value="lat">
            <input type="hidden" name="lng" :value="lng">
            <input type="hidden" name="token_qr" id="token_qr_input" required>
        </form>

        <div class="mb-6">
            <label class="block text-sm font-medium text-slate-900 dark:text-[#F8FAFC] mb-2 text-center">Arahkan Kamera ke QR Code</label>
            <div id="reader" class="overflow-hidden rounded-xl border border-gray-200 dark:border-[#F8FAFC] bg-black"></div>
        </div>
        
        <div class="text-center mt-4">
            <p class="text-sm text-slate-500 dark:text-[#F8FAFC]/70 mb-2">Atau masukkan token secara manual:</p>
            <div class="flex gap-2">
                <input type="text" id="manual_token" class="w-full rounded-xl border-gray-200 dark:border-[#F8FAFC] focus:ring-blue-600 dark:focus:ring-[#1E3A8A] focus:border-blue-600 dark:focus:border-[#1E3A8A] px-4 py-2 border bg-white/50 dark:bg-[#0F172A]/50 text-slate-900 dark:text-[#F8FAFC] font-mono text-center text-sm tracking-wider" placeholder="32 Karakter Token">
                <button type="button" @click="submitManualToken()" class="bg-[#0062FF] hover:bg-blue-700 dark:bg-[#1E3A8A] dark:hover:bg-[#1E3A8A]/80 text-white font-medium py-2 px-4 rounded-xl transition-colors text-sm">
                    Kirim
                </button>
            </div>
        </div>
    </div>
</div>

<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('scannerData', () => ({
            locationReady: false,
            locationError: false,
            locationErrorMsg: '',
            lat: '',
            lng: '',

            init() {
                this.getLocation();
            },

            getLocation() {
                this.locationError = false;
                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(
                        (position) => {
                            this.lat = position.coords.latitude;
                            this.lng = position.coords.longitude;
                            this.locationReady = true;
                            // Tambahkan sedikit delay agar DOM dirender terlebih dahulu sebelum scanner diinisiasi
                            setTimeout(() => this.startScanner(), 500);
                        },
                        (error) => {
                            this.locationError = true;
                            this.locationErrorMsg = 'Gagal mendapatkan lokasi. Pastikan GPS aktif dan browser diizinkan.';
                        },
                        { enableHighAccuracy: true }
                    );
                } else {
                    this.locationError = true;
                    this.locationErrorMsg = 'Geolocation tidak didukung oleh browser ini.';
                }
            },

            startScanner() {
                const html5QrCode = new Html5Qrcode("reader");
                const config = { fps: 10, qrbox: { width: 250, height: 250 } };

                html5QrCode.start({ facingMode: "environment" }, config, (decodedText) => {
                    // Berhasil scan
                    html5QrCode.stop().then(() => {
                        document.getElementById('token_qr_input').value = decodedText;
                        document.getElementById('presensiForm').submit();
                    });
                }, (errorMessage) => {
                    // Ignore error per frame
                }).catch((err) => {
                    console.log("Kamera tidak dapat diakses", err);
                });
            },

            submitManualToken() {
                const token = document.getElementById('manual_token').value;
                if(token) {
                    document.getElementById('token_qr_input').value = token;
                    document.getElementById('presensiForm').submit();
                }
            }
        }))
    })
</script>
@endsection
