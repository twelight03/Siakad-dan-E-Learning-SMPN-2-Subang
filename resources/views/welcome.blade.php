<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SIAKAD SMPN 2 Subang - Portal Akademik & E-Learning</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-50 text-slate-800 antialiased font-sans flex flex-col min-h-screen">

    <!-- Top Navigation Bar -->
    <header class="bg-slate-900/95 backdrop-blur-md border-b border-slate-800 text-white sticky top-0 z-50 transition-all">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <!-- Brand Logo -->
            <a href="{{ url('/') }}" class="flex items-center space-x-3 group">
                <div class="w-10 h-10 rounded-xl bg-blue-600 flex items-center justify-center text-white font-extrabold text-base shadow-md shadow-blue-900/50 group-hover:scale-105 transition-transform">
                    S2
                </div>
                <div>
                    <div class="font-extrabold text-base sm:text-lg tracking-tight leading-tight text-white group-hover:text-blue-400 transition-colors">
                        SIAKAD SMPN 2 SUBANG
                    </div>
                    <div class="text-[10px] sm:text-xs text-slate-400 font-medium">
                        Kec. Subang, Kab. Kuningan
                    </div>
                </div>
            </a>

            <!-- Desktop Navigation Links -->
            <nav class="hidden md:flex items-center space-x-6 text-sm font-medium text-slate-300">
                <a href="#hero" class="hover:text-white transition">Beranda</a>
                <a href="#statistik" class="hover:text-white transition">Statistik</a>
                <a href="#fitur" class="hover:text-white transition">Fitur Portal</a>
                <a href="#peran" class="hover:text-white transition">Hak Akses</a>
                <a href="#kontak" class="hover:text-white transition">Kontak</a>
            </nav>

            <!-- Action Button -->
            <div>
                @if (Route::has('login'))
                    @auth
                        <a href="{{ url('/dashboard') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-500 text-white rounded-lg text-xs sm:text-sm font-semibold shadow-md shadow-blue-900/40 transition">
                            <span>Buka Dashboard</span>
                            <svg class="w-4 h-4 ms-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                            </svg>
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-500 text-white rounded-lg text-xs sm:text-sm font-semibold shadow-md shadow-blue-900/40 transition">
                            <span>Masuk ke Sistem</span>
                            <svg class="w-4 h-4 ms-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                            </svg>
                        </a>
                    @endauth
                @endif
            </div>
        </div>
    </header>

    <!-- Hero Section with Real School Photo Background -->
    <section id="hero" class="relative bg-slate-950 text-white py-20 lg:py-28 overflow-hidden">
        <!-- Background Image with Overlay -->
        <div class="absolute inset-0 z-0">
            <img 
                src="{{ asset('images/hero-school.jpg') }}" 
                alt="Gedung SMP Negeri 2 Subang" 
                class="w-full h-full object-cover object-center filter scale-105 transform motion-safe:transition-transform motion-safe:duration-1000"
            />
            <!-- Dark Modern Gradient Overlay -->
            <div class="absolute inset-0 bg-gradient-to-r from-slate-950/95 via-slate-900/85 to-blue-950/75 backdrop-blur-[0.5px]"></div>
        </div>

        <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="max-w-3xl">
                <!-- Official Badge -->
                <div class="inline-flex items-center space-x-2 px-3 py-1.5 rounded-full bg-blue-500/20 border border-blue-400/30 text-blue-300 text-xs font-semibold mb-6 shadow-sm">
                    <span class="w-2 h-2 rounded-full bg-blue-400 animate-pulse"></span>
                    <span>Portal Resmi Akademik & E-Learning</span>
                </div>

                <!-- Main Headline -->
                <h1 class="text-3xl sm:text-5xl lg:text-6xl font-extrabold tracking-tight leading-tight sm:leading-none text-white mb-6">
                    Mewujudkan Pendidikan yang <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-400 to-sky-300">Terintegrasi</span> & Akuntabel
                </h1>

                <!-- Subtitle -->
                <p class="text-base sm:text-lg text-slate-300 leading-relaxed mb-8 max-w-2xl">
                    Sistem Informasi Akademik SMP Negeri 2 Subang memfasilitasi pendataan siswa, penugasan guru, jadwal pembelajaran, absensi digital harian, penilaian terstruktur, dan e-learning interaktif.
                </p>

                <!-- Action Buttons -->
                <div class="flex flex-col sm:flex-row gap-3.5 sm:items-center">
                    @auth
                        <a href="{{ url('/dashboard') }}" class="px-6 py-3.5 bg-blue-600 hover:bg-blue-500 text-white font-bold rounded-xl shadow-lg shadow-blue-600/30 transition text-center text-sm sm:text-base">
                            Buka Dashboard Utama &rarr;
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="px-6 py-3.5 bg-blue-600 hover:bg-blue-500 text-white font-bold rounded-xl shadow-lg shadow-blue-600/30 transition text-center text-sm sm:text-base">
                            Masuk Akun Pengguna &rarr;
                        </a>
                    @endauth
                    <a href="#fitur" class="px-6 py-3.5 bg-white/10 hover:bg-white/20 text-white font-semibold rounded-xl border border-white/20 transition text-center text-sm sm:text-base backdrop-blur-xs">
                        Pelajari Fitur Portal
                    </a>
                </div>

                <!-- School Location Note (from Signboard) -->
                <div class="mt-8 pt-6 border-t border-white/10 flex items-center space-x-2 text-xs text-slate-400">
                    <svg class="w-4 h-4 text-blue-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    <span>Jl. Raya Pamulihan - Tangkolo, Kec. Subang, Kab. Kuningan, Jawa Barat</span>
                </div>
            </div>
        </div>
    </section>

    <!-- Statistics Section (Floating Bar) -->
    <section id="statistik" class="relative z-20 -mt-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white rounded-2xl shadow-xl shadow-slate-200/50 border border-slate-200/80 p-4 sm:p-6 lg:p-8">
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 divide-y sm:divide-y-0 sm:divide-x divide-slate-100">
                <!-- Stat 1 -->
                <div class="p-2 sm:p-3 text-center sm:text-left">
                    <div class="text-xs font-bold uppercase tracking-wider text-slate-400">Total Siswa</div>
                    <div class="mt-1 text-2xl sm:text-4xl font-extrabold text-blue-600">
                        {{ $stats['total_students'] ?? 0 }}
                    </div>
                    <div class="text-[11px] text-slate-500 mt-0.5">Siswa aktif terdaftar</div>
                </div>

                <!-- Stat 2 -->
                <div class="p-2 sm:p-3 text-center sm:text-left pt-4 sm:pt-3">
                    <div class="text-xs font-bold uppercase tracking-wider text-slate-400">Tenaga Pendidik</div>
                    <div class="mt-1 text-2xl sm:text-4xl font-extrabold text-emerald-600">
                        {{ $stats['total_teachers'] ?? 0 }}
                    </div>
                    <div class="text-[11px] text-slate-500 mt-0.5">Guru pengampu mapel</div>
                </div>

                <!-- Stat 3 -->
                <div class="p-2 sm:p-3 text-center sm:text-left pt-4 sm:pt-3">
                    <div class="text-xs font-bold uppercase tracking-wider text-slate-400">Rombel / Kelas</div>
                    <div class="mt-1 text-2xl sm:text-4xl font-extrabold text-purple-600">
                        {{ $stats['total_classes'] ?? 0 }}
                    </div>
                    <div class="text-[11px] text-slate-500 mt-0.5">Kelas aktif terdata</div>
                </div>

                <!-- Stat 4 -->
                <div class="p-2 sm:p-3 text-center sm:text-left pt-4 sm:pt-3">
                    <div class="text-xs font-bold uppercase tracking-wider text-slate-400">Mata Pelajaran</div>
                    <div class="mt-1 text-2xl sm:text-4xl font-extrabold text-amber-600">
                        {{ $stats['total_subjects'] ?? 0 }}
                    </div>
                    <div class="text-[11px] text-slate-500 mt-0.5">Kurikulum akademik</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Main Features Section -->
    <section id="fitur" class="py-16 sm:py-24 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-3xl mx-auto mb-14">
            <span class="text-xs font-bold text-blue-600 uppercase tracking-widest bg-blue-50 px-3 py-1 rounded-full border border-blue-100">
                Layanan & Fitur Unggulan
            </span>
            <h2 class="text-2xl sm:text-4xl font-extrabold text-slate-900 mt-3 tracking-tight">
                Dirancang untuk Efisiensi & Transparansi Akademik
            </h2>
            <p class="text-slate-600 text-sm sm:text-base mt-3">
                Seluruh alur akademik diintegrasikan secara digital dengan otorisasi keamanan Role-Based Access Control (RBAC).
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Feature 1 -->
            <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-xs hover:shadow-md hover:border-blue-200 transition group">
                <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center font-bold mb-5 group-hover:bg-blue-600 group-hover:text-white transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                </div>
                <h3 class="text-base font-bold text-slate-900 mb-2">Manajemen Akademik</h3>
                <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
                    Pendataan siswa, guru, rombel kelas, kurikulum mapel, dan penempatan siswa per tahun ajaran secara terpusat.
                </p>
            </div>

            <!-- Feature 2 -->
            <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-xs hover:shadow-md hover:border-emerald-200 transition group">
                <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold mb-5 group-hover:bg-emerald-600 group-hover:text-white transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                    </svg>
                </div>
                <h3 class="text-base font-bold text-slate-900 mb-2">Presensi Harian Digital</h3>
                <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
                    Guru mencatat kehadiran per sesi kelas (Hadir, Izin, Sakit, Alpa) yang otomatis terekap ke profil siswa.
                </p>
            </div>

            <!-- Feature 3 -->
            <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-xs hover:shadow-md hover:border-purple-200 transition group">
                <div class="w-12 h-12 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center font-bold mb-5 group-hover:bg-purple-600 group-hover:text-white transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z" />
                    </svg>
                </div>
                <h3 class="text-base font-bold text-slate-900 mb-2">Penilaian Terstruktur</h3>
                <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
                    Pembobotan nilai tugas, kuis, UTS, dan UAS dengan skala 0–100 serta perhitungan nilai akhir transparan.
                </p>
            </div>

            <!-- Feature 4 -->
            <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-xs hover:shadow-md hover:border-amber-200 transition group">
                <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center font-bold mb-5 group-hover:bg-amber-600 group-hover:text-white transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                </div>
                <h3 class="text-base font-bold text-slate-900 mb-2">E-Learning & Tugas</h3>
                <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
                    Distribusi materi digital privat, pengumpulan jawaban tugas siswa, dan pemberian feedback nilai dari guru.
                </p>
            </div>
        </div>
    </section>

    <!-- Role Access Section -->
    <section id="peran" class="py-16 bg-slate-100/70 border-y border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto mb-12">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-widest">Akses Multi-Peran</span>
                <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 mt-2">Hak Akses Pengguna Terintegrasi</h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Admin Card -->
                <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-xs">
                    <div class="inline-block px-3 py-1 bg-purple-100 text-purple-800 rounded-full text-xs font-bold mb-4">
                        Administrator
                    </div>
                    <h3 class="font-bold text-lg text-slate-900 mb-3">Operator Sekolah</h3>
                    <ul class="text-xs sm:text-sm text-slate-600 space-y-2.5">
                        <li class="flex items-center"><span class="text-purple-600 me-2 font-bold">✓</span> Kelola akun & status pengguna</li>
                        <li class="flex items-center"><span class="text-purple-600 me-2 font-bold">✓</span> Master data siswa, guru, kelas, mapel</li>
                        <li class="flex items-center"><span class="text-purple-600 me-2 font-bold">✓</span> Penugasan mengajar & jadwal pelajaran</li>
                        <li class="flex items-center"><span class="text-purple-600 me-2 font-bold">✓</span> Rekapitulasi laporan & log audit keamanan</li>
                    </ul>
                </div>

                <!-- Teacher Card -->
                <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-xs">
                    <div class="inline-block px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-xs font-bold mb-4">
                        Tenaga Pendidik
                    </div>
                    <h3 class="font-bold text-lg text-slate-900 mb-3">Bapak / Ibu Guru</h3>
                    <ul class="text-xs sm:text-sm text-slate-600 space-y-2.5">
                        <li class="flex items-center"><span class="text-blue-600 me-2 font-bold">✓</span> Dashboard jadwal mengajar harian</li>
                        <li class="flex items-center"><span class="text-blue-600 me-2 font-bold">✓</span> Pencatatan kehadiran & absensi siswa</li>
                        <li class="flex items-center"><span class="text-blue-600 me-2 font-bold">✓</span> Input nilai komponen & nilai akhir</li>
                        <li class="flex items-center"><span class="text-blue-600 me-2 font-bold">✓</span> Unggah materi belajar & koreksi tugas</li>
                    </ul>
                </div>

                <!-- Student Card -->
                <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-xs">
                    <div class="inline-block px-3 py-1 bg-emerald-100 text-emerald-800 rounded-full text-xs font-bold mb-4">
                        Peserta Didik
                    </div>
                    <h3 class="font-bold text-lg text-slate-900 mb-3">Siswa / Siswi</h3>
                    <ul class="text-xs sm:text-sm text-slate-600 space-y-2.5">
                        <li class="flex items-center"><span class="text-emerald-600 me-2 font-bold">✓</span> Jadwal pembelajaran kelas pribadi</li>
                        <li class="flex items-center"><span class="text-emerald-600 me-2 font-bold">✓</span> Unduh materi ajar sesuai enrollment</li>
                        <li class="flex items-center"><span class="text-emerald-600 me-2 font-bold">✓</span> Pengumpulan jawaban tugas online</li>
                        <li class="flex items-center"><span class="text-emerald-600 me-2 font-bold">✓</span> Rekap nilai & rekam kehadiran pribadi</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer Section -->
    <footer id="kontak" class="bg-slate-900 text-slate-400 py-12 border-t border-slate-800 mt-auto">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-8 pb-8 border-b border-slate-800 text-sm">
                <!-- Col 1 -->
                <div>
                    <div class="flex items-center space-x-2.5 mb-3 text-white">
                        <div class="w-8 h-8 rounded-lg bg-blue-600 flex items-center justify-center font-bold text-sm">
                            S2
                        </div>
                        <span class="font-bold text-base">SMP Negeri 2 Subang</span>
                    </div>
                    <p class="text-xs text-slate-400 leading-relaxed">
                        Sistem Informasi Manajemen Akademik dan E-Learning resmi untuk meningkatkan mutu dan akuntabilitas pendidikan.
                    </p>
                </div>

                <!-- Col 2 -->
                <div>
                    <h4 class="font-semibold text-white mb-3 text-sm">Lokasi & Alamat Resmi</h4>
                    <p class="text-xs text-slate-400 leading-relaxed">
                        Jl. Raya Pamulihan - Tangkolo<br>
                        Kecamatan Subang, Kabupaten Kuningan<br>
                        Jawa Barat 45566
                    </p>
                </div>

                <!-- Col 3 -->
                <div>
                    <h4 class="font-semibold text-white mb-3 text-sm">Informasi Semester</h4>
                    <p class="text-xs text-slate-400 leading-relaxed">
                        Tahun Ajaran: <span class="text-white font-semibold">{{ $stats['active_year']->name ?? '2026/2027' }}</span><br>
                        Semester: <span class="text-white font-semibold">{{ ($stats['active_year']?->semester == 'odd') ? 'Ganjil' : 'Genap' }}</span><br>
                        Jam Operasional: Senin – Jumat (07:00 – 15:00 WIB)
                    </p>
                </div>
            </div>

            <div class="flex flex-col sm:flex-row justify-between items-center text-xs text-slate-500 gap-3">
                <p>&copy; {{ date('Y') }} SMP Negeri 2 Subang. Seluruh hak cipta dilindungi.</p>
                <p class="text-slate-500">Dikembangkan untuk Tugas Akademik Keamanan Cyber</p>
            </div>
        </div>
    </footer>

</body>
</html>
