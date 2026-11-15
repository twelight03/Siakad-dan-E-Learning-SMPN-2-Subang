<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2">
            <div>
                <h2 class="font-extrabold text-xl text-slate-900 leading-tight">
                    {{ __('Generator Jadwal Otomatis (Anti-Bentrok)') }}
                </h2>
                <p class="text-xs text-slate-500 mt-0.5">Sistem cerdas menyusun jadwal KBM otomatis dengan jam berbeda per tingkatan dan mematuhi batas beban mengajar guru.</p>
            </div>
            <a href="{{ route('admin.schedules.index') }}" class="text-xs text-slate-600 hover:text-blue-700 font-bold flex items-center transition">
                ← Kembali ke Jadwal Pelajaran
            </a>
        </div>
    </x-slot>

    <div class="p-4 sm:p-6 space-y-6">

        <!-- Error & Alert Notification -->
        @if(session('error'))
            <div class="p-4 rounded-xl bg-red-50 border border-red-200 text-red-800 text-sm flex items-center space-x-2">
                <svg class="w-5 h-5 text-red-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                </svg>
                <span class="font-semibold">{{ session('error') }}</span>
            </div>
        @endif

        <form action="{{ route('admin.schedules.auto-generate.run') }}" method="POST" class="space-y-6" onsubmit="return confirm('Apakah Anda yakin ingin menyusun jadwal pelajaran otomatis untuk kelas-kelas terpilih?');">
            @csrf

            <!-- Form Card: Generator Settings -->
            <div class="bg-white p-5 sm:p-6 rounded-xl border border-slate-200 shadow-xs space-y-5">
                <div class="flex flex-wrap items-center justify-between pb-3 border-b border-slate-100 gap-2">
                    <h3 class="font-extrabold text-sm text-slate-900 flex items-center">
                        <span class="w-2.5 h-2.5 rounded-full bg-blue-600 me-2"></span>
                        Konfigurasi & Parameter Jadwal Otomatis
                    </h3>
                    <span class="text-xs font-semibold px-2.5 py-1 rounded bg-blue-50 text-blue-700 border border-blue-200">
                        ⚡ Jam Pelajaran Berbeda per Tingkatan (7, 8, 9)
                    </span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Target Tahun Ajaran & Pertemuan -->
                    <div class="space-y-4">
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                                Target Tahun Ajaran <span class="text-red-500">*</span>
                            </label>
                            <select name="academic_year_id" required class="w-full text-sm rounded-lg border-slate-300 focus:border-blue-500 focus:ring-blue-500 cursor-pointer">
                                @foreach($academicYears as $y)
                                    <option value="{{ $y->id }}" {{ $activeYear?->id == $y->id ? 'selected' : '' }}>
                                        {{ $y->name }} ({{ $y->semester == 'odd' ? 'Ganjil' : 'Genap' }}) {{ $y->is_active ? '• Aktif' : '' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                                Alokasi Pertemuan per Mapel / Minggu
                            </label>
                            <select name="sessions_per_subject" class="w-full text-sm rounded-lg border-slate-300 focus:border-blue-500 focus:ring-blue-500">
                                <option value="1">1 Pertemuan per Minggu (2 Jam Pelajaran / 80 Menit)</option>
                                <option value="2">2 Pertemuan per Minggu (4 Jam Pelajaran / 160 Menit)</option>
                            </select>
                            <p class="text-[11px] text-slate-400 mt-1">Sistem menyebarkan pertemuan ke hari yang berbeda secara merata.</p>
                        </div>

                        <!-- Rule Constraints -->
                        <div class="p-3.5 rounded-lg bg-slate-50 border border-slate-200 space-y-2.5">
                            <label class="flex items-start space-x-2 text-xs cursor-pointer">
                                <input type="checkbox" name="clear_existing" value="1" checked class="mt-0.5 rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                                <div>
                                    <span class="font-bold text-slate-800">Bersihkan Jadwal Lama Kelas Terpilih</span>
                                    <p class="text-[11px] text-slate-500">Menghapus jadwal lama pada kelas yang dipilih agar tidak terjadi tumpang tindih.</p>
                                </div>
                            </label>

                            <label class="flex items-start space-x-2 text-xs cursor-pointer">
                                <input type="checkbox" name="enforce_teacher_limits" value="1" checked class="mt-0.5 rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                                <div>
                                    <span class="font-bold text-slate-800">Patuhi Batas Beban Mengajar Mingguan Guru</span>
                                    <p class="text-[11px] text-slate-500">Guru tidak akan dijadwalkan melebihi batas jam pelajaran/minggu yang sudah diatur operator (misal: 7 jam, 12 jam, 24 jam).</p>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Pilih Kelas Target -->
                    <div>
                        <div class="flex justify-between items-center mb-1.5">
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">
                                Pilih Kelas yang Ingin Dibuatkan Jadwal
                            </label>
                            <button type="button" onclick="toggleAllClasses(true)" class="text-[11px] text-blue-600 font-bold hover:underline">
                                Pilih Semua
                            </button>
                        </div>

                        <div class="max-h-64 overflow-y-auto p-3 bg-slate-50 rounded-xl border border-slate-200 space-y-2">
                            @foreach($classes as $c)
                                <label class="flex items-center justify-between p-2 rounded-lg bg-white border border-slate-200 hover:border-blue-300 cursor-pointer text-xs transition">
                                    <div class="flex items-center space-x-2.5">
                                        <input type="checkbox" name="class_ids[]" value="{{ $c->id }}" checked class="class-cb rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                                        <span class="font-bold text-slate-800">Kelas {{ $c->name }}</span>
                                        <span class="px-1.5 py-0.5 rounded text-[10px] font-bold bg-slate-100 text-slate-600">
                                            Tingkat {{ $c->grade_level }}
                                        </span>
                                    </div>
                                    <span class="text-[11px] text-slate-500">
                                        {{ $c->class_subjects_count }} Mapel Terdaftar
                                    </span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Action Button -->
                <div class="flex items-center justify-between pt-4 border-t border-slate-100">
                    <span class="text-xs text-slate-500 flex items-center">
                        🔒 Dilindungi validasi anti-bentrok guru & ruangan secara otomatis.
                    </span>
                    <button type="submit" class="px-6 py-2.5 bg-blue-700 hover:bg-blue-800 text-white rounded-lg text-sm font-bold shadow-md shadow-blue-700/20 transition flex items-center space-x-2">
                        <span>🚀 Susun Jadwal Otomatis Sekarang</span>
                    </button>
                </div>
            </div>
        </form>

        <!-- Monitoring Beban Mengajar Guru (Real-time Overview) -->
        <div class="bg-white p-5 sm:p-6 rounded-xl border border-slate-200 shadow-xs space-y-3">
            <div class="flex justify-between items-center pb-2 border-b border-slate-100">
                <div>
                    <h3 class="font-extrabold text-sm text-slate-900">
                        Beban Jam Mengajar Guru (Diatur oleh Operator)
                    </h3>
                    <p class="text-xs text-slate-500">Batas maksimal beban mengajar dapat diubah kapan saja di menu Data Guru.</p>
                </div>
                <a href="{{ route('admin.teachers.index') }}" class="text-xs text-blue-600 hover:underline font-bold">
                    Atur Batas Guru ➔
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-600 border-collapse">
                    <thead class="bg-slate-50 text-[11px] uppercase font-bold text-slate-500 border-b border-slate-200">
                        <tr>
                            <th class="px-3 py-2.5">Nama Guru</th>
                            <th class="px-3 py-2.5">NIP</th>
                            <th class="px-3 py-2.5 text-center">Batas Beban Operator</th>
                            <th class="px-3 py-2.5 text-center">Jam Terjadwal Saat Ini</th>
                            <th class="px-3 py-2.5 text-center">Status Kapasitas</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($teacherStats as $ts)
                            @php
                                $percent = $ts['max_hours'] > 0 ? round(($ts['current_hours'] / $ts['max_hours']) * 100) : 0;
                            @endphp
                            <tr class="hover:bg-slate-50">
                                <td class="px-3 py-2.5 font-bold text-slate-800">{{ $ts['name'] }}</td>
                                <td class="px-3 py-2.5 font-mono text-slate-500">{{ $ts['nip'] }}</td>
                                <td class="px-3 py-2.5 text-center">
                                    <span class="px-2 py-0.5 rounded font-bold bg-slate-100 text-slate-800">
                                        {{ $ts['max_hours'] }} Jam/Minggu
                                    </span>
                                </td>
                                <td class="px-3 py-2.5 text-center font-bold text-blue-700">
                                    {{ $ts['current_hours'] }} Jam
                                </td>
                                <td class="px-3 py-2.5 text-center">
                                    @if($ts['current_hours'] >= $ts['max_hours'])
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-red-100 text-red-800">
                                            Penuh ({{ $percent }}%)
                                        </span>
                                    @elseif($ts['current_hours'] > 0)
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800">
                                            Terisi Sebagian ({{ $percent }}%)
                                        </span>
                                    @else
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">
                                            Tersedia (0%)
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        function toggleAllClasses(state) {
            document.querySelectorAll('.class-cb').forEach(cb => cb.checked = state);
        }
    </script>
</x-app-layout>
