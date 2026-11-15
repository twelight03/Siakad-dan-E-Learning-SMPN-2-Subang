<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2">
            <div>
                <h2 class="font-extrabold text-xl text-slate-900 leading-tight">
                    {{ __('⚙️ Pengaturan Waktu KBM & Jam Istirahat Sekolah') }}
                </h2>
                <p class="text-xs text-slate-500 mt-0.5">Konfigurasi durasi 1 JP, jam masuk/pulang KBM, dan sesi istirahat sekolah yang berlaku untuk seluruh tingkatan kelas.</p>
            </div>
            <a href="{{ route('admin.schedules.index') }}" class="text-xs text-slate-600 hover:text-blue-700 font-bold flex items-center transition">
                ← Kembali ke Jadwal Pelajaran
            </a>
        </div>
    </x-slot>

    <div class="p-4 sm:p-6 max-w-5xl mx-auto space-y-6">

        @if(session('success'))
            <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm flex items-center space-x-2">
                <svg class="w-5 h-5 text-emerald-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                <span class="font-semibold">{{ session('success') }}</span>
            </div>
        @endif

        @if($errors->any())
            <div class="p-4 rounded-xl bg-red-50 border border-red-200 text-red-800 text-sm space-y-1">
                <p class="font-bold flex items-center">
                    <svg class="w-5 h-5 text-red-600 me-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                    Terdapat kesalahan pada input pengaturan:
                </p>
                <ul class="list-disc list-inside text-xs ps-2">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.schedules.settings.update') }}" class="space-y-6">
            @csrf

            <!-- Section 1: Standard KBM Parameters -->
            <div class="bg-white rounded-xl border border-slate-200 shadow-xs p-5 sm:p-6 space-y-5">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <h3 class="font-extrabold text-sm text-slate-900 flex items-center">
                        <span class="w-2.5 h-2.5 rounded-full bg-blue-600 me-2"></span>
                        Parameter Utama Jam Belajar (KBM)
                    </h3>
                    <span class="text-xs font-semibold px-2.5 py-0.5 rounded bg-blue-50 text-blue-700 border border-blue-200">
                        SMP Negeri 2 Subang
                    </span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <!-- Lesson Duration -->
                    <div class="bg-slate-50 p-4 rounded-lg border border-slate-200">
                        <label for="lesson_duration" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                            Durasi 1 JP (Menit) <span class="text-red-500">*</span>
                        </label>
                        <div class="flex items-center space-x-2">
                            <input type="number" id="lesson_duration" name="lesson_duration" min="20" max="90" required
                                value="{{ old('lesson_duration', $config['lesson_duration'] ?? 40) }}"
                                class="w-full text-base font-bold text-slate-900 rounded-lg border-slate-300 focus:border-blue-500 focus:ring-blue-500">
                            <span class="text-xs text-slate-500 font-semibold whitespace-nowrap">menit</span>
                        </div>
                        <p class="text-[11px] text-slate-400 mt-1">Standar SMP Kemdikbud: 40 menit per JP.</p>
                    </div>

                    <!-- Start Time -->
                    <div class="bg-slate-50 p-4 rounded-lg border border-slate-200">
                        <label for="start_time" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                            Jam Masuk KBM <span class="text-red-500">*</span>
                        </label>
                        <input type="time" id="start_time" name="start_time" required
                            value="{{ old('start_time', $config['start_time'] ?? '07:00') }}"
                            class="w-full text-base font-bold text-slate-900 rounded-lg border-slate-300 focus:border-blue-500 focus:ring-blue-500">
                        <p class="text-[11px] text-slate-400 mt-1">Berlaku Senin s/d Jumat (07:00 WIB).</p>
                    </div>

                    <!-- End Time (Mon - Thu) -->
                    <div class="bg-slate-50 p-4 rounded-lg border border-slate-200">
                        <label for="end_time" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                            Jam Pulang (Senin - Kamis) <span class="text-red-500">*</span>
                        </label>
                        <input type="time" id="end_time" name="end_time" required
                            value="{{ old('end_time', $config['end_time'] ?? '14:00') }}"
                            class="w-full text-base font-bold text-slate-900 rounded-lg border-slate-300 focus:border-blue-500 focus:ring-blue-500">
                        <p class="text-[11px] text-slate-400 mt-1">Batas akhir KBM reguler (14:00 WIB).</p>
                    </div>

                    <!-- End Time Friday -->
                    <div class="bg-slate-50 p-4 rounded-lg border border-slate-200">
                        <label for="friday_end_time" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                            Jam Pulang (Khusus Jumat) <span class="text-red-500">*</span>
                        </label>
                        <input type="time" id="friday_end_time" name="friday_end_time" required
                            value="{{ old('friday_end_time', $config['friday_end_time'] ?? '11:15') }}"
                            class="w-full text-base font-bold text-slate-900 rounded-lg border-slate-300 focus:border-blue-500 focus:ring-blue-500">
                        <p class="text-[11px] text-slate-400 mt-1">Selesai sebelum Shalat Jumat (11:15 WIB).</p>
                    </div>
                </div>
            </div>

            <!-- Section 2: Monday - Thursday Recesses -->
            <div class="bg-white rounded-xl border border-slate-200 shadow-xs p-5 sm:p-6 space-y-4">
                <div class="flex flex-wrap items-center justify-between pb-3 border-b border-slate-100 gap-2">
                    <div>
                        <h3 class="font-extrabold text-sm text-slate-900 flex items-center">
                            <span class="w-2.5 h-2.5 rounded-full bg-amber-500 me-2"></span>
                            Sesi Jam Istirahat (Senin — Kamis)
                        </h3>
                        <p class="text-xs text-slate-500">Atur berapa kali istirahat dan rentang waktu masing-masing istirahat (misal: snack pagi & sholat dhuhur).</p>
                    </div>
                    <button type="button" onclick="addRecessRow('recess-table-body', 'recess_names[]', 'recess_starts[]', 'recess_ends[]')" class="px-3 py-1.5 text-xs font-bold text-blue-700 bg-blue-50 hover:bg-blue-100 border border-blue-200 rounded-lg transition flex items-center">
                        <span class="text-sm me-1">+</span> Tambah Jam Istirahat
                    </button>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm border border-slate-200 rounded-lg overflow-hidden">
                        <thead class="bg-slate-50 text-slate-600 text-xs uppercase font-bold border-b border-slate-200">
                            <tr>
                                <th class="p-3">Nama Sesi Istirahat</th>
                                <th class="p-3 w-40">Jam Mulai</th>
                                <th class="p-3 w-40">Jam Selesai</th>
                                <th class="p-3 w-20 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="recess-table-body" class="divide-y divide-slate-100">
                            @php
                                $recesses = old('recess_names') ? [] : ($config['recesses'] ?? []);
                                if (old('recess_names')) {
                                    foreach (old('recess_names') as $idx => $rName) {
                                        $recesses[] = [
                                            'name' => $rName,
                                            'start' => old('recess_starts')[$idx] ?? '',
                                            'end' => old('recess_ends')[$idx] ?? '',
                                        ];
                                    }
                                }
                            @endphp

                            @forelse($recesses as $r)
                                <tr class="recess-row hover:bg-slate-50/50">
                                    <td class="p-3">
                                        <input type="text" name="recess_names[]" value="{{ $r['name'] ?? '' }}" required
                                            placeholder="Contoh: Istirahat 1 / Ishoma Dhuhur"
                                            class="w-full text-xs font-semibold rounded-lg border-slate-300 focus:border-blue-500 focus:ring-blue-500">
                                    </td>
                                    <td class="p-3">
                                        <input type="time" name="recess_starts[]" value="{{ $r['start'] ?? '' }}" required
                                            class="w-full text-xs font-mono font-bold rounded-lg border-slate-300 focus:border-blue-500 focus:ring-blue-500">
                                    </td>
                                    <td class="p-3">
                                        <input type="time" name="recess_ends[]" value="{{ $r['end'] ?? '' }}" required
                                            class="w-full text-xs font-mono font-bold rounded-lg border-slate-300 focus:border-blue-500 focus:ring-blue-500">
                                    </td>
                                    <td class="p-3 text-center">
                                        <button type="button" onclick="removeRow(this)" class="p-1.5 text-red-600 hover:text-red-800 hover:bg-red-50 rounded-lg transition" title="Hapus Istirahat">
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                            </svg>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr class="recess-row hover:bg-slate-50/50">
                                    <td class="p-3">
                                        <input type="text" name="recess_names[]" value="Istirahat 1" required
                                            class="w-full text-xs font-semibold rounded-lg border-slate-300 focus:border-blue-500 focus:ring-blue-500">
                                    </td>
                                    <td class="p-3">
                                        <input type="time" name="recess_starts[]" value="09:40" required
                                            class="w-full text-xs font-mono font-bold rounded-lg border-slate-300 focus:border-blue-500 focus:ring-blue-500">
                                    </td>
                                    <td class="p-3">
                                        <input type="time" name="recess_ends[]" value="10:05" required
                                            class="w-full text-xs font-mono font-bold rounded-lg border-slate-300 focus:border-blue-500 focus:ring-blue-500">
                                    </td>
                                    <td class="p-3 text-center">
                                        <button type="button" onclick="removeRow(this)" class="p-1.5 text-red-600 hover:text-red-800 hover:bg-red-50 rounded-lg transition" title="Hapus Istirahat">
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                            </svg>
                                        </button>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Section 3: Friday Recesses -->
            <div class="bg-white rounded-xl border border-slate-200 shadow-xs p-5 sm:p-6 space-y-4">
                <div class="flex flex-wrap items-center justify-between pb-3 border-b border-slate-100 gap-2">
                    <div>
                        <h3 class="font-extrabold text-sm text-slate-900 flex items-center">
                            <span class="w-2.5 h-2.5 rounded-full bg-teal-500 me-2"></span>
                            Sesi Jam Istirahat (Khusus Hari Jumat)
                        </h3>
                        <p class="text-xs text-slate-500">Hari Jumat jam KBM lebih pendek (selesai 11:15 WIB), atur istirahat pagi khusus Jumat.</p>
                    </div>
                    <button type="button" onclick="addRecessRow('friday-recess-table-body', 'friday_recess_names[]', 'friday_recess_starts[]', 'friday_recess_ends[]')" class="px-3 py-1.5 text-xs font-bold text-teal-700 bg-teal-50 hover:bg-teal-100 border border-teal-200 rounded-lg transition flex items-center">
                        <span class="text-sm me-1">+</span> Tambah Istirahat Jumat
                    </button>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm border border-slate-200 rounded-lg overflow-hidden">
                        <thead class="bg-slate-50 text-slate-600 text-xs uppercase font-bold border-b border-slate-200">
                            <tr>
                                <th class="p-3">Nama Sesi Istirahat Jumat</th>
                                <th class="p-3 w-40">Jam Mulai</th>
                                <th class="p-3 w-40">Jam Selesai</th>
                                <th class="p-3 w-20 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="friday-recess-table-body" class="divide-y divide-slate-100">
                            @php
                                $friRecesses = old('friday_recess_names') ? [] : ($config['friday_recesses'] ?? []);
                                if (old('friday_recess_names')) {
                                    foreach (old('friday_recess_names') as $idx => $rName) {
                                        $friRecesses[] = [
                                            'name' => $rName,
                                            'start' => old('friday_recess_starts')[$idx] ?? '',
                                            'end' => old('friday_recess_ends')[$idx] ?? '',
                                        ];
                                    }
                                }
                            @endphp

                            @forelse($friRecesses as $r)
                                <tr class="recess-row hover:bg-slate-50/50">
                                    <td class="p-3">
                                        <input type="text" name="friday_recess_names[]" value="{{ $r['name'] ?? '' }}" required
                                            placeholder="Contoh: Istirahat Pagi Jumat"
                                            class="w-full text-xs font-semibold rounded-lg border-slate-300 focus:border-teal-500 focus:ring-teal-500">
                                    </td>
                                    <td class="p-3">
                                        <input type="time" name="friday_recess_starts[]" value="{{ $r['start'] ?? '' }}" required
                                            class="w-full text-xs font-mono font-bold rounded-lg border-slate-300 focus:border-teal-500 focus:ring-teal-500">
                                    </td>
                                    <td class="p-3">
                                        <input type="time" name="friday_recess_ends[]" value="{{ $r['end'] ?? '' }}" required
                                            class="w-full text-xs font-mono font-bold rounded-lg border-slate-300 focus:border-teal-500 focus:ring-teal-500">
                                    </td>
                                    <td class="p-3 text-center">
                                        <button type="button" onclick="removeRow(this)" class="p-1.5 text-red-600 hover:text-red-800 hover:bg-red-50 rounded-lg transition" title="Hapus Istirahat">
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                            </svg>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr class="recess-row hover:bg-slate-50/50">
                                    <td class="p-3">
                                        <input type="text" name="friday_recess_names[]" value="Istirahat Pagi Jumat" required
                                            class="w-full text-xs font-semibold rounded-lg border-slate-300 focus:border-teal-500 focus:ring-teal-500">
                                    </td>
                                    <td class="p-3">
                                        <input type="time" name="friday_recess_starts[]" value="09:00" required
                                            class="w-full text-xs font-mono font-bold rounded-lg border-slate-300 focus:border-teal-500 focus:ring-teal-500">
                                    </td>
                                    <td class="p-3">
                                        <input type="time" name="friday_recess_ends[]" value="09:20" required
                                            class="w-full text-xs font-mono font-bold rounded-lg border-slate-300 focus:border-teal-500 focus:ring-teal-500">
                                    </td>
                                    <td class="p-3 text-center">
                                        <button type="button" onclick="removeRow(this)" class="p-1.5 text-red-600 hover:text-red-800 hover:bg-red-50 rounded-lg transition" title="Hapus Istirahat">
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                            </svg>
                                        </button>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center justify-between pt-2">
                <a href="{{ route('admin.schedules.index') }}" class="px-5 py-2.5 rounded-lg border border-slate-300 text-xs font-bold text-slate-700 hover:bg-slate-100 transition">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-xs font-extrabold shadow-sm transition flex items-center">
                    <svg class="w-4 h-4 me-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Simpan Pengaturan Jam KBM
                </button>
            </div>
        </form>

    </div>

    <script>
        function removeRow(btn) {
            const row = btn.closest('tr');
            const tbody = row.closest('tbody');
            if (tbody.querySelectorAll('tr').length <= 1) {
                alert('Minimal harus ada 1 entri istirahat, atau kosongkan nilainya.');
                return;
            }
            row.remove();
        }

        function addRecessRow(tbodyId, nameField, startField, endField) {
            const tbody = document.getElementById(tbodyId);
            const tr = document.createElement('tr');
            tr.className = 'recess-row hover:bg-slate-50/50';
            tr.innerHTML = `
                <td class="p-3">
                    <input type="text" name="${nameField}" required placeholder="Contoh: Istirahat Tambahan"
                        class="w-full text-xs font-semibold rounded-lg border-slate-300 focus:border-blue-500 focus:ring-blue-500">
                </td>
                <td class="p-3">
                    <input type="time" name="${startField}" required value="12:00"
                        class="w-full text-xs font-mono font-bold rounded-lg border-slate-300 focus:border-blue-500 focus:ring-blue-500">
                </td>
                <td class="p-3">
                    <input type="time" name="${endField}" required value="12:30"
                        class="w-full text-xs font-mono font-bold rounded-lg border-slate-300 focus:border-blue-500 focus:ring-blue-500">
                </td>
                <td class="p-3 text-center">
                    <button type="button" onclick="removeRow(this)" class="p-1.5 text-red-600 hover:text-red-800 hover:bg-red-50 rounded-lg transition" title="Hapus Istirahat">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                    </button>
                </td>
            `;
            tbody.appendChild(tr);
        }
    </script>
</x-app-layout>
