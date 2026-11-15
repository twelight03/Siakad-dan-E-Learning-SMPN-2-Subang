<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SIAKAD SMP Negeri 2 Subang - Portal Resmi Akademik</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-100 text-slate-800 antialiased font-sans flex flex-col min-h-screen">

    <!-- 1. Top Government / Institutional Strip -->
    <div class="bg-slate-800 text-slate-300 text-xs py-2 px-4 sm:px-8 border-b border-slate-700">
        <div class="max-w-7xl mx-auto flex flex-col sm:flex-row justify-between items-center gap-1 sm:gap-4 text-center sm:text-left">
            <div class="flex items-center space-x-2">
                <span class="inline-block w-2 h-2 rounded-full bg-emerald-400"></span>
                <span class="font-medium tracking-wide">Pemerintah Kabupaten Kuningan &bull; Dinas Pendidikan dan Kebudayaan</span>
            </div>
            <div class="flex items-center space-x-4 text-[11px] text-slate-400">
                <span>NPSN: <strong class="text-slate-200">20212952</strong></span>
                <span>&bull;</span>
                <span>Status: <strong class="text-slate-200">Negeri</strong></span>
                <span>&bull;</span>
                <span class="text-emerald-300 font-semibold">T.A. {{ $stats['active_year']->name ?? '2026/2027' }} ({{ $stats['active_year']?->semester == 'odd' ? 'Ganjil' : 'Genap' }})</span>
            </div>
        </div>
    </div>

    <!-- 2. Main School Header (Kop Portal Sekolah) -->
    <header class="bg-white border-b border-slate-200 shadow-xs sticky top-0 z-40">
        <div class="max-w-7xl mx-auto px-4 sm:px-8 py-3.5 flex items-center justify-between">
            <!-- School Brand with Real Logo -->
            <a href="{{ url('/') }}" class="flex items-center space-x-3.5 group">
                <img 
                    src="{{ asset('images/logo-smpn2.png') }}" 
                    alt="Logo SMP Negeri 2 Subang" 
                    class="h-12 sm:h-14 w-auto object-contain flex-shrink-0 group-hover:scale-105 transition-transform"
                />
                <div>
                    <h1 class="text-base sm:text-xl font-extrabold text-slate-900 tracking-tight leading-none group-hover:text-blue-700 transition-colors">
                        SMP NEGERI 2 SUBANG
                    </h1>
                    <p class="text-xs sm:text-sm font-semibold text-blue-600 mt-0.5">
                        Sistem Informasi Akademik & E-Learning (SIAKAD)
                    </p>
                    <p class="text-[11px] text-slate-400 hidden sm:block">
                        Jl. Raya Pamulihan - Tangkolo, Kec. Subang, Kab. Kuningan, Jawa Barat
                    </p>
                </div>
            </a>

            <!-- Right Action -->
            <div class="flex items-center space-x-3">
                @if (Route::has('login'))
                    @auth
                        <a href="{{ url('/dashboard') }}" class="inline-flex items-center px-4 py-2 bg-blue-700 hover:bg-blue-800 text-white rounded-lg text-xs sm:text-sm font-bold shadow-sm transition">
                            <span>Buka Dashboard</span>
                            <svg class="w-4 h-4 ms-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                            </svg>
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="inline-flex items-center px-4 sm:px-5 py-2.5 bg-blue-700 hover:bg-blue-800 text-white rounded-lg text-xs sm:text-sm font-bold shadow-sm transition">
                            <svg class="w-4 h-4 me-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                            </svg>
                            <span>Masuk ke Portal</span>
                        </a>
                    @endauth
                @endif
            </div>
        </div>
    </header>

    <!-- 3. Real School Hero Section: Split Layout (2 Columns) -->
    <section class="bg-gradient-to-b from-blue-900 to-slate-900 text-white py-12 lg:py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-center">
                
                <!-- Left Column: Official Welcome & Quick Portals (7 Cols) -->
                <div class="lg:col-span-7 space-y-6">
                    <div class="inline-flex items-center space-x-2 px-3 py-1 rounded-md bg-blue-800/80 border border-blue-600/50 text-blue-200 text-xs font-semibold">
                        <span>🏛️ Portal Pelayanan KBM & Administrasi Sekolah</span>
                    </div>

                    <h2 class="text-2xl sm:text-4xl font-extrabold text-white leading-tight">
                        Selamat Datang di SIAKAD & E-Learning SMP Negeri 2 Subang
                    </h2>

                    <p class="text-sm sm:text-base text-slate-200 leading-relaxed max-w-2xl">
                        Pusat layanan digital bagi civitas akademika untuk kemudahan akses jadwal pelajaran, pencatatan absensi harian terpadu, transparansi penilaian rapor, serta distribusi materi dan tugas e-learning.
                    </p>

                    <!-- 3 Quick Access Role Buttons -->
                    <div class="pt-2">
                        <p class="text-xs uppercase font-bold text-slate-300 tracking-wider mb-3">Pilih Jalur Akses Masuk:</p>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                            <!-- Portal Siswa -->
                            <a href="{{ route('login') }}" class="p-3.5 rounded-xl bg-white/10 hover:bg-white/20 border border-white/15 transition flex items-center space-x-3 group">
                                <div class="w-10 h-10 rounded-lg bg-emerald-500 text-white flex items-center justify-center font-bold text-lg flex-shrink-0 shadow-sm">
                                    🎓
                                </div>
                                <div class="text-left">
                                    <div class="font-bold text-sm text-white group-hover:text-emerald-300 transition">Portal Siswa</div>
                                    <div class="text-[11px] text-slate-300">Tugas, Nilai & Jadwal</div>
                                </div>
                            </a>

                            <!-- Portal Guru -->
                            <a href="{{ route('login') }}" class="p-3.5 rounded-xl bg-white/10 hover:bg-white/20 border border-white/15 transition flex items-center space-x-3 group">
                                <div class="w-10 h-10 rounded-lg bg-blue-500 text-white flex items-center justify-center font-bold text-lg flex-shrink-0 shadow-sm">
                                    👨‍🏫
                                </div>
                                <div class="text-left">
                                    <div class="font-bold text-sm text-white group-hover:text-blue-300 transition">Portal Guru</div>
                                    <div class="text-[11px] text-slate-300">Absensi & Input Nilai</div>
                                </div>
                            </a>

                            <!-- Portal Admin/TU -->
                            <a href="{{ route('login') }}" class="p-3.5 rounded-xl bg-white/10 hover:bg-white/20 border border-white/15 transition flex items-center space-x-3 group">
                                <div class="w-10 h-10 rounded-lg bg-purple-500 text-white flex items-center justify-center font-bold text-lg flex-shrink-0 shadow-sm">
                                    🛠️
                                </div>
                                <div class="text-left">
                                    <div class="font-bold text-sm text-white group-hover:text-purple-300 transition">Operator / TU</div>
                                    <div class="text-[11px] text-slate-300">Master Data & Log</div>
                                </div>
                            </a>
                        </div>
                    </div>

                    <!-- Security Notice Box -->
                    <div class="p-3 rounded-lg bg-blue-950/60 border border-blue-800/60 flex items-start space-x-2.5 text-xs text-blue-200">
                        <svg class="w-4 h-4 text-blue-400 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                        </svg>
                        <span>Gunakan <strong>Username (NIS / NIP)</strong> dan kata sandi yang telah diterbitkan resmi oleh pihak sekolah untuk masuk.</span>
                    </div>
                </div>

                <!-- Right Column: Real School Photo Framed (5 Cols) -->
                <div class="lg:col-span-5">
                    <div class="bg-white p-2 sm:p-2.5 rounded-2xl shadow-2xl border border-slate-200/50">
                        <div class="relative overflow-hidden rounded-xl aspect-4/3 bg-slate-200">
                            <img 
                                src="{{ asset('images/hero-school.jpg') }}" 
                                alt="Gedung Kampus SMP Negeri 2 Subang" 
                                class="w-full h-full object-cover object-center hover:scale-105 transition-transform duration-500"
                            />
                            <div class="absolute bottom-0 inset-x-0 bg-gradient-to-t from-slate-900/90 via-slate-900/40 to-transparent p-3 text-white">
                                <span class="text-[10px] font-bold uppercase tracking-wider bg-blue-600 px-2 py-0.5 rounded text-white inline-block mb-1">
                                    Kampus Sekolah
                                </span>
                                <p class="text-xs font-bold leading-tight">Gedung Utama SMPN 2 Subang</p>
                                <p class="text-[10px] text-slate-300">Kec. Subang, Kab. Kuningan</p>
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

    <!-- 4. Papan Informasi & Pengumuman Sekolah (Bulletin Board Style) -->
    <section class="py-10 bg-white border-b border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                
                <!-- Left: Papan Pengumuman Akademik (8 Cols) -->
                <div class="lg:col-span-8">
                    <div class="flex items-center justify-between pb-3 mb-4 border-b border-slate-200">
                        <div class="flex items-center space-x-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-blue-600"></span>
                            <h3 class="font-bold text-base text-slate-900 uppercase tracking-wide">Papan Informasi Akademik</h3>
                        </div>
                        <span class="text-xs font-semibold text-slate-500">T.A. {{ $stats['active_year']->name ?? '2026/2027' }}</span>
                    </div>

                    <div class="space-y-3.5">
                        <!-- Item 1 -->
                        <div class="p-4 rounded-xl border border-slate-200 bg-slate-50 hover:bg-white hover:border-blue-300 transition flex items-start space-x-3.5">
                            <div class="w-10 h-10 rounded-lg bg-blue-100 text-blue-700 flex flex-col items-center justify-center font-bold flex-shrink-0 text-xs">
                                <span>KBM</span>
                                <span class="text-[9px] font-normal">Aktif</span>
                            </div>
                            <div class="flex-1">
                                <h4 class="font-bold text-sm text-slate-900">Pelaksanaan Kegiatan Belajar Mengajar (KBM) Semester Ganjil</h4>
                                <p class="text-xs text-slate-600 mt-1 leading-relaxed">
                                    Seluruh siswa dan guru diimbau memantau jadwal pelajaran dan absensi digital harian secara berkala melalui sistem SIAKAD.
                                </p>
                            </div>
                        </div>

                        <!-- Item 2 -->
                        <div class="p-4 rounded-xl border border-slate-200 bg-slate-50 hover:bg-white hover:border-emerald-300 transition flex items-start space-x-3.5">
                            <div class="w-10 h-10 rounded-lg bg-emerald-100 text-emerald-700 flex flex-col items-center justify-center font-bold flex-shrink-0 text-xs">
                                <span>TUGAS</span>
                                <span class="text-[9px] font-normal">Online</span>
                            </div>
                            <div class="flex-1">
                                <h4 class="font-bold text-sm text-slate-900">Distribusi Bahan Ajar & Pengumpulan Tugas E-Learning</h4>
                                <p class="text-xs text-slate-600 mt-1 leading-relaxed">
                                    Pengumpulan berkas tugas siswa wajib memperhatikan batas akhir (deadline) yang telah ditetapkan oleh guru mata pelajaran masing-masing.
                                </p>
                            </div>
                        </div>

                        <!-- Item 3 -->
                        <div class="p-4 rounded-xl border border-slate-200 bg-slate-50 hover:bg-white hover:border-amber-300 transition flex items-start space-x-3.5">
                            <div class="w-10 h-10 rounded-lg bg-amber-100 text-amber-700 flex flex-col items-center justify-center font-bold flex-shrink-0 text-xs">
                                <span>RAPOR</span>
                                <span class="text-[9px] font-normal">Nilai</span>
                            </div>
                            <div class="flex-1">
                                <h4 class="font-bold text-sm text-slate-900">Transparansi Nilai Tugas, Harian, UTS, dan UAS</h4>
                                <p class="text-xs text-slate-600 mt-1 leading-relaxed">
                                    Penilaian diinput secara terstruktur dengan pembobotan persentase resmi sehingga rekapitulasi nilai akhir dapat dipantau langsung oleh siswa.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right: Kalender & Jam Operasional (4 Cols) -->
                <div class="lg:col-span-4 space-y-4">
                    <div class="bg-slate-50 p-5 rounded-2xl border border-slate-200">
                        <h4 class="font-bold text-sm text-slate-900 pb-2 mb-3 border-b border-slate-200 flex items-center space-x-2">
                            <span>⏰ Jam Operasional KBM</span>
                        </h4>
                        <ul class="text-xs text-slate-600 space-y-2">
                            <li class="flex justify-between">
                                <span class="font-medium">Senin – Kamis:</span>
                                <span class="font-bold text-slate-800">07:00 – 14:00 WIB</span>
                            </li>
                            <li class="flex justify-between">
                                <span class="font-medium">Jumat:</span>
                                <span class="font-bold text-slate-800">07:00 – 11:30 WIB</span>
                            </li>
                            <li class="flex justify-between">
                                <span class="font-medium">Sabtu & Minggu:</span>
                                <span class="text-red-600 font-semibold">Libur</span>
                            </li>
                        </ul>
                    </div>

                    <div class="bg-blue-50 p-5 rounded-2xl border border-blue-200">
                        <h4 class="font-bold text-sm text-blue-950 pb-2 mb-2 border-b border-blue-200">
                            📞 Layanan Bantuan Akun
                        </h4>
                        <p class="text-xs text-blue-900 leading-relaxed mb-3">
                            Apabila mengalami kendala lupa kata sandi atau kendala masuk akun, silakan hubungi Ruang Tata Usaha (TU) atau Operator Sekolah.
                        </p>
                        <a href="{{ route('login') }}" class="block text-center py-2 px-3 bg-blue-700 hover:bg-blue-800 text-white rounded-lg text-xs font-bold transition">
                            Halaman Masuk Sistem &rarr;
                        </a>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- 5. Data Pokok Satuan Pendidikan (Statistik Real-time) -->
    <section class="py-12 bg-slate-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-8">
            <div class="text-center max-w-2xl mx-auto mb-8">
                <h3 class="text-xl sm:text-2xl font-extrabold text-slate-900">Data Pokok Pendidikan Sekolah</h3>
                <p class="text-xs sm:text-sm text-slate-600 mt-1">Data statistik aktif yang terkelola dalam basis data SIAKAD</p>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 sm:gap-6">
                <!-- Stat 1 -->
                <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-2xs text-center">
                    <div class="text-2xl sm:text-4xl font-extrabold text-blue-700 font-mono">
                        {{ $stats['total_students'] ?? 0 }}
                    </div>
                    <div class="text-xs font-bold text-slate-800 mt-1">Peserta Didik</div>
                    <div class="text-[11px] text-slate-500">Siswa aktif terdaftar</div>
                </div>

                <!-- Stat 2 -->
                <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-2xs text-center">
                    <div class="text-2xl sm:text-4xl font-extrabold text-emerald-700 font-mono">
                        {{ $stats['total_teachers'] ?? 0 }}
                    </div>
                    <div class="text-xs font-bold text-slate-800 mt-1">Tenaga Pendidik</div>
                    <div class="text-[11px] text-slate-500">Guru pengampu mapel</div>
                </div>

                <!-- Stat 3 -->
                <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-2xs text-center">
                    <div class="text-2xl sm:text-4xl font-extrabold text-purple-700 font-mono">
                        {{ $stats['total_classes'] ?? 0 }}
                    </div>
                    <div class="text-xs font-bold text-slate-800 mt-1">Rombel Kelas</div>
                    <div class="text-[11px] text-slate-500">Kelas VII, VIII, IX</div>
                </div>

                <!-- Stat 4 -->
                <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-2xs text-center">
                    <div class="text-2xl sm:text-4xl font-extrabold text-amber-700 font-mono">
                        {{ $stats['total_subjects'] ?? 0 }}
                    </div>
                    <div class="text-xs font-bold text-slate-800 mt-1">Mata Pelajaran</div>
                    <div class="text-[11px] text-slate-500">Kurikulum resmi</div>
                </div>
            </div>
        </div>
    </section>

    <!-- 6. Layanan Utama SIAKAD (4 Kotak Bersih) -->
    <section class="py-12 bg-white border-t border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-8">
            <div class="text-center max-w-2xl mx-auto mb-10">
                <h3 class="text-xl sm:text-2xl font-extrabold text-slate-900">Ruang Lingkup Layanan Sistem</h3>
                <p class="text-xs sm:text-sm text-slate-600 mt-1">Modul utama yang dirancang untuk mendukung operasional sekolah</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Modul 1 -->
                <div class="p-5 rounded-xl border border-slate-200 bg-slate-50/50">
                    <div class="w-10 h-10 rounded-lg bg-blue-100 text-blue-800 font-bold flex items-center justify-center text-base mb-3">
                        📁
                    </div>
                    <h4 class="font-bold text-sm text-slate-900 mb-1.5">Administrasi & Penempatan</h4>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        Pengelolaan master data siswa, guru, pembagian rombongan belajar, dan plotting jadwal pelajaran anti-bentrok.
                    </p>
                </div>

                <!-- Modul 2 -->
                <div class="p-5 rounded-xl border border-slate-200 bg-slate-50/50">
                    <div class="w-10 h-10 rounded-lg bg-emerald-100 text-emerald-800 font-bold flex items-center justify-center text-base mb-3">
                        📋
                    </div>
                    <h4 class="font-bold text-sm text-slate-900 mb-1.5">Presensi Siswa Digital</h4>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        Pencatatan sesi kehadiran siswa per mata pelajaran dengan rekapitulasi kehadiran Hadir, Sakit, Izin, dan Alpa.
                    </p>
                </div>

                <!-- Modul 3 -->
                <div class="p-5 rounded-xl border border-slate-200 bg-slate-50/50">
                    <div class="w-10 h-10 rounded-lg bg-purple-100 text-purple-800 font-bold flex items-center justify-center text-base mb-3">
                        📊
                    </div>
                    <h4 class="font-bold text-sm text-slate-900 mb-1.5">Penilaian & Buku Nilai</h4>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        Manajemen komponen nilai (Tugas, Harian, UTS, UAS) dengan batas bobot 100% dan rekap nilai akhir otomatis.
                    </p>
                </div>

                <!-- Modul 4 -->
                <div class="p-5 rounded-xl border border-slate-200 bg-slate-50/50">
                    <div class="w-10 h-10 rounded-lg bg-amber-100 text-amber-800 font-bold flex items-center justify-center text-base mb-3">
                        📖
                    </div>
                    <h4 class="font-bold text-sm text-slate-900 mb-1.5">E-Learning & Tugas</h4>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        Unggah materi digital terlindungi, penugasan dengan tenggat waktu, dan penilaian jawaban siswa disertai feedback guru.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- 7. Footer Resmi Sekolah -->
    <footer class="bg-slate-900 text-slate-400 py-10 border-t border-slate-800 mt-auto">
        <div class="max-w-7xl mx-auto px-4 sm:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 pb-8 border-b border-slate-800 text-xs">
                <!-- Col 1 -->
                <div class="space-y-3">
                    <div class="flex items-center space-x-3">
                        <img src="{{ asset('images/logo-smpn2.png') }}" alt="Logo" class="h-10 w-auto object-contain">
                        <div>
                            <span class="font-bold text-white text-sm block">SMP Negeri 2 Subang</span>
                            <span class="text-slate-400 text-[11px]">Kabupaten Kuningan, Jawa Barat</span>
                        </div>
                    </div>
                    <p class="text-slate-400 leading-relaxed">
                        Portal Resmi Sistem Informasi Akademik dan E-Learning Terpadu untuk peningkatan kualitas tata kelola dan akuntabilitas pendidikan.
                    </p>
                </div>

                <!-- Col 2 -->
                <div class="space-y-2">
                    <h5 class="font-bold text-white text-sm mb-2.5">Alamat & Identitas Satuan Pendidikan</h5>
                    <p class="leading-relaxed">
                        <strong>Kampus:</strong> Jl. Raya Pamulihan - Tangkolo<br>
                        <strong>Kecamatan:</strong> Subang<br>
                        <strong>Kabupaten:</strong> Kuningan, Jawa Barat 45566<br>
                        <strong>NPSN:</strong> 20212952 &bull; <strong>Bentuk Pendidikan:</strong> SMP Negeri
                    </p>
                </div>

                <!-- Col 3 -->
                <div class="space-y-2">
                    <h5 class="font-bold text-white text-sm mb-2.5">Informasi Sistem</h5>
                    <p class="leading-relaxed">
                        <strong>Tahun Ajaran:</strong> {{ $stats['active_year']->name ?? '2026/2027' }}<br>
                        <strong>Semester:</strong> {{ $stats['active_year']?->semester == 'odd' ? 'Ganjil' : 'Genap' }} (Aktif)<br>
                        <strong>Platform:</strong> SIAKAD Berbasis Web & RBAC Security<br>
                        <strong>Keperluan:</strong> Tugas Proyek Akademik Keamanan Cyber
                    </p>
                </div>
            </div>

            <div class="pt-6 flex flex-col sm:flex-row justify-between items-center text-xs text-slate-500 gap-2">
                <p>&copy; {{ date('Y') }} SMP Negeri 2 Subang. Seluruh hak cipta dilindungi.</p>
                <p class="text-slate-500">Dikembangkan untuk Tugas Mata Kuliah Keamanan Cyber</p>
            </div>
        </div>
    </footer>

</body>
</html>
