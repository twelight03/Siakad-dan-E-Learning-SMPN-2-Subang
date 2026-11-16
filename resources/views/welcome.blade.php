<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SIAKAD & E-Learning | SMP Negeri 2 Subang - Portal Resmi</title>

    <!-- Meta Description & Favicon -->
    <meta name="description" content="Portal Resmi Sistem Informasi Akademik dan E-Learning Terpadu SMP Negeri 2 Subang, Kabupaten Kuningan, Jawa Barat.">
    <meta name="author" content="SMP Negeri 2 Subang">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800,900&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-50 text-slate-800 antialiased font-sans flex flex-col min-h-screen selection:bg-blue-700 selection:text-white">

    <!-- 1. Top Government & Institutional Strip -->
    <div class="bg-slate-900 text-slate-300 text-xs py-2 px-4 sm:px-8 border-b border-slate-800">
        <div class="max-w-7xl mx-auto flex flex-col sm:flex-row justify-between items-center gap-2 sm:gap-4 text-center sm:text-left">
            <div class="flex items-center space-x-2">
                <span class="inline-block w-2 h-2 rounded-full bg-emerald-400"></span>
                <span class="font-medium tracking-wide">Pemerintah Kabupaten Kuningan &bull; Dinas Pendidikan dan Kebudayaan</span>
            </div>
            <div class="flex items-center flex-wrap justify-center space-x-3 text-[11px] text-slate-400">
                <span>NPSN: <strong class="text-slate-200">20212952</strong></span>
                <span>&bull;</span>
                <span>Akreditasi: <strong class="text-slate-200">A (Unggul)</strong></span>
                <span>&bull;</span>
                <span>Status: <strong class="text-slate-200">Negeri</strong></span>
                <span>&bull;</span>
                <span class="text-emerald-300 font-semibold">T.A. {{ $stats['active_year']->name ?? '2026/2027' }} ({{ ($stats['active_year']?->semester ?? 'odd') == 'odd' ? 'Ganjil' : 'Genap' }})</span>
            </div>
        </div>
    </div>

    <!-- 2. Main School Header (Kop Portal & Navigasi) -->
    <header class="bg-white border-b border-slate-200 shadow-xs sticky top-0 z-40">
        <div class="max-w-7xl mx-auto px-4 sm:px-8 py-3 flex items-center justify-between">
            <!-- School Brand with Real Logo -->
            <a href="{{ url('/') }}" class="flex items-center space-x-3 group">
                <img 
                    src="{{ asset('images/logo-smpn2.png') }}" 
                    alt="Logo SMP Negeri 2 Subang" 
                    class="h-12 sm:h-14 w-auto object-contain flex-shrink-0 group-hover:scale-105 transition-transform"
                />
                <div>
                    <h1 class="text-base sm:text-xl font-black text-slate-900 tracking-tight leading-none group-hover:text-blue-700 transition-colors">
                        SMP NEGERI 2 SUBANG
                    </h1>
                    <p class="text-xs sm:text-sm font-bold text-blue-700 mt-0.5">
                        Sistem Informasi Akademik & E-Learning (SIAKAD)
                    </p>
                    <p class="text-[11px] text-slate-500 hidden md:block">
                        Jl. Raya Pamulihan - Tangkolo, Kec. Subang, Kab. Kuningan, Jawa Barat
                    </p>
                </div>
            </a>

            <!-- Navigation Links (Desktop) -->
            <nav class="hidden lg:flex items-center space-x-6 text-xs font-semibold text-slate-600">
                <a href="#beranda" class="hover:text-blue-700 transition">Beranda</a>
                <a href="#sambutan" class="hover:text-blue-700 transition">Sambutan</a>
                <a href="#pengumuman" class="hover:text-blue-700 transition">Pengumuman</a>
                <a href="#data-pokok" class="hover:text-blue-700 transition">Data Pokok</a>
                <a href="#layanan" class="hover:text-blue-700 transition">Layanan</a>
                <a href="#kontak" class="hover:text-blue-700 transition">Kontak</a>
            </nav>

            <!-- Right Action (Masuk / Dashboard) -->
            <div class="flex items-center space-x-2.5">
                @if (Route::has('login'))
                    @auth
                        <a href="{{ url('/dashboard') }}" class="inline-flex items-center px-4 py-2 bg-blue-700 hover:bg-blue-800 text-white rounded-lg text-xs sm:text-sm font-bold shadow-xs transition">
                            <span>Buka Dashboard</span>
                            <svg class="w-4 h-4 ms-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                            </svg>
                        </a>
                    @else
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="hidden sm:inline-flex items-center px-3.5 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-xs sm:text-sm font-semibold transition border border-slate-300">
                                <span>Pendaftaran Siswa</span>
                            </a>
                        @endif
                        <a href="{{ route('login') }}" class="inline-flex items-center px-4 sm:px-5 py-2 bg-blue-700 hover:bg-blue-800 text-white rounded-lg text-xs sm:text-sm font-bold shadow-xs transition">
                            <svg class="w-4 h-4 me-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                            </svg>
                            <span>Masuk Portal</span>
                        </a>
                    @endauth
                @endif
            </div>
        </div>
    </header>

    <!-- 3. Real School Hero Section (Hero Banner Resmi) -->
    <section id="beranda" class="bg-gradient-to-b from-blue-950 via-slate-900 to-slate-900 text-white py-12 lg:py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-center">
                
                <!-- Left Column: Official Welcome & Quick Portals (7 Cols) -->
                <div class="lg:col-span-7 space-y-6">
                    <div class="inline-flex items-center space-x-2 px-3 py-1 rounded-md bg-blue-800/80 border border-blue-500/50 text-blue-200 text-xs font-semibold">
                        <span>🏛️ Portal Resmi Pelayanan KBM & Administrasi Sekolah</span>
                    </div>

                    <h2 class="text-2xl sm:text-4xl lg:text-5xl font-black text-white leading-tight tracking-tight">
                        Selamat Datang di SIAKAD SMP Negeri 2 Subang
                    </h2>

                    <p class="text-sm sm:text-base text-slate-200 leading-relaxed max-w-2xl font-normal">
                        Sistem Informasi Akademik dan E-Learning Terpadu untuk memfasilitasi transparansi pembelajaran digital, pemantauan presensi harian, penilaian kurikulum nasional, serta distribusi bahan ajar bagi seluruh civitas akademika.
                    </p>

                    <!-- 3 Quick Access Portals for Real School Roles -->
                    <div class="pt-2">
                        <p class="text-xs uppercase font-bold text-slate-300 tracking-wider mb-3">Pilih Jalur Akses Portal:</p>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                            <!-- Portal Siswa -->
                            <a href="{{ route('login') }}" class="p-3.5 rounded-xl bg-white/10 hover:bg-white/20 border border-white/15 transition flex items-center space-x-3 group backdrop-blur-xs">
                                <div class="w-10 h-10 rounded-lg bg-emerald-500 text-white flex items-center justify-center font-bold text-lg flex-shrink-0 shadow-sm">
                                    🎓
                                </div>
                                <div class="text-left">
                                    <div class="font-bold text-sm text-white group-hover:text-emerald-300 transition">Portal Siswa</div>
                                    <div class="text-[11px] text-slate-300">Tugas, Rapor & Absen</div>
                                </div>
                            </a>

                            <!-- Portal Guru -->
                            <a href="{{ route('login') }}" class="p-3.5 rounded-xl bg-white/10 hover:bg-white/20 border border-white/15 transition flex items-center space-x-3 group backdrop-blur-xs">
                                <div class="w-10 h-10 rounded-lg bg-blue-500 text-white flex items-center justify-center font-bold text-lg flex-shrink-0 shadow-sm">
                                    👨‍🏫
                                </div>
                                <div class="text-left">
                                    <div class="font-bold text-sm text-white group-hover:text-blue-300 transition">Portal Guru</div>
                                    <div class="text-[11px] text-slate-300">Jurnal & Input Nilai</div>
                                </div>
                            </a>

                            <!-- Portal TU / Staf -->
                            <a href="{{ route('login') }}" class="p-3.5 rounded-xl bg-white/10 hover:bg-white/20 border border-white/15 transition flex items-center space-x-3 group backdrop-blur-xs">
                                <div class="w-10 h-10 rounded-lg bg-indigo-500 text-white flex items-center justify-center font-bold text-lg flex-shrink-0 shadow-sm">
                                    🏛️
                                </div>
                                <div class="text-left">
                                    <div class="font-bold text-sm text-white group-hover:text-indigo-300 transition">Tata Usaha</div>
                                    <div class="text-[11px] text-slate-300">Dapodik & Rombel</div>
                                </div>
                            </a>
                        </div>
                    </div>

                    <!-- Security & Credential Guidance -->
                    <div class="p-3.5 rounded-xl bg-blue-950/70 border border-blue-700/60 flex items-start space-x-3 text-xs text-blue-200">
                        <svg class="w-4 h-4 text-blue-400 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                        </svg>
                        <span>Gunakan <strong>Username (NISN untuk Siswa, NIP untuk Guru)</strong> dan kata sandi yang telah diterbitkan resmi oleh Bagian Tata Usaha Sekolah.</span>
                    </div>
                </div>

                <!-- Right Column: Real School Photo Framed (5 Cols) -->
                <div class="lg:col-span-5">
                    <div class="bg-white p-2.5 rounded-2xl shadow-2xl border border-slate-200/50">
                        <div class="relative overflow-hidden rounded-xl aspect-4/3 bg-slate-200">
                            <img 
                                src="{{ asset('images/hero-school.jpg') }}" 
                                alt="Gedung Kampus SMP Negeri 2 Subang" 
                                class="w-full h-full object-cover object-center hover:scale-105 transition-transform duration-500"
                            />
                            <div class="absolute bottom-0 inset-x-0 bg-gradient-to-t from-slate-900/90 via-slate-900/40 to-transparent p-4 text-white">
                                <span class="text-[10px] font-bold uppercase tracking-wider bg-blue-600 px-2 py-0.5 rounded text-white inline-block mb-1">
                                    Kampus Sekolah
                                </span>
                                <p class="text-sm font-bold leading-tight">Gedung Utama SMP Negeri 2 Subang</p>
                                <p class="text-xs text-slate-300">Kecamatan Subang, Kabupaten Kuningan</p>
                            </div>
                        </div>
                        <div class="p-3 text-center">
                            <p class="text-xs text-slate-600 font-medium">
                                📍 Jl. Raya Pamulihan - Tangkolo, Kec. Subang, Kab. Kuningan
                            </p>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- 4. Sambutan Kepala Sekolah (Authentic Principal Statement) -->
    <section id="sambutan" class="py-12 bg-white border-b border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-8">
            <div class="bg-gradient-to-r from-slate-50 via-blue-50/40 to-slate-50 rounded-2xl border border-slate-200/80 p-6 sm:p-10">
                <div class="grid grid-cols-1 md:grid-cols-12 gap-8 items-center">
                    
                    <div class="md:col-span-3 text-center">
                        <div class="w-24 h-24 sm:w-28 sm:h-28 mx-auto rounded-full bg-blue-900 text-white flex items-center justify-center font-bold text-3xl shadow-md border-4 border-white mb-3">
                            🏛️
                        </div>
                        <h4 class="font-extrabold text-sm text-slate-900">Kepala Sekolah</h4>
                        <p class="text-xs text-slate-600">SMP Negeri 2 Subang</p>
                        <p class="text-[11px] text-slate-500 mt-0.5">Kab. Kuningan</p>
                    </div>

                    <div class="md:col-span-9 space-y-3">
                        <div class="inline-flex items-center space-x-1.5 text-blue-700 font-bold text-xs uppercase tracking-wider">
                            <span>Prakata Pimpinan Satuan Pendidikan</span>
                        </div>
                        <h3 class="text-xl sm:text-2xl font-black text-slate-900 leading-snug">
                            "Mewujudkan Tata Kelola Pendidikan yang Transparan, Akuntabel, dan Berkarakter"
                        </h3>
                        <p class="text-xs sm:text-sm text-slate-600 leading-relaxed italic">
                            &ldquo;Penerapan Sistem Informasi Akademik (SIAKAD) ini merupakan langkah strategis SMP Negeri 2 Subang dalam mendigitalisasi layanan pendidikan. Melalui platform ini, kami memastikan keterbukaan informasi capaian belajar siswa, ketertiban administrasi mengajar dewan guru, serta kemudahan orang tua dalam mendampingi perkembangan putra-putrinya menuju insan yang cerdas dan berakhlak mulia.&rdquo;
                        </p>
                        <div class="pt-2 flex items-center space-x-4 text-xs font-semibold text-slate-700">
                            <span class="flex items-center space-x-1 text-emerald-600">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                <span>Kurikulum Merdeka</span>
                            </span>
                            <span class="text-slate-300">&bull;</span>
                            <span class="flex items-center space-x-1 text-blue-600">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                <span>Manajemen Berbasis Sekolah (MBS)</span>
                            </span>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </section>

    <!-- 5. Papan Pengumuman & Agenda Akademik Resmi -->
    <section id="pengumuman" class="py-12 bg-white border-b border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                
                <!-- Left: Papan Informasi Akademik Terkini (8 Cols) -->
                <div class="lg:col-span-8">
                    <div class="flex items-center justify-between pb-3 mb-5 border-b border-slate-200">
                        <div class="flex items-center space-x-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-blue-600"></span>
                            <h3 class="font-extrabold text-base sm:text-lg text-slate-900 uppercase tracking-wide">
                                Papan Informasi Akademik
                            </h3>
                        </div>
                        <span class="text-xs font-bold text-blue-700 bg-blue-50 px-2.5 py-1 rounded-md border border-blue-200">
                            T.A. {{ $stats['active_year']->name ?? '2026/2027' }} Aktif
                        </span>
                    </div>

                    <div class="space-y-3.5">
                        <!-- Agenda 1 -->
                        <div class="p-4 rounded-xl border border-slate-200 bg-slate-50 hover:bg-white hover:border-blue-400 transition flex items-start space-x-4 shadow-2xs">
                            <div class="w-12 h-12 rounded-xl bg-blue-100 text-blue-700 flex flex-col items-center justify-center font-black flex-shrink-0 text-xs">
                                <span class="text-[10px] font-semibold uppercase">KBM</span>
                                <span class="text-base leading-none">AKTIF</span>
                            </div>
                            <div class="flex-1">
                                <div class="flex items-center justify-between">
                                    <h4 class="font-bold text-sm text-slate-900">Kegiatan Belajar Mengajar (KBM) Semester {{ ($stats['active_year']?->semester ?? 'odd') == 'odd' ? 'Ganjil' : 'Genap' }}</h4>
                                    <span class="text-[11px] text-slate-400">Edaran Sekolah</span>
                                </div>
                                <p class="text-xs text-slate-600 mt-1 leading-relaxed">
                                    Peserta didik wajib mengikuti KBM sesuai jadwal harian yang telah dipublikasikan di portal siswa dan menjaga ketertiban kehadiran minimum 85%.
                                </p>
                            </div>
                        </div>

                        <!-- Agenda 2 -->
                        <div class="p-4 rounded-xl border border-slate-200 bg-slate-50 hover:bg-white hover:border-emerald-400 transition flex items-start space-x-4 shadow-2xs">
                            <div class="w-12 h-12 rounded-xl bg-emerald-100 text-emerald-700 flex flex-col items-center justify-center font-black flex-shrink-0 text-xs">
                                <span class="text-[10px] font-semibold uppercase">TUGAS</span>
                                <span class="text-base leading-none">ONLINE</span>
                            </div>
                            <div class="flex-1">
                                <div class="flex items-center justify-between">
                                    <h4 class="font-bold text-sm text-slate-900">Pemanfaatan Bahan Ajar & Pengumpulan Tugas E-Learning</h4>
                                    <span class="text-[11px] text-slate-400">Akademik</span>
                                </div>
                                <p class="text-xs text-slate-600 mt-1 leading-relaxed">
                                    Materi pembelajaran dan lembar kerja peserta didik diunggah langsung oleh guru mapel. Siswa dapat mengunggah hasil pekerjaan sebelum batas waktu berakhir.
                                </p>
                            </div>
                        </div>

                        <!-- Agenda 3 -->
                        <div class="p-4 rounded-xl border border-slate-200 bg-slate-50 hover:bg-white hover:border-amber-400 transition flex items-start space-x-4 shadow-2xs">
                            <div class="w-12 h-12 rounded-xl bg-amber-100 text-amber-700 flex flex-col items-center justify-center font-black flex-shrink-0 text-xs">
                                <span class="text-[10px] font-semibold uppercase">RAPOR</span>
                                <span class="text-base leading-none">NILAI</span>
                            </div>
                            <div class="flex-1">
                                <div class="flex items-center justify-between">
                                    <h4 class="font-bold text-sm text-slate-900">Transparansi Penilaian Harian, Formatif & Sumatif</h4>
                                    <span class="text-[11px] text-slate-400">Kurikulum</span>
                                </div>
                                <p class="text-xs text-slate-600 mt-1 leading-relaxed">
                                    Perhitungan akumulasi nilai tugas, kehadiran, PTS, dan PAS diproses secara otomatis sesuai bobot persentase kurikulum yang berlaku di SMPN 2 Subang.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right: Jam KBM & Helpdesk Tata Usaha (4 Cols) -->
                <div class="lg:col-span-4 space-y-4">
                    <!-- Jam Operasional -->
                    <div class="bg-slate-50 p-5 rounded-2xl border border-slate-200">
                        <h4 class="font-bold text-sm text-slate-900 pb-2 mb-3 border-b border-slate-200 flex items-center space-x-2">
                            <span>⏰ Jam Pelayanan & KBM Sekolah</span>
                        </h4>
                        <ul class="text-xs text-slate-600 space-y-2.5">
                            <li class="flex justify-between items-center">
                                <span class="font-medium">Senin – Kamis:</span>
                                <span class="font-bold text-slate-800 bg-white px-2 py-0.5 rounded border border-slate-200">07:00 – 14:15 WIB</span>
                            </li>
                            <li class="flex justify-between items-center">
                                <span class="font-medium">Jumat (KBM & Ibadah):</span>
                                <span class="font-bold text-slate-800 bg-white px-2 py-0.5 rounded border border-slate-200">07:00 – 11:30 WIB</span>
                            </li>
                            <li class="flex justify-between items-center">
                                <span class="font-medium">Layanan Tata Usaha:</span>
                                <span class="font-bold text-slate-800 bg-white px-2 py-0.5 rounded border border-slate-200">07:30 – 15:00 WIB</span>
                            </li>
                            <li class="flex justify-between items-center">
                                <span class="font-medium">Sabtu & Minggu:</span>
                                <span class="text-red-600 font-bold bg-red-50 px-2 py-0.5 rounded border border-red-200">Libur KBM</span>
                            </li>
                        </ul>
                    </div>

                    <!-- Bantuan Akun & Helpdesk -->
                    <div class="bg-blue-50 p-5 rounded-2xl border border-blue-200">
                        <div class="flex items-center space-x-2 text-blue-950 font-bold text-sm pb-2 mb-2 border-b border-blue-200">
                            <span>📞 Layanan Bantuan Akun & TU</span>
                        </div>
                        <p class="text-xs text-blue-900 leading-relaxed mb-3">
                            Bagi siswa baru, mutasi, atau wali murid yang mengalami kendala masuk akun, silakan hubungi Ruang Tata Usaha Gedung Utama SMPN 2 Subang.
                        </p>
                        <a href="{{ route('login') }}" class="block text-center py-2.5 px-4 bg-blue-700 hover:bg-blue-800 text-white rounded-lg text-xs font-bold transition shadow-xs">
                            Buka Halaman Masuk &rarr;
                        </a>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- 6. Data Pokok Satuan Pendidikan (Statistik Real-time Dapodik) -->
    <section id="data-pokok" class="py-14 bg-slate-100 border-b border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-8">
            <div class="text-center max-w-2xl mx-auto mb-10">
                <span class="text-xs font-bold uppercase tracking-wider text-blue-700 bg-blue-100 px-3 py-1 rounded-full">
                    Profil Satuan Pendidikan
                </span>
                <h3 class="text-2xl sm:text-3xl font-black text-slate-900 mt-2">Data Pokok Pendidikan Sekolah</h3>
                <p class="text-xs sm:text-sm text-slate-600 mt-1">
                    Rekapitulasi data operasional yang terdaftar dalam pangkalan data resmi SIAKAD SMP Negeri 2 Subang
                </p>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 sm:gap-6">
                <!-- Stat 1 -->
                <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-xs text-center hover:border-blue-400 transition">
                    <div class="text-3xl sm:text-4xl font-black text-blue-700 font-mono">
                        {{ $stats['total_students'] ?? 0 }}
                    </div>
                    <div class="text-sm font-bold text-slate-800 mt-1.5">Peserta Didik</div>
                    <div class="text-[11px] text-slate-500">Siswa aktif terdaftar</div>
                </div>

                <!-- Stat 2 -->
                <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-xs text-center hover:border-emerald-400 transition">
                    <div class="text-3xl sm:text-4xl font-black text-emerald-700 font-mono">
                        {{ $stats['total_teachers'] ?? 0 }}
                    </div>
                    <div class="text-sm font-bold text-slate-800 mt-1.5">Tenaga Pendidik</div>
                    <div class="text-[11px] text-slate-500">Guru pengampu bersertifikasi</div>
                </div>

                <!-- Stat 3 -->
                <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-xs text-center hover:border-indigo-400 transition">
                    <div class="text-3xl sm:text-4xl font-black text-indigo-700 font-mono">
                        {{ $stats['total_classes'] ?? 0 }}
                    </div>
                    <div class="text-sm font-bold text-slate-800 mt-1.5">Rombel Kelas</div>
                    <div class="text-[11px] text-slate-500">Kelas VII, VIII, dan IX</div>
                </div>

                <!-- Stat 4 -->
                <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-xs text-center hover:border-amber-400 transition">
                    <div class="text-3xl sm:text-4xl font-black text-amber-700 font-mono">
                        {{ $stats['total_subjects'] ?? 0 }}
                    </div>
                    <div class="text-sm font-bold text-slate-800 mt-1.5">Mata Pelajaran</div>
                    <div class="text-[11px] text-slate-500">Kurikulum Nasional</div>
                </div>
            </div>
        </div>
    </section>

    <!-- 7. Visi, Misi & Karakter Satuan Pendidikan -->
    <section class="py-14 bg-white border-b border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-8">
            <div class="grid grid-cols-1 md:grid-cols-12 gap-8 items-center">
                
                <div class="md:col-span-5 space-y-4">
                    <span class="text-xs font-bold uppercase tracking-wider text-blue-700 bg-blue-100 px-3 py-1 rounded-full">
                        Visi Sekolah
                    </span>
                    <h3 class="text-2xl sm:text-3xl font-black text-slate-900 leading-tight">
                        "Terwujudnya Insan Berakhlak Mulia, Cerdas, Terampil, dan Berwawasan Lingkungan"
                    </h3>
                    <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
                        SMP Negeri 2 Subang bertekad mencetak lulusan yang berintegritas tinggi, menguasai ilmu pengetahuan dan teknologi, serta berakar teguh pada kearifan budaya dan nilai-nilai Pancasila.
                    </p>
                </div>

                <div class="md:col-span-7 grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="p-4 rounded-xl border border-slate-200 bg-slate-50">
                        <div class="w-8 h-8 rounded-lg bg-blue-600 text-white flex items-center justify-center font-bold text-sm mb-2">
                            1
                        </div>
                        <h4 class="font-bold text-sm text-slate-900 mb-1">Penguatan Karakter</h4>
                        <p class="text-xs text-slate-600 leading-relaxed">
                            Membina keimanan, ketakwaan, serta pembiasaan budi pekerti luhur dalam keseharian KBM.
                        </p>
                    </div>

                    <div class="p-4 rounded-xl border border-slate-200 bg-slate-50">
                        <div class="w-8 h-8 rounded-lg bg-emerald-600 text-white flex items-center justify-center font-bold text-sm mb-2">
                            2
                        </div>
                        <h4 class="font-bold text-sm text-slate-900 mb-1">Kecakapan Akademik</h4>
                        <p class="text-xs text-slate-600 leading-relaxed">
                            Menyelenggarakan proses pembelajaran aktif, kritis, dan berbasis literasi-numerasi terpadu.
                        </p>
                    </div>

                    <div class="p-4 rounded-xl border border-slate-200 bg-slate-50">
                        <div class="w-8 h-8 rounded-lg bg-amber-600 text-white flex items-center justify-center font-bold text-sm mb-2">
                            3
                        </div>
                        <h4 class="font-bold text-sm text-slate-900 mb-1">Transformasi Digital</h4>
                        <p class="text-xs text-slate-600 leading-relaxed">
                            Mengintegrasikan teknologi informasi dalam KBM, presensi daring, dan penilaian rapor digital.
                        </p>
                    </div>

                    <div class="p-4 rounded-xl border border-slate-200 bg-slate-50">
                        <div class="w-8 h-8 rounded-lg bg-indigo-600 text-white flex items-center justify-center font-bold text-sm mb-2">
                            4
                        </div>
                        <h4 class="font-bold text-sm text-slate-900 mb-1">Pelestarian Lingkungan</h4>
                        <p class="text-xs text-slate-600 leading-relaxed">
                            Mewujudkan lingkungan sekolah yang bersih, hijau, asri, dan mendukung kesehatan belajar.
                        </p>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- 8. Layanan Utama SIAKAD Terpadu (4 Pilar Pelayanan) -->
    <section id="layanan" class="py-14 bg-slate-50 border-b border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-8">
            <div class="text-center max-w-2xl mx-auto mb-10">
                <span class="text-xs font-bold uppercase tracking-wider text-blue-700 bg-blue-100 px-3 py-1 rounded-full">
                    Modul Terintegrasi
                </span>
                <h3 class="text-2xl sm:text-3xl font-black text-slate-900 mt-2">Layanan Sistem Informasi Akademik</h3>
                <p class="text-xs sm:text-sm text-slate-600 mt-1">
                    Fasilitas terpadu yang dirancang untuk mendukung mutu tata kelola pendidikan di SMP Negeri 2 Subang
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Modul 1 -->
                <div class="p-6 rounded-2xl border border-slate-200 bg-white shadow-2xs hover:shadow-md transition">
                    <div class="w-12 h-12 rounded-xl bg-blue-100 text-blue-800 font-bold flex items-center justify-center text-xl mb-4">
                        📁
                    </div>
                    <h4 class="font-bold text-base text-slate-900 mb-2">Administrasi Rombel</h4>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        Pengelolaan master data siswa, guru, pembagian rombongan belajar, dan penataan jadwal pelajaran resmi tanpa bentrok.
                    </p>
                </div>

                <!-- Modul 2 -->
                <div class="p-6 rounded-2xl border border-slate-200 bg-white shadow-2xs hover:shadow-md transition">
                    <div class="w-12 h-12 rounded-xl bg-emerald-100 text-emerald-800 font-bold flex items-center justify-center text-xl mb-4">
                        📋
                    </div>
                    <h4 class="font-bold text-base text-slate-900 mb-2">Presensi Digital Harian</h4>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        Pencatatan sesi kehadiran siswa per mata pelajaran dengan rekapitulasi status Hadir, Sakit, Izin, dan Alpa yang akurat.
                    </p>
                </div>

                <!-- Modul 3 -->
                <div class="p-6 rounded-2xl border border-slate-200 bg-white shadow-2xs hover:shadow-md transition">
                    <div class="w-12 h-12 rounded-xl bg-indigo-100 text-indigo-800 font-bold flex items-center justify-center text-xl mb-4">
                        📊
                    </div>
                    <h4 class="font-bold text-base text-slate-900 mb-2">Buku Nilai & Rapor</h4>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        Manajemen komponen nilai (Tugas, Harian, UTS, UAS) dengan pembobotan persentase resmi dan kalkulasi nilai akhir otomatis.
                    </p>
                </div>

                <!-- Modul 4 -->
                <div class="p-6 rounded-2xl border border-slate-200 bg-white shadow-2xs hover:shadow-md transition">
                    <div class="w-12 h-12 rounded-xl bg-amber-100 text-amber-800 font-bold flex items-center justify-center text-xl mb-4">
                        📖
                    </div>
                    <h4 class="font-bold text-base text-slate-900 mb-2">E-Learning & Bahan Ajar</h4>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        Distribusi bahan ajar digital, penugasan berbatas tenggat waktu, dan pemberian umpan balik langsung dari guru pengampu.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- 9. Footer Resmi Sekolah (Official Institutional Footer) -->
    <footer id="kontak" class="bg-slate-900 text-slate-400 py-12 border-t border-slate-800 mt-auto">
        <div class="max-w-7xl mx-auto px-4 sm:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8 pb-10 border-b border-slate-800 text-xs">
                
                <!-- Col 1: School Identity -->
                <div class="space-y-3.5 md:col-span-1">
                    <div class="flex items-center space-x-3">
                        <img src="{{ asset('images/logo-smpn2.png') }}" alt="Logo SMP Negeri 2 Subang" class="h-12 w-auto object-contain">
                        <div>
                            <span class="font-black text-white text-sm block">SMP Negeri 2 Subang</span>
                            <span class="text-slate-400 text-[11px]">Kabupaten Kuningan, Jawa Barat</span>
                        </div>
                    </div>
                    <p class="text-slate-400 leading-relaxed">
                        Portal Resmi Sistem Informasi Akademik dan E-Learning untuk peningkatan kualitas tata kelola dan akuntabilitas pendidikan sekolah.
                    </p>
                </div>

                <!-- Col 2: Alamat & Kontak -->
                <div class="space-y-2.5">
                    <h5 class="font-bold text-white text-sm mb-3">Alamat & Kontak Resmi</h5>
                    <p class="leading-relaxed">
                        <strong>Kampus:</strong> Jl. Raya Pamulihan - Tangkolo<br>
                        <strong>Kecamatan:</strong> Subang<br>
                        <strong>Kabupaten:</strong> Kuningan, Jawa Barat 45566<br>
                        <strong>Email TU:</strong> tu@smpn2subang.sch.id<br>
                        <strong>Helpdesk WA:</strong> (0232) 8892-SMPN2
                    </p>
                </div>

                <!-- Col 3: Legalitas & Identitas Satuan Pendidikan -->
                <div class="space-y-2.5">
                    <h5 class="font-bold text-white text-sm mb-3">Identitas Satuan Pendidikan</h5>
                    <p class="leading-relaxed">
                        <strong>NPSN:</strong> 20212952<br>
                        <strong>Bentuk Pendidikan:</strong> Sekolah Menengah Pertama (SMP)<br>
                        <strong>Status:</strong> Negeri<br>
                        <strong>Akreditasi:</strong> A (Unggul)<br>
                        <strong>Naungan:</strong> Dinas Pendidikan & Kebudayaan Kab. Kuningan
                    </p>
                </div>

                <!-- Col 4: Tautan Kedinasan Terkait -->
                <div class="space-y-2.5">
                    <h5 class="font-bold text-white text-sm mb-3">Tautan Instansi Terkait</h5>
                    <ul class="space-y-1.5 text-slate-300">
                        <li>
                            <a href="https://kemdikbud.go.id" target="_blank" rel="noopener noreferrer" class="hover:text-blue-400 transition flex items-center space-x-1.5">
                                <span>&bull;</span>
                                <span>Kemendikbudristek RI</span>
                            </a>
                        </li>
                        <li>
                            <a href="https://dapo.kemdikbud.go.id" target="_blank" rel="noopener noreferrer" class="hover:text-blue-400 transition flex items-center space-x-1.5">
                                <span>&bull;</span>
                                <span>Portal Dapodikdasmen</span>
                            </a>
                        </li>
                        <li>
                            <a href="https://kuningankab.go.id" target="_blank" rel="noopener noreferrer" class="hover:text-blue-400 transition flex items-center space-x-1.5">
                                <span>&bull;</span>
                                <span>Pemerintah Kabupaten Kuningan</span>
                            </a>
                        </li>
                        <li>
                            <a href="https://disdikbud.kuningankab.go.id" target="_blank" rel="noopener noreferrer" class="hover:text-blue-400 transition flex items-center space-x-1.5">
                                <span>&bull;</span>
                                <span>Disdikbud Kab. Kuningan</span>
                            </a>
                        </li>
                    </ul>
                </div>

            </div>

            <!-- Bottom Copyright Strip -->
            <div class="pt-6 flex flex-col sm:flex-row justify-between items-center text-xs text-slate-500 gap-2">
                <p>&copy; {{ date('Y') }} SMP Negeri 2 Subang, Kabupaten Kuningan. Seluruh hak cipta dilindungi.</p>
                <p class="text-slate-500">Dikelola secara resmi oleh Tim TIK & Bagian Tata Usaha SMPN 2 Subang</p>
            </div>
        </div>
    </footer>

</body>
</html>
