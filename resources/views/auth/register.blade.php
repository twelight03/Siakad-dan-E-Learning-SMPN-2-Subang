<x-guest-layout>
    <div class="min-h-screen flex flex-col lg:flex-row bg-slate-50">
        
        <!-- Left Column: School Visual & Identity -->
        <div class="lg:w-5/12 relative flex flex-col justify-between p-6 sm:p-10 lg:p-12 text-white bg-slate-900 overflow-hidden"
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

            <!-- Middle Content -->
            <div class="relative z-10 my-8 lg:my-auto max-w-lg">
                <div class="inline-flex items-center space-x-2 px-3 py-1 rounded-full bg-blue-500/20 border border-blue-400/30 text-blue-200 text-xs font-semibold mb-4">
                    <span>Portal Pendaftaran Siswa</span>
                </div>
                <h2 class="text-2xl sm:text-3xl font-extrabold text-white leading-tight tracking-tight mb-3">
                    Registrasi Mandiri Siswa Baru
                </h2>
                <p class="text-sm text-slate-200 leading-relaxed font-normal mb-6">
                    Daftarkan akun siswa Anda untuk mengakses jadwal KBM, mengunduh bahan ajar, mengumpulkan tugas secara daring, dan memantau nilai rapor secara transparan.
                </p>

                <!-- Information List -->
                <div class="space-y-3 p-4 rounded-xl bg-slate-900/70 border border-white/15 text-xs">
                    <div class="flex items-center space-x-2.5 text-slate-200">
                        <span class="text-emerald-400 font-bold">✓</span>
                        <span>Nomor Induk Siswa (NIS) unik & terdaftar resmi</span>
                    </div>
                    <div class="flex items-center space-x-2.5 text-slate-200">
                        <span class="text-emerald-400 font-bold">✓</span>
                        <span>Kata sandi diamankan algoritma hashing Bcrypt</span>
                    </div>
                    <div class="flex items-center space-x-2.5 text-slate-200">
                        <span class="text-emerald-400 font-bold">✓</span>
                        <span>Langsung terhubung dengan Portal Akademik Siswa</span>
                    </div>
                </div>
            </div>

            <!-- Bottom Footer -->
            <div class="relative z-10 pt-5 border-t border-white/15 flex items-center justify-between text-xs text-slate-300">
                <span>🔒 Sistem Terenkripsi SSL 256-Bit</span>
                <span class="text-[11px] text-slate-400">SMPN 2 Subang</span>
            </div>
        </div>

        <!-- Right Column: Registration Form -->
        <div class="lg:w-7/12 flex flex-col justify-between p-6 sm:p-10 lg:p-12 bg-white">
            
            <!-- Top Navigation -->
            <div class="flex items-center justify-between mb-6">
                <a href="{{ route('login') }}" class="inline-flex items-center text-xs font-bold text-slate-600 hover:text-blue-700 transition">
                    <svg class="w-4 h-4 me-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Sudah Punya Akun? Masuk di Sini
                </a>
                <span class="text-[11px] font-semibold px-2.5 py-1 rounded bg-slate-100 text-slate-600 border border-slate-200">
                    Form Pendaftaran Siswa
                </span>
            </div>

            <!-- Form Content Box -->
            <div class="w-full max-w-lg mx-auto my-auto" x-data="{ showPassword: false }">
                
                <div class="mb-5">
                    <h2 class="text-2xl font-black text-slate-900 tracking-tight">Pendaftaran Akun Siswa</h2>
                    <p class="text-xs sm:text-sm text-slate-600 mt-1">
                        Isi data diri dengan benar dan lengkap sesuai identitas sekolah.
                    </p>
                </div>

                <!-- Error Alert -->
                @if ($errors->any())
                    <div class="mb-4 p-3.5 rounded-lg bg-red-50 border border-red-200 text-red-800 text-xs flex items-start space-x-2.5">
                        <svg class="w-4 h-4 text-red-600 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                        <div>
                            <span class="font-bold">Periksa kesalahan input:</span>
                            <ul class="list-disc list-inside mt-0.5 space-y-0.5">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endif

                <form method="POST" action="{{ route('register') }}" class="space-y-3.5">
                    @csrf

                    <!-- Name -->
                    <div>
                        <label for="name" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                            Nama Lengkap Siswa <span class="text-red-500">*</span>
                        </label>
                        <input id="name"
                               type="text"
                               name="name"
                               value="{{ old('name') }}"
                               required
                               autofocus
                               autocomplete="name"
                               placeholder="Contoh: Ahmad Fauzan Pratama"
                               class="w-full px-3.5 py-2 text-sm bg-white border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-600 focus:border-blue-600 transition placeholder:text-slate-400 text-slate-900 shadow-sm" />
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <!-- Username -->
                        <div>
                            <label for="username" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                                Username Akun <span class="text-red-500">*</span>
                            </label>
                            <input id="username"
                                   type="text"
                                   name="username"
                                   value="{{ old('username') }}"
                                   required
                                   autocomplete="username"
                                   placeholder="ahmad123"
                                   class="w-full px-3.5 py-2 text-sm bg-white border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-600 focus:border-blue-600 transition placeholder:text-slate-400 text-slate-900 shadow-sm" />
                        </div>

                        <!-- NIS -->
                        <div>
                            <label for="nis" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                                Nomor Induk Siswa (NIS) <span class="text-red-500">*</span>
                            </label>
                            <input id="nis"
                                   type="text"
                                   name="nis"
                                   value="{{ old('nis') }}"
                                   required
                                   placeholder="Contoh: 202607009"
                                   class="w-full px-3.5 py-2 text-sm bg-white border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-600 focus:border-blue-600 transition placeholder:text-slate-400 text-slate-900 shadow-sm" />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <!-- Gender -->
                        <div>
                            <label for="gender" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                                Jenis Kelamin <span class="text-red-500">*</span>
                            </label>
                            <select id="gender"
                                    name="gender"
                                    required
                                    class="w-full px-3.5 py-2 text-sm bg-white border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-600 focus:border-blue-600 transition text-slate-900 shadow-sm">
                                <option value="" disabled {{ old('gender') ? '' : 'selected' }}>Pilih jenis kelamin</option>
                                <option value="male" {{ old('gender') === 'male' ? 'selected' : '' }}>Laki-laki</option>
                                <option value="female" {{ old('gender') === 'female' ? 'selected' : '' }}>Perempuan</option>
                            </select>
                        </div>

                        <!-- Email -->
                        <div>
                            <label for="email" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                                Alamat Email <span class="text-red-500">*</span>
                            </label>
                            <input id="email"
                                   type="email"
                                   name="email"
                                   value="{{ old('email') }}"
                                   required
                                   autocomplete="email"
                                   placeholder="nama@email.com"
                                   class="w-full px-3.5 py-2 text-sm bg-white border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-600 focus:border-blue-600 transition placeholder:text-slate-400 text-slate-900 shadow-sm" />
                        </div>
                    </div>

                    <!-- Password and Confirmation -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label for="password" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                                Kata Sandi (Min. 8) <span class="text-red-500">*</span>
                            </label>
                            <input id="password"
                                   :type="showPassword ? 'text' : 'password'"
                                   name="password"
                                   required
                                   autocomplete="new-password"
                                   placeholder="Minimal 8 karakter"
                                   class="w-full px-3.5 py-2 text-sm bg-white border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-600 focus:border-blue-600 transition placeholder:text-slate-400 text-slate-900 shadow-sm" />
                        </div>

                        <div>
                            <label for="password_confirmation" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                                Ulangi Kata Sandi <span class="text-red-500">*</span>
                            </label>
                            <input id="password_confirmation"
                                   :type="showPassword ? 'text' : 'password'"
                                   name="password_confirmation"
                                   required
                                   autocomplete="new-password"
                                   placeholder="Ulangi kata sandi"
                                   class="w-full px-3.5 py-2 text-sm bg-white border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-600 focus:border-blue-600 transition placeholder:text-slate-400 text-slate-900 shadow-sm" />
                        </div>
                    </div>

                    <!-- Show Password Checkbox -->
                    <div class="flex items-center pt-1">
                        <label class="inline-flex items-center cursor-pointer">
                            <input type="checkbox"
                                   @click="showPassword = !showPassword"
                                   class="w-4 h-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500 transition">
                            <span class="ms-2 text-xs text-slate-600">Tampilkan karakter kata sandi</span>
                        </label>
                    </div>

                    <!-- Submit Button -->
                    <div class="pt-3">
                        <button type="submit"
                                class="w-full inline-flex items-center justify-center px-5 py-3 bg-blue-700 hover:bg-blue-800 text-white rounded-lg text-sm font-bold shadow-md shadow-blue-700/20 hover:shadow-lg transition active:scale-[0.99]">
                            <span>Daftarkan Akun Siswa</span>
                            <svg class="w-4 h-4 ms-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                            </svg>
                        </button>
                    </div>
                </form>

                <div class="mt-5 text-center text-xs text-slate-600">
                    Sudah memiliki akun terdaftar?
                    <a href="{{ route('login') }}" class="font-bold text-blue-700 hover:text-blue-900 underline ml-1">
                        Masuk ke Portal
                    </a>
                </div>
            </div>

            <!-- Bottom Footer -->
            <div class="mt-8 pt-4 border-t border-slate-100 text-center text-xs text-slate-500">
                &copy; {{ date('Y') }} SMP Negeri 2 Subang • Hak Cipta Dilindungi
            </div>
        </div>
    </div>
</x-guest-layout>
