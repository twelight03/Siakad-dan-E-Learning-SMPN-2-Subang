<x-guest-layout>
    <div class="min-h-screen flex flex-col lg:flex-row bg-slate-50">
        
        <!-- Left Column: School Visual & Official Identity (Desktop) -->
        <div class="lg:w-7/12 relative flex flex-col justify-between p-6 sm:p-10 lg:p-12 text-white bg-slate-900 overflow-hidden"
             style="background-image: linear-gradient(135deg, rgba(15, 23, 42, 0.94) 0%, rgba(30, 58, 138, 0.88) 55%, rgba(15, 23, 42, 0.96) 100%), url('{{ asset('images/hero-school.jpg') }}'); background-size: cover; background-position: center;">
            
            <!-- Top Branding Header -->
            <div class="relative z-10">
                <div class="flex items-center space-x-3.5 pb-5 border-b border-white/15">
                    <img src="{{ asset('images/logo-smpn2.png') }}" alt="Logo SMP Negeri 2 Subang" class="w-14 h-14 object-contain drop-shadow-md">
                    <div>
                        <div class="text-[11px] font-semibold tracking-wider uppercase text-blue-200 leading-tight">
                            Pemerintah Kabupaten Kuningan • Dinas Pendidikan
                        </div>
                        <h1 class="text-xl sm:text-2xl font-black tracking-tight text-white leading-tight">
                            SMP NEGERI 2 SUBANG
                        </h1>
                        <div class="text-xs text-slate-300 font-mono">
                            NPSN: 20212952 • Terakreditasi "A"
                        </div>
                    </div>
                </div>
            </div>

            <!-- Middle Identity / Value Proposition -->
            <div class="relative z-10 my-8 lg:my-auto max-w-xl">
                <div class="inline-flex items-center space-x-2 px-3 py-1 rounded-full bg-blue-500/20 border border-blue-400/30 text-blue-200 text-xs font-semibold mb-4">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    <span>Portal Akademik & E-Learning Terpadu</span>
                </div>
                <h2 class="text-2xl sm:text-4xl font-extrabold text-white leading-tight tracking-tight mb-3">
                    Mewujudkan Insan Berakhlak, Cerdas, dan Berprestasi.
                </h2>
                <p class="text-sm sm:text-base text-slate-200 leading-relaxed font-normal mb-6">
                    Sistem informasi resmi untuk pengelolaan data akademik, presensi digital, rekapitulasi nilai rapor, serta penugasan siswa SMP Negeri 2 Subang.
                </p>

                <!-- Quick Demo Login Helper for Evaluator/Dosen -->
                <div class="p-4 rounded-xl bg-slate-900/80 backdrop-blur-md border border-white/20 shadow-xl">
                    <div class="flex items-center justify-between mb-2.5">
                        <span class="text-xs font-bold uppercase tracking-wider text-amber-300 flex items-center">
                            <svg class="w-4 h-4 me-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                            </svg>
                            Akun Demo Pengujian (Sekali Klik)
                        </span>
                        <span class="text-[10px] text-slate-400">Password: <code class="text-amber-200">password</code></span>
                    </div>
                    <div class="grid grid-cols-3 gap-2">
                        <button type="button" onclick="fillDemo('admin', 'password')" class="px-2.5 py-2 bg-purple-600/30 hover:bg-purple-600/50 border border-purple-400/40 rounded-lg text-xs font-bold text-white text-center transition flex flex-col items-center">
                            <span class="text-purple-300 text-[10px]">Portal</span>
                            <span>👑 Admin</span>
                        </button>
                        <button type="button" onclick="fillDemo('guru', 'password')" class="px-2.5 py-2 bg-blue-600/30 hover:bg-blue-600/50 border border-blue-400/40 rounded-lg text-xs font-bold text-white text-center transition flex flex-col items-center">
                            <span class="text-blue-300 text-[10px]">Portal</span>
                            <span>👨‍🏫 Guru</span>
                        </button>
                        <button type="button" onclick="fillDemo('siswa', 'password')" class="px-2.5 py-2 bg-emerald-600/30 hover:bg-emerald-600/50 border border-emerald-400/40 rounded-lg text-xs font-bold text-white text-center transition flex flex-col items-center">
                            <span class="text-emerald-300 text-[10px]">Portal</span>
                            <span>👨‍🎓 Siswa</span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Bottom Security Notice -->
            <div class="relative z-10 pt-5 border-t border-white/15 flex items-center justify-between text-xs text-slate-300">
                <div class="flex items-center space-x-2">
                    <svg class="w-4 h-4 text-emerald-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    <span>Enkripsi SSL 256-Bit & Proteksi Rate Limiting Aktif</span>
                </div>
                <div class="hidden sm:block text-[11px] text-slate-400">
                    Kec. Subang, Kab. Kuningan
                </div>
            </div>
        </div>

        <!-- Right Column: Login Form -->
        <div class="lg:w-5/12 flex flex-col justify-between p-6 sm:p-10 lg:p-12 bg-white">
            
            <!-- Top Navigation -->
            <div class="flex items-center justify-between mb-6">
                <a href="/" class="inline-flex items-center text-xs font-bold text-slate-600 hover:text-blue-700 transition">
                    <svg class="w-4 h-4 me-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Kembali ke Beranda
                </a>
                <span class="text-[11px] font-semibold px-2.5 py-1 rounded bg-slate-100 text-slate-600 border border-slate-200">
                    T.A. 2026/2027 Ganjil
                </span>
            </div>

            <!-- Form Content Box -->
            <div class="w-full max-w-md mx-auto my-auto" x-data="{ showPassword: false }">
                
                <!-- Mobile Only Logo & Title -->
                <div class="lg:hidden text-center mb-6">
                    <img src="{{ asset('images/logo-smpn2.png') }}" alt="Logo SMP Negeri 2 Subang" class="w-16 h-16 mx-auto object-contain mb-2">
                    <h2 class="text-xl font-black text-slate-900">SMP NEGERI 2 SUBANG</h2>
                    <p class="text-xs text-slate-500">Sistem Informasi Akademik & E-Learning</p>
                </div>

                <div class="mb-6">
                    <h2 class="text-2xl font-black text-slate-900 tracking-tight">Masuk ke Portal</h2>
                    <p class="text-sm text-slate-600 mt-1">
                        Masukkan nama pengguna / email dan kata sandi Anda.
                    </p>
                </div>

                <!-- Session Status Alert -->
                <x-auth-session-status class="mb-4" :status="session('status')" />

                <!-- General Error Alert -->
                @if ($errors->any())
                    <div class="mb-4 p-3.5 rounded-lg bg-red-50 border border-red-200 text-red-800 text-xs flex items-start space-x-2.5">
                        <svg class="w-4 h-4 text-red-600 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                        <div>
                            <span class="font-bold">Gagal masuk:</span>
                            <ul class="list-disc list-inside mt-0.5 space-y-0.5">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}" class="space-y-4">
                    @csrf

                    <!-- Username or Email -->
                    <div>
                        <label for="username" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            Nama Pengguna atau Email
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                            </div>
                            <input id="username"
                                   type="text"
                                   name="username"
                                   value="{{ old('username') }}"
                                   required
                                   autofocus
                                   autocomplete="username"
                                   placeholder="Contoh: admin / budi@email.com"
                                   class="w-full pl-10 pr-3.5 py-2.5 text-sm bg-white border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-600 focus:border-blue-600 transition placeholder:text-slate-400 text-slate-900 shadow-sm" />
                        </div>
                    </div>

                    <!-- Password -->
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label for="password" class="block text-xs font-bold uppercase tracking-wider text-slate-700">
                                Kata Sandi
                            </label>
                            @if (Route::has('password.request'))
                                <a href="{{ route('password.request') }}" class="text-xs text-blue-600 hover:text-blue-800 font-semibold underline underline-offset-2">
                                    Lupa kata sandi?
                                </a>
                            @endif
                        </div>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                </svg>
                            </div>
                            <input id="password"
                                   :type="showPassword ? 'text' : 'password'"
                                   name="password"
                                   required
                                   autocomplete="current-password"
                                   placeholder="Masukkan kata sandi"
                                   class="w-full pl-10 pr-10 py-2.5 text-sm bg-white border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-600 focus:border-blue-600 transition placeholder:text-slate-400 text-slate-900 shadow-sm" />
                            <button type="button"
                                    @click="showPassword = !showPassword"
                                    class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600">
                                <svg x-show="!showPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                                <svg x-show="showPassword" style="display: none;" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Remember Me -->
                    <div class="flex items-center justify-between pt-1">
                        <label for="remember_me" class="inline-flex items-center cursor-pointer">
                            <input id="remember_me"
                                   type="checkbox"
                                   class="w-4 h-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500 transition"
                                   name="remember">
                            <span class="ms-2 text-xs font-medium text-slate-600 select-none">Ingat saya di perangkat ini</span>
                        </label>
                    </div>

                    <!-- Submit Button -->
                    <div class="pt-2">
                        <button type="submit"
                                class="w-full inline-flex items-center justify-center px-5 py-3 bg-blue-700 hover:bg-blue-800 text-white rounded-lg text-sm font-bold shadow-md shadow-blue-700/20 hover:shadow-lg transition active:scale-[0.99]">
                            <span>Masuk ke Portal Akademik</span>
                            <svg class="w-4 h-4 ms-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                            </svg>
                        </button>
                    </div>
                </form>

                <!-- Student Registration Link Card -->
                <div class="mt-6 p-4 rounded-xl bg-slate-50 border border-slate-200 text-center">
                    <p class="text-xs text-slate-600 mb-2 font-medium">
                        Calon siswa baru atau belum memiliki akun?
                    </p>
                    <a href="{{ route('register') }}"
                       class="inline-flex items-center justify-center w-full px-4 py-2 bg-white hover:bg-slate-100 text-slate-800 rounded-lg text-xs font-bold border border-slate-300 shadow-sm transition">
                        <svg class="w-4 h-4 me-1.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                        </svg>
                        Pendaftaran Akun Siswa Baru
                    </a>
                </div>
            </div>

            <!-- Bottom Footer -->
            <div class="mt-8 pt-4 border-t border-slate-100 text-center text-xs text-slate-500">
                &copy; {{ date('Y') }} SMP Negeri 2 Subang • Hak Cipta Dilindungi
            </div>
        </div>
    </div>

    <!-- Quick Fill Demo Helper Script -->
    <script>
        function fillDemo(username, password) {
            const u = document.getElementById('username');
            const p = document.getElementById('password');
            if (u && p) {
                u.value = username;
                p.value = password;
                u.classList.add('ring-2', 'ring-amber-400');
                p.classList.add('ring-2', 'ring-amber-400');
                setTimeout(() => {
                    u.classList.remove('ring-2', 'ring-amber-400');
                    p.classList.remove('ring-2', 'ring-amber-400');
                }, 800);
            }
        }
    </script>
</x-guest-layout>
