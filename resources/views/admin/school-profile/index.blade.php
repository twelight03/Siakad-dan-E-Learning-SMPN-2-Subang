<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2">
            <div>
                <h2 class="font-black text-xl text-slate-900 leading-tight flex items-center">
                    <span class="me-2 text-2xl">🏛️</span> {{ __('Pengaturan Data Pokok & Profil Sekolah') }}
                </h2>
                <p class="text-xs text-slate-500 mt-0.5">
                    Konfigurasi angka resmi sensus Dapodik (siswa, guru, rombel, mapel) dan identitas sekolah yang ditampilkan di landing page.
                </p>
            </div>
            <a href="{{ url('/') }}" target="_blank" class="inline-flex items-center px-3.5 py-2 bg-white hover:bg-slate-50 text-blue-700 border border-slate-300 rounded-lg text-xs font-bold shadow-xs transition">
                <svg class="w-4 h-4 me-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                </svg>
                Lihat Tampilan Landing Page
            </a>
        </div>
    </x-slot>

    <div class="p-4 sm:p-6 max-w-5xl mx-auto space-y-6">

        @if(session('success'))
            <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm flex items-center space-x-2">
                <svg class="w-5 h-5 text-emerald-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                <span class="font-bold">{{ session('success') }}</span>
            </div>
        @endif

        @if($errors->any())
            <div class="p-4 rounded-xl bg-red-50 border border-red-200 text-red-800 text-sm space-y-1">
                <p class="font-bold">Terdapat kesalahan pada input formulir:</p>
                <ul class="list-disc list-inside text-xs ps-2">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.school-profile.update') }}" class="space-y-6">
            @csrf

            <!-- CARD 1: SUMBER DATA POKOK (MODE SELECTION) -->
            <div class="bg-white rounded-xl border border-slate-200 shadow-xs p-5 sm:p-6 space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <h3 class="font-extrabold text-sm text-slate-900 flex items-center">
                        <span class="w-2.5 h-2.5 rounded-full bg-blue-600 me-2"></span>
                        Sumber Data Pokok Pendidikan (Landing Page)
                    </h3>
                    <span class="text-xs font-semibold px-2.5 py-0.5 rounded bg-blue-50 text-blue-700 border border-blue-200">
                        SMP Negeri 2 Subang
                    </span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Option 1: Manual Mode (Recommended) -->
                    <label class="p-4 rounded-xl border-2 cursor-pointer transition flex items-start space-x-3 {{ old('dapodik_mode', $dapodik['mode']) === 'manual' ? 'border-blue-600 bg-blue-50/40 ring-2 ring-blue-100' : 'border-slate-200 hover:border-slate-300 bg-white' }}">
                        <input type="radio" name="dapodik_mode" value="manual" class="mt-1 text-blue-600 focus:ring-blue-500" {{ old('dapodik_mode', $dapodik['mode']) === 'manual' ? 'checked' : '' }}>
                        <div>
                            <span class="font-extrabold text-sm text-slate-900 block">Mode Manual Sensus Dapodik (Disarankan)</span>
                            <p class="text-xs text-slate-600 mt-1 leading-relaxed">
                                Angka statistik ditentukan langsung oleh operator sesuai data resmi sensus Dapodik sekolah (misal: 648 Siswa, 38 Guru, 18 Rombel). Tidak terpengaruh jumlah akun uji coba.
                            </p>
                        </div>
                    </label>

                    <!-- Option 2: Automatic DB Count -->
                    <label class="p-4 rounded-xl border-2 cursor-pointer transition flex items-start space-x-3 {{ old('dapodik_mode', $dapodik['mode']) === 'auto' ? 'border-blue-600 bg-blue-50/40 ring-2 ring-blue-100' : 'border-slate-200 hover:border-slate-300 bg-white' }}">
                        <input type="radio" name="dapodik_mode" value="auto" class="mt-1 text-blue-600 focus:ring-blue-500" {{ old('dapodik_mode', $dapodik['mode']) === 'auto' ? 'checked' : '' }}>
                        <div>
                            <span class="font-extrabold text-sm text-slate-900 block">Mode Otomatis (Database Riil)</span>
                            <p class="text-xs text-slate-600 mt-1 leading-relaxed">
                                Angka dihitung secara real-time dari total baris akun siswa, guru, kelas, dan mata pelajaran yang tersimpan aktif di dalam database.
                            </p>
                        </div>
                    </label>
                </div>

                <!-- Live Comparison Table -->
                <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 mt-2">
                    <span class="text-xs font-bold text-slate-700 block mb-2 uppercase tracking-wider">Perbandingan Nilai Data Pokok:</span>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-center text-xs">
                        <div class="bg-white p-2.5 rounded-lg border border-slate-200">
                            <span class="text-slate-500 block font-medium">Peserta Didik</span>
                            <span class="font-black text-sm text-blue-700 font-mono">{{ $dapodik['manual_students'] }}</span>
                            <span class="text-[10px] text-slate-400 block">(DB: {{ $dapodik['db_students'] }})</span>
                        </div>
                        <div class="bg-white p-2.5 rounded-lg border border-slate-200">
                            <span class="text-slate-500 block font-medium">Tenaga Pendidik</span>
                            <span class="font-black text-sm text-emerald-700 font-mono">{{ $dapodik['manual_teachers'] }}</span>
                            <span class="text-[10px] text-slate-400 block">(DB: {{ $dapodik['db_teachers'] }})</span>
                        </div>
                        <div class="bg-white p-2.5 rounded-lg border border-slate-200">
                            <span class="text-slate-500 block font-medium">Rombel Kelas</span>
                            <span class="font-black text-sm text-indigo-700 font-mono">{{ $dapodik['manual_classes'] }}</span>
                            <span class="text-[10px] text-slate-400 block">(DB: {{ $dapodik['db_classes'] }})</span>
                        </div>
                        <div class="bg-white p-2.5 rounded-lg border border-slate-200">
                            <span class="text-slate-500 block font-medium">Mata Pelajaran</span>
                            <span class="font-black text-sm text-amber-700 font-mono">{{ $dapodik['manual_subjects'] }}</span>
                            <span class="text-[10px] text-slate-400 block">(DB: {{ $dapodik['db_subjects'] }})</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- CARD 2: 4 STATISTIK DAPODIK SEKOLAH -->
            <div class="bg-white rounded-xl border border-slate-200 shadow-xs p-5 sm:p-6 space-y-4">
                <div class="pb-3 border-b border-slate-100">
                    <h3 class="font-extrabold text-sm text-slate-900 flex items-center">
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-600 me-2"></span>
                        Pengaturan 4 Statistik Data Pokok (Kotak Dapodik)
                    </h3>
                    <p class="text-xs text-slate-500 mt-0.5">Atur angka dan teks label keterangan yang tampil pada 4 kartu statistik di landing page.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <!-- Stat 1: Siswa -->
                    <div class="p-4 rounded-xl border border-slate-200 bg-slate-50/50 space-y-3">
                        <div class="flex items-center space-x-2">
                            <span class="text-lg">🎓</span>
                            <span class="font-bold text-sm text-slate-900">Statistik Peserta Didik (Siswa)</span>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Jumlah Siswa</label>
                                <input type="number" name="dapodik_students" min="0" value="{{ old('dapodik_students', $dapodik['manual_students']) }}" class="w-full text-base font-black font-mono text-blue-700 rounded-lg border-slate-300 focus:border-blue-500 focus:ring-blue-500" required>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Label Keterangan</label>
                                <input type="text" name="dapodik_students_label" value="{{ old('dapodik_students_label', $dapodik['students_label']) }}" class="w-full text-xs font-semibold rounded-lg border-slate-300 focus:border-blue-500 focus:ring-blue-500" required>
                            </div>
                        </div>
                    </div>

                    <!-- Stat 2: Guru -->
                    <div class="p-4 rounded-xl border border-slate-200 bg-slate-50/50 space-y-3">
                        <div class="flex items-center space-x-2">
                            <span class="text-lg">👨‍🏫</span>
                            <span class="font-bold text-sm text-slate-900">Statistik Tenaga Pendidik (Guru)</span>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Jumlah Guru</label>
                                <input type="number" name="dapodik_teachers" min="0" value="{{ old('dapodik_teachers', $dapodik['manual_teachers']) }}" class="w-full text-base font-black font-mono text-emerald-700 rounded-lg border-slate-300 focus:border-blue-500 focus:ring-blue-500" required>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Label Keterangan</label>
                                <input type="text" name="dapodik_teachers_label" value="{{ old('dapodik_teachers_label', $dapodik['teachers_label']) }}" class="w-full text-xs font-semibold rounded-lg border-slate-300 focus:border-blue-500 focus:ring-blue-500" required>
                            </div>
                        </div>
                    </div>

                    <!-- Stat 3: Rombel Kelas -->
                    <div class="p-4 rounded-xl border border-slate-200 bg-slate-50/50 space-y-3">
                        <div class="flex items-center space-x-2">
                            <span class="text-lg">🏛️</span>
                            <span class="font-bold text-sm text-slate-900">Statistik Rombongan Belajar (Kelas)</span>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Jumlah Rombel</label>
                                <input type="number" name="dapodik_classes" min="0" value="{{ old('dapodik_classes', $dapodik['manual_classes']) }}" class="w-full text-base font-black font-mono text-indigo-700 rounded-lg border-slate-300 focus:border-blue-500 focus:ring-blue-500" required>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Label Keterangan</label>
                                <input type="text" name="dapodik_classes_label" value="{{ old('dapodik_classes_label', $dapodik['classes_label']) }}" class="w-full text-xs font-semibold rounded-lg border-slate-300 focus:border-blue-500 focus:ring-blue-500" required>
                            </div>
                        </div>
                    </div>

                    <!-- Stat 4: Mapel -->
                    <div class="p-4 rounded-xl border border-slate-200 bg-slate-50/50 space-y-3">
                        <div class="flex items-center space-x-2">
                            <span class="text-lg">📖</span>
                            <span class="font-bold text-sm text-slate-900">Statistik Mata Pelajaran</span>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Jumlah Mapel</label>
                                <input type="number" name="dapodik_subjects" min="0" value="{{ old('dapodik_subjects', $dapodik['manual_subjects']) }}" class="w-full text-base font-black font-mono text-amber-700 rounded-lg border-slate-300 focus:border-blue-500 focus:ring-blue-500" required>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Label Keterangan</label>
                                <input type="text" name="dapodik_subjects_label" value="{{ old('dapodik_subjects_label', $dapodik['subjects_label']) }}" class="w-full text-xs font-semibold rounded-lg border-slate-300 focus:border-blue-500 focus:ring-blue-500" required>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- CARD 3: IDENTITAS SATUAN PENDIDIKAN & KONTAK -->
            <div class="bg-white rounded-xl border border-slate-200 shadow-xs p-5 sm:p-6 space-y-4">
                <div class="pb-3 border-b border-slate-100">
                    <h3 class="font-extrabold text-sm text-slate-900 flex items-center">
                        <span class="w-2.5 h-2.5 rounded-full bg-amber-500 me-2"></span>
                        Identitas Satuan Pendidikan & Kontak Resmi
                    </h3>
                    <p class="text-xs text-slate-500 mt-0.5">Informasi legalitas dan kontak yang tampil pada strip header serta footer sekolah.</p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Nomor Pokok Sekolah Nasional (NPSN)</label>
                        <input type="text" name="school_npsn" value="{{ old('school_npsn', $dapodik['npsn']) }}" class="w-full text-sm font-mono font-bold rounded-lg border-slate-300 focus:border-blue-500 focus:ring-blue-500" required>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Status Akreditasi</label>
                        <input type="text" name="school_accreditation" value="{{ old('school_accreditation', $dapodik['accreditation']) }}" class="w-full text-sm font-bold text-amber-700 rounded-lg border-slate-300 focus:border-blue-500 focus:ring-blue-500" required>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">No. Telepon / Helpdesk WA Tata Usaha</label>
                        <input type="text" name="school_phone" value="{{ old('school_phone', $dapodik['school_phone']) }}" class="w-full text-sm rounded-lg border-slate-300 focus:border-blue-500 focus:ring-blue-500" required>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Email Resmi Sekolah / TU</label>
                        <input type="email" name="school_email" value="{{ old('school_email', $dapodik['school_email']) }}" class="w-full text-sm rounded-lg border-slate-300 focus:border-blue-500 focus:ring-blue-500" required>
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Alamat Kampus Sekolah Lengkap</label>
                        <input type="text" name="school_address" value="{{ old('school_address', $dapodik['school_address']) }}" class="w-full text-sm rounded-lg border-slate-300 focus:border-blue-500 focus:ring-blue-500" required>
                    </div>
                </div>
            </div>

            <!-- CARD 4: PRAKATA / SAMBUTAN KEPALA SEKOLAH -->
            <div class="bg-white rounded-xl border border-slate-200 shadow-xs p-5 sm:p-6 space-y-4">
                <div class="pb-3 border-b border-slate-100">
                    <h3 class="font-extrabold text-sm text-slate-900 flex items-center">
                        <span class="w-2.5 h-2.5 rounded-full bg-indigo-600 me-2"></span>
                        Prakata / Sambutan Kepala Sekolah (Landing Page)
                    </h3>
                    <p class="text-xs text-slate-500 mt-0.5">Teks kutipan pidato pimpinan yang tampil pada kartu sambutan di halaman muka portal.</p>
                </div>

                <div class="space-y-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Nama / Jabatan Pimpinan</label>
                        <input type="text" name="principal_name" value="{{ old('principal_name', $dapodik['principal_name']) }}" class="w-full text-sm font-bold rounded-lg border-slate-300 focus:border-blue-500 focus:ring-blue-500" required>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Teks Sambutan Pimpinan</label>
                        <textarea name="principal_speech" rows="4" class="w-full text-xs leading-relaxed rounded-lg border-slate-300 focus:border-blue-500 focus:ring-blue-500" required>{{ old('principal_speech', $dapodik['principal_speech']) }}</textarea>
                    </div>
                </div>
            </div>

            <!-- ACTION BUTTONS -->
            <div class="flex items-center justify-between pt-2">
                <a href="{{ route('admin.dashboard') }}" class="px-5 py-2.5 rounded-lg border border-slate-300 text-xs font-bold text-slate-700 hover:bg-slate-100 transition">
                    Kembali ke Dashboard
                </a>
                <button type="submit" class="px-6 py-2.5 rounded-lg bg-blue-700 hover:bg-blue-800 text-white text-xs font-black shadow-sm transition flex items-center">
                    <svg class="w-4 h-4 me-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                    </svg>
                    Simpan Perubahan Data Pokok & Profil
                </button>
            </div>
        </form>

    </div>
</x-app-layout>
