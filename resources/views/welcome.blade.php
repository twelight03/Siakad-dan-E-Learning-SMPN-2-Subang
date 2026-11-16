<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SIAKAD & E-Learning | SMP Negeri 2 Subang - Portal Resmi</title>

    <!-- Meta Description & SEO -->
    <meta name="description" content="Portal Resmi Sistem Informasi Akademik dan E-Learning Terpadu SMP Negeri 2 Subang, Kabupaten Kuningan, Jawa Barat.">
    <meta name="author" content="SMP Negeri 2 Subang">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800,900&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-50 text-slate-900 antialiased font-sans flex flex-col min-h-screen selection:bg-blue-800 selection:text-white">

    <!-- 1. TOP INSTITUTIONAL STRIP (GOVERNMENT IDENTITY) -->
    <div class="bg-slate-950 text-slate-200 text-xs py-2.5 px-4 sm:px-8 border-b border-blue-950">
        <div class="max-w-7xl mx-auto flex flex-col sm:flex-row justify-between items-center gap-2 text-center sm:text-left">
            <div class="flex items-center space-x-2.5">
                <!-- Bendera Merah Putih Mini -->
                <span class="inline-flex flex-col w-4 h-3 rounded-2xs overflow-hidden border border-slate-700 flex-shrink-0 shadow-2xs">
                    <span class="h-1.5 bg-red-600 w-full"></span>
                    <span class="h-1.5 bg-white w-full"></span>
                </span>
                <span class="font-bold tracking-wide text-slate-100">
                    Pemerintah Kabupaten Kuningan &bull; Dinas Pendidikan dan Kebudayaan
                </span>
            </div>
            <div class="flex items-center flex-wrap justify-center space-x-3 text-[11px] text-slate-300">
                <span>NPSN: <strong class="text-white font-mono">{{ $stats['npsn'] ?? '20212952' }}</strong></span>
                <span class="text-slate-600">&bull;</span>
                <span>Akreditasi: <strong class="text-amber-300 font-bold">{{ $stats['accreditation'] ?? 'A (Unggul)' }}</strong></span>
                <span class="text-slate-600">&bull;</span>
                <span>Status: <strong class="text-white">Negeri</strong></span>
                <span class="text-slate-600">&bull;</span>
                <span class="text-emerald-300 font-bold bg-emerald-950/80 px-2 py-0.5 rounded border border-emerald-700/60">
                    T.A. {{ $stats['active_year']->name ?? '2026/2027' }} ({{ ($stats['active_year']?->semester ?? 'odd') == 'odd' ? 'Ganjil' : 'Genap' }})
                </span>
            </div>
        </div>
    </div>

    <!-- 2. MAIN SCHOOL HEADER (KOP RESMI & NAVIGASI) -->
    <header class="bg-white border-b-2 border-slate-200 shadow-xs sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3 flex items-center justify-between gap-4">
            <!-- School Brand with Real Official Logo -->
            <a href="{{ url('/') }}" class="flex items-center space-x-3 group flex-shrink-0">
                <img 
                    src="{{ asset('images/logo-smpn2.png') }}" 
                    alt="Logo SMP Negeri 2 Subang" 
                    class="h-9 sm:h-10 w-auto object-contain flex-shrink-0 group-hover:scale-105 transition-transform"
                />
                <div>
                    <h1 class="text-sm sm:text-base font-black text-slate-950 tracking-tight leading-none group-hover:text-blue-800 transition-colors">
                        SMP NEGERI 2 SUBANG
                    </h1>
                    <p class="text-[11px] sm:text-xs font-extrabold text-blue-800 mt-0.5 tracking-wide">
                        Sistem Informasi Akademik & E-Learning (SIAKAD)
                    </p>
                    <p class="text-[10px] text-slate-500 font-medium hidden xl:block mt-0.5">
                        Jl. Raya Pamulihan - Tangkolo, Kec. Subang, Kab. Kuningan, Jawa Barat
                    </p>
                </div>
            </a>

            <!-- Navigation Links (Desktop) -->
            <nav class="hidden lg:flex items-center space-x-4 xl:space-x-6 text-xs font-bold text-slate-700 uppercase tracking-wider flex-shrink-0">
                <a href="#beranda" class="hover:text-blue-800 transition">Beranda</a>
                <a href="#sambutan" class="hover:text-blue-800 transition">Sambutan</a>
                <a href="#pengumuman" class="hover:text-blue-800 transition">Pengumuman</a>
                <a href="#data-pokok" class="hover:text-blue-800 transition">Data Pokok</a>
                <a href="#layanan" class="hover:text-blue-800 transition">Layanan</a>
                <a href="#kontak" class="hover:text-blue-800 transition">Kontak</a>
            </nav>

            <!-- Right Action (Login / Dashboard) -->
            <div class="flex items-center space-x-2.5 flex-shrink-0">
                @if (Route::has('login'))
                    @auth
                        <a href="{{ url('/dashboard') }}" class="inline-flex items-center justify-center px-4 py-2 bg-blue-800 hover:bg-blue-900 text-white rounded-lg text-xs sm:text-sm font-bold shadow-xs transition whitespace-nowrap flex-shrink-0 h-9 sm:h-10">
                            <span>Buka Dashboard</span>
                            <svg class="w-4 h-4 ms-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                            </svg>
                        </a>
                    @else
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="hidden sm:inline-flex items-center justify-center px-3.5 py-2 bg-slate-100 hover:bg-slate-200 text-slate-800 rounded-lg text-xs sm:text-sm font-bold transition border border-slate-300 whitespace-nowrap flex-shrink-0 h-9 sm:h-10">
                                <span>Pendaftaran Siswa</span>
                            </a>
                        @endif
                        <a href="{{ route('login') }}" class="inline-flex items-center justify-center px-4 py-2 bg-blue-800 hover:bg-blue-900 text-white rounded-lg text-xs sm:text-sm font-bold shadow-xs transition whitespace-nowrap flex-shrink-0 h-9 sm:h-10">
                            <svg class="w-4 h-4 me-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                            </svg>
                            <span>Login</span>
                        </a>
                    @endauth
                @endif
            </div>
        </div>
    </header>

    <!-- 3. ROYAL INSTITUTIONAL NAVY HERO SECTION -->
    <section id="beranda" class="bg-[#0B192C] text-white py-12 lg:py-16 relative overflow-hidden border-b-4 border-amber-400">
        <!-- Subtle Institutional Background Accents -->
        <div class="absolute -top-32 -right-32 w-96 h-96 bg-blue-600/15 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-32 -left-32 w-96 h-96 bg-blue-900/30 rounded-full blur-3xl pointer-events-none"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-8 relative z-10">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-center">
                
                <!-- Left Column: Official Welcome & High-Contrast Portals (7 Cols) -->
                <div class="lg:col-span-7 space-y-5">
                    <div class="inline-flex items-center space-x-2 px-3.5 py-1.5 rounded-lg bg-blue-900/90 border border-amber-400/60 text-amber-300 text-xs font-bold tracking-wide shadow-xs">
                        <span>🏛️ Portal Resmi Pelayanan KBM & Administrasi Sekolah</span>
                    </div>

                    <h2 class="text-2xl sm:text-4xl lg:text-5xl font-black text-white leading-tight tracking-tight">
                        Selamat Datang di SIAKAD SMP Negeri 2 Subang
                    </h2>

                    <p class="text-sm sm:text-base text-slate-100 leading-relaxed font-normal max-w-2xl">
                        Sistem Informasi Akademik dan E-Learning Terpadu untuk memfasilitasi transparansi pembelajaran digital, pemantauan presensi harian, penilaian kurikulum nasional, serta distribusi bahan ajar bagi seluruh civitas akademika.
                    </p>

                    <!-- 3 High-Contrast Quick Portal Action Cards -->
                    <div class="pt-2">
                        <p class="text-xs uppercase font-extrabold text-slate-200 tracking-wider mb-3 flex items-center">
                            <span class="w-2 h-2 rounded-full bg-amber-400 me-2"></span>
                            Pilih Jalur Akses Portal:
                        </p>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                            <!-- Portal Siswa -->
                            <a href="{{ route('login') }}" class="p-3.5 rounded-xl bg-white text-slate-900 hover:bg-emerald-50 border-2 border-emerald-400/80 shadow-md transition flex items-center space-x-3 group">
                                <div class="w-10 h-10 rounded-lg bg-emerald-600 text-white flex items-center justify-center font-bold text-xl flex-shrink-0 shadow-xs">
                                    🎓
                                </div>
                                <div class="text-left">
                                    <div class="font-extrabold text-sm text-slate-900 group-hover:text-emerald-700 transition">Portal Siswa</div>
                                    <div class="text-[11px] text-slate-600 font-semibold">Tugas & Nilai Rapor</div>
                                </div>
                            </a>

                            <!-- Portal Guru -->
                            <a href="{{ route('login') }}" class="p-3.5 rounded-xl bg-white text-slate-900 hover:bg-blue-50 border-2 border-blue-500/80 shadow-md transition flex items-center space-x-3 group">
                                <div class="w-10 h-10 rounded-lg bg-blue-700 text-white flex items-center justify-center font-bold text-xl flex-shrink-0 shadow-xs">
                                    👨‍🏫
                                </div>
                                <div class="text-left">
                                    <div class="font-extrabold text-sm text-slate-900 group-hover:text-blue-800 transition">Portal Guru</div>
                                    <div class="text-[11px] text-slate-600 font-semibold">Jurnal & Input Nilai</div>
                                </div>
                            </a>

                            <!-- Portal Tata Usaha / Admin -->
                            <a href="{{ route('login') }}" class="p-3.5 rounded-xl bg-white text-slate-900 hover:bg-amber-50 border-2 border-amber-400/80 shadow-md transition flex items-center space-x-3 group">
                                <div class="w-10 h-10 rounded-lg bg-amber-600 text-white flex items-center justify-center font-bold text-xl flex-shrink-0 shadow-xs">
                                    🏛️
                                </div>
                                <div class="text-left">
                                    <div class="font-extrabold text-sm text-slate-900 group-hover:text-amber-800 transition">Tata Usaha</div>
                                    <div class="text-[11px] text-slate-600 font-semibold">Rombel & KBM</div>
                                </div>
                            </a>
                        </div>
                    </div>

                    <!-- Security & Credential Guidance (High Contrast) -->
                    <div class="p-4 rounded-xl bg-blue-950/90 border border-blue-800 text-xs text-slate-100 flex items-start space-x-3 shadow-xs">
                        <svg class="w-5 h-5 text-amber-300 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                        </svg>
                        <span class="leading-relaxed font-medium">
                            Masuk menggunakan <strong class="text-amber-300 font-bold">Username (NISN untuk Siswa, NIP untuk Guru)</strong> dan kata sandi yang telah diterbitkan resmi oleh Bagian Tata Usaha SMPN 2 Subang.
                        </span>
                    </div>
                </div>

                <!-- Right Column: Real School Photo with Golden Institutional Framing (5 Cols) -->
                <div class="lg:col-span-5">
                    <div class="bg-white p-3 rounded-2xl shadow-2xl border-2 border-amber-400/80">
                        <div class="relative overflow-hidden rounded-xl aspect-4/3 bg-slate-900">
                            <img 
                                src="{{ asset('images/hero-school.jpg') }}" 
                                alt="Gedung Kampus SMP Negeri 2 Subang" 
                                class="w-full h-full object-cover object-center hover:scale-105 transition-transform duration-500"
                            />
                            <div class="absolute bottom-0 inset-x-0 bg-gradient-to-t from-slate-950 via-slate-950/80 to-transparent p-4 text-white">
                                <span class="text-[10px] font-black uppercase tracking-wider bg-amber-500 text-slate-950 px-2 py-0.5 rounded inline-block mb-1 shadow-xs">
                                    Kampus Utama
                                </span>
                                <p class="text-sm font-extrabold text-white leading-tight">Gedung Pembelajaran SMP Negeri 2 Subang</p>
                                <p class="text-xs text-slate-200">Kecamatan Subang, Kabupaten Kuningan</p>
                            </div>
                        </div>
                        <div class="p-3 text-center">
                            <p class="text-xs text-slate-800 font-bold">
                                📍 Jl. Raya Pamulihan - Tangkolo, Kec. Subang, Kab. Kuningan
                            </p>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- 4. PRAKATA KEPALA SEKOLAH (AUTHENTIC PRINCIPAL STATEMENT) -->
    <section id="sambutan" class="py-14 bg-white border-b border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-8">
            <div class="bg-slate-50 rounded-2xl border-2 border-slate-200 p-6 sm:p-10 shadow-xs">
                <div class="grid grid-cols-1 md:grid-cols-12 gap-8 items-center">
                    
                    <div class="md:col-span-3 text-center border-b md:border-b-0 md:border-r border-slate-200 pb-6 md:pb-0 md:pe-6">
                        <div class="w-24 h-24 sm:w-28 sm:h-28 mx-auto rounded-full bg-blue-900 text-white flex items-center justify-center font-bold text-4xl shadow-md border-4 border-amber-400 mb-3">
                            🏛️
                        </div>
                        <h4 class="font-black text-sm text-slate-900">{{ $stats['principal_name'] ?? 'Kepala Sekolah' }}</h4>
                        <p class="text-xs text-blue-800 font-bold">SMP Negeri 2 Subang</p>
                        <p class="text-[11px] text-slate-600 mt-0.5">Dinas Pendidikan Kab. Kuningan</p>
                    </div>

                    <div class="md:col-span-9 space-y-3">
                        <div class="inline-flex items-center space-x-1.5 text-blue-900 font-extrabold text-xs uppercase tracking-wider">
                            <span>Prakata Pimpinan Satuan Pendidikan</span>
                        </div>
                        <h3 class="text-xl sm:text-2xl font-black text-slate-900 leading-snug">
                            "Mewujudkan Tata Kelola Pendidikan yang Transparan, Akuntabel, dan Berkarakter"
                        </h3>
                        <p class="text-xs sm:text-sm text-slate-700 leading-relaxed font-medium">
                            &ldquo;{{ $stats['principal_speech'] ?? 'Penerapan Sistem Informasi Akademik (SIAKAD) ini merupakan langkah strategis SMP Negeri 2 Subang dalam mendigitalisasi layanan pendidikan. Melalui platform ini, kami memastikan keterbukaan informasi capaian belajar siswa, ketertiban administrasi mengajar dewan guru, serta kemudahan orang tua dalam mendampingi perkembangan putra-putrinya menuju insan yang cerdas dan berakhlak mulia.' }}&rdquo;
                        </p>
                        <div class="pt-2 flex flex-wrap items-center gap-3 text-xs font-bold text-slate-800">
                            <span class="inline-flex items-center space-x-1.5 px-3 py-1 rounded-md bg-emerald-100 text-emerald-800 border border-emerald-300">
                                <svg class="w-4 h-4 text-emerald-700" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                <span>Kurikulum Merdeka</span>
                            </span>
                            <span class="inline-flex items-center space-x-1.5 px-3 py-1 rounded-md bg-blue-100 text-blue-800 border border-blue-300">
                                <svg class="w-4 h-4 text-blue-700" fill="currentColor" viewBox="0 0 20 20">
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

    <!-- 5. PAPAN INFORMASI & AGENDA KBM SEKOLAH -->
    <section id="pengumuman" class="py-14 bg-slate-100/80 border-b border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                
                <!-- Left: Papan Informasi Akademik Terkini (8 Cols) -->
                <div class="lg:col-span-8">
                    <div class="flex items-center justify-between pb-3 mb-5 border-b-2 border-slate-300">
                        <div class="flex items-center space-x-2">
                            <span class="w-3 h-3 rounded-full bg-blue-800"></span>
                            <h3 class="font-black text-base sm:text-lg text-slate-900 uppercase tracking-wide">
                                Papan Informasi Akademik Terkini
                            </h3>
                        </div>
                        <span class="text-xs font-extrabold text-blue-900 bg-blue-100 px-3 py-1 rounded-md border border-blue-300">
                            T.A. {{ $stats['active_year']->name ?? '2026/2027' }} Aktif
                        </span>
                    </div>

                    <div class="space-y-4">
                        <!-- Agenda 1: KBM Standar -->
                        <div class="p-4 sm:p-5 rounded-xl border border-slate-300 bg-white hover:border-blue-500 transition flex items-start space-x-4 shadow-xs">
                            <div class="w-14 h-14 rounded-xl bg-blue-900 text-white flex flex-col items-center justify-center font-black flex-shrink-0 text-xs shadow-xs">
                                <span class="text-[9px] font-bold text-amber-300 uppercase">KBM</span>
                                <span class="text-sm leading-none mt-0.5">AKTIF</span>
                            </div>
                            <div class="flex-1">
                                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-1">
                                    <h4 class="font-extrabold text-sm sm:text-base text-slate-900">
                                        Kegiatan Belajar Mengajar (KBM) Semester {{ ($stats['active_year']?->semester ?? 'odd') == 'odd' ? 'Ganjil' : 'Genap' }}
                                    </h4>
                                    <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Edaran Sekolah</span>
                                </div>
                                <p class="text-xs sm:text-sm text-slate-700 mt-1 leading-relaxed">
                                    KBM berlangsung pukul <strong>07:00 - 14:00 WIB</strong> (Senin s/d Kamis) dan pukul <strong>07:00 - 11:15 WIB</strong> (Khusus Jumat). Peserta didik wajib hadir tepat waktu dan menjaga ketertiban kehadiran minimum 85%.
                                </p>
                            </div>
                        </div>

                        <!-- Agenda 2: E-Learning & Tugas -->
                        <div class="p-4 sm:p-5 rounded-xl border border-slate-300 bg-white hover:border-emerald-500 transition flex items-start space-x-4 shadow-xs">
                            <div class="w-14 h-14 rounded-xl bg-emerald-700 text-white flex flex-col items-center justify-center font-black flex-shrink-0 text-xs shadow-xs">
                                <span class="text-[9px] font-bold text-emerald-200 uppercase">TUGAS</span>
                                <span class="text-sm leading-none mt-0.5">ONLINE</span>
                            </div>
                            <div class="flex-1">
                                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-1">
                                    <h4 class="font-extrabold text-sm sm:text-base text-slate-900">
                                        Pemanfaatan Bahan Ajar & Pengumpulan Tugas Daring
                                    </h4>
                                    <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Akademik</span>
                                </div>
                                <p class="text-xs sm:text-sm text-slate-700 mt-1 leading-relaxed">
                                    Materi pembelajaran berbasis dokumen (PDF) dan lembar tugas diunggah langsung oleh guru mapel. Siswa dapat mengunggah hasil pekerjaan sebelum batas waktu berakhir.
                                </p>
                            </div>
                        </div>

                        <!-- Agenda 3: Penilaian Rapor -->
                        <div class="p-4 sm:p-5 rounded-xl border border-slate-300 bg-white hover:border-amber-500 transition flex items-start space-x-4 shadow-xs">
                            <div class="w-14 h-14 rounded-xl bg-amber-600 text-white flex flex-col items-center justify-center font-black flex-shrink-0 text-xs shadow-xs">
                                <span class="text-[9px] font-bold text-amber-100 uppercase">RAPOR</span>
                                <span class="text-sm leading-none mt-0.5">NILAI</span>
                            </div>
                            <div class="flex-1">
                                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-1">
                                    <h4 class="font-extrabold text-sm sm:text-base text-slate-900">
                                        Transparansi Penilaian Harian, Formatif, dan Sumatif
                                    </h4>
                                    <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Kurikulum</span>
                                </div>
                                <p class="text-xs sm:text-sm text-slate-700 mt-1 leading-relaxed">
                                    Perhitungan akumulasi nilai tugas, kehadiran, PTS, dan PAS diproses secara otomatis sesuai bobot persentase kurikulum yang berlaku di SMPN 2 Subang.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right: Jam KBM & Helpdesk Tata Usaha (4 Cols) -->
                <div class="lg:col-span-4 space-y-4">
                    <!-- Jam Operasional Sekolah -->
                    <div class="bg-white p-5 rounded-2xl border-2 border-slate-200 shadow-xs">
                        <h4 class="font-black text-sm text-slate-900 pb-2 mb-3 border-b border-slate-200 flex items-center space-x-2">
                            <span>⏰ Jam Pelayanan & KBM Sekolah</span>
                        </h4>
                        <ul class="text-xs text-slate-700 space-y-2.5">
                            <li class="flex justify-between items-center">
                                <span class="font-bold text-slate-800">Senin s/d Kamis:</span>
                                <span class="font-black text-blue-900 bg-blue-50 px-2.5 py-1 rounded border border-blue-200">07:00 - 14:00 WIB</span>
                            </li>
                            <li class="flex justify-between items-center">
                                <span class="font-bold text-slate-800">Jumat (KBM & Shalat):</span>
                                <span class="font-black text-teal-900 bg-teal-50 px-2.5 py-1 rounded border border-teal-200">07:00 - 11:15 WIB</span>
                            </li>
                            <li class="flex justify-between items-center">
                                <span class="font-bold text-slate-800">Layanan Tata Usaha:</span>
                                <span class="font-black text-slate-900 bg-slate-100 px-2.5 py-1 rounded border border-slate-300">07:30 - 15:00 WIB</span>
                            </li>
                            <li class="flex justify-between items-center">
                                <span class="font-bold text-slate-800">Sabtu & Minggu:</span>
                                <span class="text-red-700 font-black bg-red-100 px-2.5 py-1 rounded border border-red-300">Libur KBM</span>
                            </li>
                        </ul>
                    </div>

                    <!-- Bantuan Akun & Helpdesk Resmi -->
                    <div class="bg-blue-950 p-5 rounded-2xl text-white shadow-xs border-2 border-amber-400/80">
                        <div class="flex items-center space-x-2 text-amber-300 font-black text-sm pb-2 mb-2 border-b border-blue-900">
                            <span>📞 Bantuan Akun & Tata Usaha</span>
                        </div>
                        <p class="text-xs text-slate-100 leading-relaxed mb-4 font-normal">
                            Bagi siswa baru atau guru yang mengalami kendala masuk akun, silakan hubungi Ruang Tata Usaha Gedung Utama SMPN 2 Subang.
                        </p>
                        <a href="{{ route('login') }}" class="block text-center py-2.5 px-4 bg-amber-400 hover:bg-amber-300 text-slate-950 rounded-lg text-xs font-black transition shadow-sm">
                            Buka Halaman Masuk Portal &rarr;
                        </a>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- 6. DATA POKOK SATUAN PENDIDIKAN (STATISTIK DAPODIK) -->
    <section id="data-pokok" class="py-14 bg-white border-b border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-8">
            <div class="text-center max-w-2xl mx-auto mb-10">
                <span class="text-xs font-black uppercase tracking-wider text-blue-900 bg-blue-100 px-3.5 py-1 rounded-full border border-blue-300">
                    Profil Satuan Pendidikan
                </span>
                <h3 class="text-2xl sm:text-3xl font-black text-slate-950 mt-2.5">
                    Data Pokok Pendidikan Sekolah
                </h3>
                <p class="text-xs sm:text-sm text-slate-600 mt-1 font-medium">
                    Rekapitulasi data operasional terverifikasi dalam pangkalan data resmi SIAKAD SMP Negeri 2 Subang
                </p>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 sm:gap-6">
                <!-- Stat 1 -->
                <div class="bg-slate-50 p-6 rounded-2xl border-2 border-slate-200 shadow-xs text-center hover:border-blue-600 transition group">
                    <div class="text-3xl sm:text-4xl font-black text-blue-800 font-mono group-hover:scale-105 transition-transform">
                        {{ $stats['total_students'] ?? 648 }}
                    </div>
                    <div class="text-sm font-black text-slate-900 mt-2">Peserta Didik</div>
                    <div class="text-[11px] text-slate-600 font-semibold">{{ $stats['students_label'] ?? 'Siswa aktif terdaftar' }}</div>
                </div>

                <!-- Stat 2 -->
                <div class="bg-slate-50 p-6 rounded-2xl border-2 border-slate-200 shadow-xs text-center hover:border-emerald-600 transition group">
                    <div class="text-3xl sm:text-4xl font-black text-emerald-700 font-mono group-hover:scale-105 transition-transform">
                        {{ $stats['total_teachers'] ?? 38 }}
                    </div>
                    <div class="text-sm font-black text-slate-900 mt-2">Tenaga Pendidik</div>
                    <div class="text-[11px] text-slate-600 font-semibold">{{ $stats['teachers_label'] ?? 'Guru pengampu bersertifikasi' }}</div>
                </div>

                <!-- Stat 3 -->
                <div class="bg-slate-50 p-6 rounded-2xl border-2 border-slate-200 shadow-xs text-center hover:border-indigo-600 transition group">
                    <div class="text-3xl sm:text-4xl font-black text-indigo-700 font-mono group-hover:scale-105 transition-transform">
                        {{ $stats['total_classes'] ?? 18 }}
                    </div>
                    <div class="text-sm font-black text-slate-900 mt-2">Rombel Kelas</div>
                    <div class="text-[11px] text-slate-600 font-semibold">{{ $stats['classes_label'] ?? 'Tingkat VII, VIII, dan IX' }}</div>
                </div>

                <!-- Stat 4 -->
                <div class="bg-slate-50 p-6 rounded-2xl border-2 border-slate-200 shadow-xs text-center hover:border-amber-600 transition group">
                    <div class="text-3xl sm:text-4xl font-black text-amber-700 font-mono group-hover:scale-105 transition-transform">
                        {{ $stats['total_subjects'] ?? 11 }}
                    </div>
                    <div class="text-sm font-black text-slate-900 mt-2">Mata Pelajaran</div>
                    <div class="text-[11px] text-slate-600 font-semibold">{{ $stats['subjects_label'] ?? 'Kurikulum Nasional' }}</div>
                </div>
            </div>
        </div>
    </section>

    <!-- 7. VISI, MISI & KARAKTER PENDIDIKAN -->
    <section class="py-14 bg-slate-100/70 border-b border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-8">
            <div class="grid grid-cols-1 md:grid-cols-12 gap-8 items-center">
                
                <div class="md:col-span-5 space-y-4">
                    <span class="text-xs font-black uppercase tracking-wider text-blue-900 bg-blue-100 px-3.5 py-1 rounded-full border border-blue-300">
                        Visi Sekolah
                    </span>
                    <h3 class="text-2xl sm:text-3xl font-black text-slate-950 leading-tight">
                        "Terwujudnya Insan Berakhlak Mulia, Cerdas, Terampil, dan Berwawasan Lingkungan"
                    </h3>
                    <p class="text-xs sm:text-sm text-slate-700 leading-relaxed font-medium">
                        SMP Negeri 2 Subang bertekad mencetak generasi penerus bangsa yang berintegritas tinggi, menguasai ilmu pengetahuan dan teknologi, serta berakar teguh pada kearifan budaya dan nilai-nilai Pancasila.
                    </p>
                </div>

                <div class="md:col-span-7 grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="p-5 rounded-xl border border-slate-300 bg-white shadow-2xs">
                        <div class="w-8 h-8 rounded-lg bg-blue-800 text-white flex items-center justify-center font-black text-sm mb-2.5">
                            1
                        </div>
                        <h4 class="font-extrabold text-sm text-slate-900 mb-1">Penguatan Karakter</h4>
                        <p class="text-xs text-slate-700 leading-relaxed">
                            Membina keimanan, ketakwaan, serta pembiasaan budi pekerti luhur dalam keseharian KBM.
                        </p>
                    </div>

                    <div class="p-5 rounded-xl border border-slate-300 bg-white shadow-2xs">
                        <div class="w-8 h-8 rounded-lg bg-emerald-700 text-white flex items-center justify-center font-black text-sm mb-2.5">
                            2
                        </div>
                        <h4 class="font-extrabold text-sm text-slate-900 mb-1">Kecakapan Akademik</h4>
                        <p class="text-xs text-slate-700 leading-relaxed">
                            Menyelenggarakan proses pembelajaran aktif, kritis, dan berbasis literasi-numerasi terpadu.
                        </p>
                    </div>

                    <div class="p-5 rounded-xl border border-slate-300 bg-white shadow-2xs">
                        <div class="w-8 h-8 rounded-lg bg-amber-600 text-white flex items-center justify-center font-black text-sm mb-2.5">
                            3
                        </div>
                        <h4 class="font-extrabold text-sm text-slate-900 mb-1">Transformasi Digital</h4>
                        <p class="text-xs text-slate-700 leading-relaxed">
                            Mengintegrasikan teknologi informasi dalam KBM, presensi daring, dan penilaian rapor digital.
                        </p>
                    </div>

                    <div class="p-5 rounded-xl border border-slate-300 bg-white shadow-2xs">
                        <div class="w-8 h-8 rounded-lg bg-indigo-700 text-white flex items-center justify-center font-black text-sm mb-2.5">
                            4
                        </div>
                        <h4 class="font-extrabold text-sm text-slate-900 mb-1">Pelestarian Lingkungan</h4>
                        <p class="text-xs text-slate-700 leading-relaxed">
                            Mewujudkan lingkungan sekolah yang bersih, hijau, asri, dan mendukung kesehatan belajar.
                        </p>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- 8. LAYANAN UTAMA SIAKAD TERPADU (4 PILAR PELAYANAN) -->
    <section id="layanan" class="py-14 bg-white border-b border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-8">
            <div class="text-center max-w-2xl mx-auto mb-10">
                <span class="text-xs font-black uppercase tracking-wider text-blue-900 bg-blue-100 px-3.5 py-1 rounded-full border border-blue-300">
                    Modul Terintegrasi
                </span>
                <h3 class="text-2xl sm:text-3xl font-black text-slate-950 mt-2.5">
                    Layanan Sistem Informasi Akademik
                </h3>
                <p class="text-xs sm:text-sm text-slate-600 mt-1 font-medium">
                    Fasilitas terpadu yang dirancang untuk mendukung mutu tata kelola pendidikan di SMP Negeri 2 Subang
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Modul 1 -->
                <div class="p-6 rounded-2xl border-2 border-slate-200 bg-white shadow-2xs hover:shadow-md hover:border-blue-600 transition">
                    <div class="w-12 h-12 rounded-xl bg-blue-100 text-blue-900 font-bold flex items-center justify-center text-2xl mb-4">
                        📁
                    </div>
                    <h4 class="font-black text-base text-slate-900 mb-2">Administrasi Rombel</h4>
                    <p class="text-xs text-slate-700 leading-relaxed">
                        Pengelolaan master data siswa, guru, pembagian rombongan belajar, dan penataan jadwal pelajaran resmi tanpa bentrok.
                    </p>
                </div>

                <!-- Modul 2 -->
                <div class="p-6 rounded-2xl border-2 border-slate-200 bg-white shadow-2xs hover:shadow-md hover:border-emerald-600 transition">
                    <div class="w-12 h-12 rounded-xl bg-emerald-100 text-emerald-900 font-bold flex items-center justify-center text-2xl mb-4">
                        📋
                    </div>
                    <h4 class="font-black text-base text-slate-900 mb-2">Presensi Digital Harian</h4>
                    <p class="text-xs text-slate-700 leading-relaxed">
                        Pencatatan sesi kehadiran siswa per mata pelajaran dengan rekapitulasi status Hadir, Sakit, Izin, dan Alpa yang akurat.
                    </p>
                </div>

                <!-- Modul 3 -->
                <div class="p-6 rounded-2xl border-2 border-slate-200 bg-white shadow-2xs hover:shadow-md hover:border-indigo-600 transition">
                    <div class="w-12 h-12 rounded-xl bg-indigo-100 text-indigo-900 font-bold flex items-center justify-center text-2xl mb-4">
                        📊
                    </div>
                    <h4 class="font-black text-base text-slate-900 mb-2">Buku Nilai & Rapor</h4>
                    <p class="text-xs text-slate-700 leading-relaxed">
                        Manajemen komponen nilai (Tugas, Harian, UTS, UAS) dengan pembobotan persentase resmi dan kalkulasi nilai akhir otomatis.
                    </p>
                </div>

                <!-- Modul 4 -->
                <div class="p-6 rounded-2xl border-2 border-slate-200 bg-white shadow-2xs hover:shadow-md hover:border-amber-600 transition">
                    <div class="w-12 h-12 rounded-xl bg-amber-100 text-amber-900 font-bold flex items-center justify-center text-2xl mb-4">
                        📖
                    </div>
                    <h4 class="font-black text-base text-slate-900 mb-2">E-Learning & Bahan Ajar</h4>
                    <p class="text-xs text-slate-700 leading-relaxed">
                        Distribusi bahan ajar digital, penugasan berbatas tenggat waktu, dan pemberian umpan balik langsung dari guru pengampu.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- 9. OFFICIAL INSTITUTIONAL FOOTER -->
    <footer id="kontak" class="bg-[#0B192C] text-slate-300 py-12 border-t-4 border-amber-400 mt-auto">
        <div class="max-w-7xl mx-auto px-4 sm:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8 pb-10 border-b border-blue-900/60 text-xs">
                
                <!-- Col 1: School Identity -->
                <div class="space-y-3.5 md:col-span-1">
                    <div class="flex items-center space-x-3">
                        <img src="{{ asset('images/logo-smpn2.png') }}" alt="Logo SMP Negeri 2 Subang" class="h-10 w-auto object-contain">
                        <div>
                            <span class="font-black text-white text-sm block">SMP Negeri 2 Subang</span>
                            <span class="text-slate-300 text-[11px] font-medium">Kabupaten Kuningan, Jawa Barat</span>
                        </div>
                    </div>
                    <p class="text-slate-300 leading-relaxed font-normal">
                        Portal Resmi Sistem Informasi Akademik dan E-Learning untuk peningkatan kualitas tata kelola dan akuntabilitas pendidikan sekolah.
                    </p>
                </div>

                <!-- Col 2: Alamat & Kontak -->
                <div class="space-y-2.5">
                    <h5 class="font-black text-amber-300 text-sm mb-3 uppercase tracking-wider">Alamat & Kontak Resmi</h5>
                    <p class="leading-relaxed text-slate-200 font-medium">
                        <strong>Kampus:</strong> {{ $stats['school_address'] ?? 'Jl. Raya Pamulihan - Tangkolo, Kec. Subang, Kab. Kuningan' }}<br>
                        <strong>Email TU:</strong> {{ $stats['school_email'] ?? 'tu@smpn2subang.sch.id' }}<br>
                        <strong>Helpdesk WA:</strong> {{ $stats['school_phone'] ?? '(0232) 8892-SMPN2' }}
                    </p>
                </div>

                <!-- Col 3: Identitas Pendidikan -->
                <div class="space-y-2.5">
                    <h5 class="font-black text-amber-300 text-sm mb-3 uppercase tracking-wider">Identitas Satuan Pendidikan</h5>
                    <p class="leading-relaxed text-slate-200 font-medium">
                        <strong>NPSN:</strong> {{ $stats['npsn'] ?? '20212952' }}<br>
                        <strong>Bentuk Pendidikan:</strong> SMP Negeri<br>
                        <strong>Status:</strong> Terakreditasi {{ $stats['accreditation'] ?? 'A (Unggul)' }}<br>
                        <strong>Naungan:</strong> Disdikbud Kabupaten Kuningan
                    </p>
                </div>

                <!-- Col 4: Tautan Kedinasan -->
                <div class="space-y-2.5">
                    <h5 class="font-black text-amber-300 text-sm mb-3 uppercase tracking-wider">Tautan Kedinasan Terkait</h5>
                    <ul class="space-y-2 text-slate-200 font-medium">
                        <li>
                            <a href="https://kemdikbud.go.id" target="_blank" rel="noopener noreferrer" class="hover:text-amber-300 transition flex items-center space-x-1.5">
                                <span>&bull;</span>
                                <span>Kemendikbudristek RI</span>
                            </a>
                        </li>
                        <li>
                            <a href="https://dapo.kemdikbud.go.id" target="_blank" rel="noopener noreferrer" class="hover:text-amber-300 transition flex items-center space-x-1.5">
                                <span>&bull;</span>
                                <span>Portal Dapodikdasmen</span>
                            </a>
                        </li>
                        <li>
                            <a href="https://kuningankab.go.id" target="_blank" rel="noopener noreferrer" class="hover:text-amber-300 transition flex items-center space-x-1.5">
                                <span>&bull;</span>
                                <span>Pemerintah Kab. Kuningan</span>
                            </a>
                        </li>
                        <li>
                            <a href="https://disdikbud.kuningankab.go.id" target="_blank" rel="noopener noreferrer" class="hover:text-amber-300 transition flex items-center space-x-1.5">
                                <span>&bull;</span>
                                <span>Disdikbud Kab. Kuningan</span>
                            </a>
                        </li>
                    </ul>
                </div>

            </div>

            <!-- Bottom Copyright Strip -->
            <div class="pt-6 flex flex-col sm:flex-row justify-between items-center text-xs text-slate-400 gap-2 font-medium">
                <p>&copy; {{ date('Y') }} SMP Negeri 2 Subang, Kabupaten Kuningan. Seluruh hak cipta dilindungi.</p>
                <p class="text-slate-400">Dikelola resmi oleh Tim TIK & Bagian Tata Usaha SMPN 2 Subang</p>
            </div>
        </div>
    </footer>

</body>
</html>
