<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SIAKAD SMPN 2 Subang</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 text-gray-800 antialiased font-sans">
    <div class="min-h-screen flex flex-col justify-between">
        <!-- Header -->
        <header class="bg-white border-b border-gray-100 shadow-sm sticky top-0 z-20">
            <div class="max-w-7xl mx-auto px-3 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
                <div class="flex items-center space-x-2.5 sm:space-x-3">
                    <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl bg-blue-600 flex items-center justify-center text-white font-extrabold text-sm sm:text-lg shadow-sm shadow-blue-200">
                        S2
                    </div>
                    <div>
                        <div class="font-extrabold text-sm sm:text-lg text-gray-900 leading-tight">SIAKAD SMPN 2 Subang</div>
                        <div class="text-[11px] sm:text-xs text-gray-500 hidden sm:block">Sistem Informasi Akademik & E-Learning</div>
                    </div>
                </div>
                <div>
                    @if (Route::has('login'))
                        @auth
                            <a href="{{ url('/dashboard') }}" class="text-xs sm:text-sm font-semibold text-blue-600 hover:text-blue-800">
                                Dashboard &rarr;
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="px-3 sm:px-4 py-2 bg-blue-600 text-white rounded-lg text-xs sm:text-sm font-semibold hover:bg-blue-700 shadow-sm transition whitespace-nowrap">
                                Masuk ke Sistem
                            </a>
                        @endauth
                    @endif
                </div>
            </div>
        </header>

        <!-- Hero Section -->
        <main class="flex-1 flex items-center">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12 text-center">
                <span class="inline-block px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-xs font-semibold tracking-wide uppercase mb-4">
                    Portal Resmi Akademik & E-Learning
                </span>
                <h1 class="text-4xl sm:text-5xl font-extrabold text-gray-900 tracking-tight leading-tight mb-4">
                    Mewujudkan Pengelolaan Pendidikan yang Terintegrasi dan Akuntabel
                </h1>
                <p class="text-lg text-gray-600 max-w-2xl mx-auto mb-8">
                    Sistem Informasi Akademik SMP Negeri 2 Subang memfasilitasi pendataan siswa, penugasan guru, jadwal pembelajaran, absensi digital, penilaian terstruktur, dan e-learning interaktif.
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    @auth
                        <a href="{{ url('/dashboard') }}" class="px-6 py-3 bg-blue-600 text-white font-semibold rounded-lg shadow hover:bg-blue-700 transition">
                            Masuk ke Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="px-6 py-3 bg-blue-600 text-white font-semibold rounded-lg shadow hover:bg-blue-700 transition">
                            Masuk Akun Pengguna
                        </a>
                    @endauth
                </div>

                <!-- Features Highlight -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-16 text-left">
                    <div class="p-6 bg-white rounded-xl shadow-sm border border-gray-100">
                        <div class="text-blue-600 font-bold mb-2">Manajemen Akademik</div>
                        <p class="text-sm text-gray-600">Pengelolaan terpusat untuk data siswa, guru, kelas, mata pelajaran, penempatan kelas, dan jadwal pelajaran bebas bentrok.</p>
                    </div>
                    <div class="p-6 bg-white rounded-xl shadow-sm border border-gray-100">
                        <div class="text-green-600 font-bold mb-2">Absensi & Nilai Terpadu</div>
                        <p class="text-sm text-gray-600">Pencatatan sesi absensi harian dan pembobotan nilai tugas, UTS, hingga UAS secara akurat dan transparan.</p>
                    </div>
                    <div class="p-6 bg-white rounded-xl shadow-sm border border-gray-100">
                        <div class="text-purple-600 font-bold mb-2">E-Learning & Tugas</div>
                        <p class="text-sm text-gray-600">Distribusi materi pembelajaran digital dan pengumpulan tugas siswa dengan kontrol otorisasi ketat.</p>
                    </div>
                </div>
            </div>
        </main>

        <!-- Footer -->
        <footer class="bg-white border-t border-gray-100 py-6 text-center text-xs text-gray-500">
            &copy; {{ date('Y') }} SMP Negeri 2 Subang. Seluruh hak cipta dilindungi.
        </footer>
    </div>
</body>
</html>
