<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2">
            <div>
                <h2 class="font-extrabold text-xl text-slate-900 leading-tight">
                    {{ __('Generator Acak Kelompok Belajar') }}
                </h2>
                <p class="text-xs text-slate-500 mt-0.5">Bagi siswa kelas secara otomatis, acak, dan seimbang gender untuk kerja kelompok atau diskusi kelas.</p>
            </div>
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-blue-100 text-blue-800 border border-blue-200">
                👨‍🏫 Modul Guru
            </span>
        </div>
    </x-slot>

    <div class="p-4 sm:p-6 space-y-6">
        
        <!-- Filter & Generator Form Card -->
        <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-xs">
            <form method="GET" action="{{ route('teacher.groups.index') }}" class="space-y-4">
                <input type="hidden" name="generate" value="1">

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <!-- Pilih Kelas -->
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            Pilih Kelas & Mata Pelajaran <span class="text-red-500">*</span>
                        </label>
                        <select name="class_subject_id" onchange="this.form.submit()" class="w-full text-sm rounded-lg border-slate-300 focus:border-blue-500 focus:ring-blue-500 cursor-pointer">
                            @forelse($classSubjects as $cs)
                                <option value="{{ $cs->id }}" {{ $selectedClassSubject?->id == $cs->id ? 'selected' : '' }}>
                                    Kelas {{ $cs->schoolClass->name }} • {{ $cs->subject->name }}
                                </option>
                            @empty
                                <option value="">Tidak ada kelas yang diampu</option>
                            @endforelse
                        </select>
                        <span class="text-[11px] text-slate-400 mt-1 block">
                            Total Siswa Aktif: <strong class="text-slate-700">{{ $students->count() }} orang</strong>
                        </span>
                    </div>

                    <!-- Mode Pembagian -->
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            Target Jumlah Kelompok
                        </label>
                        <div class="flex items-center space-x-2">
                            <input type="number"
                                   name="num_groups"
                                   value="{{ request('num_groups', 4) }}"
                                   min="2"
                                   max="{{ max(2, $students->count()) }}"
                                   class="w-full text-sm rounded-lg border-slate-300 focus:border-blue-500 focus:ring-blue-500">
                            <span class="text-xs text-slate-500 whitespace-nowrap">Kelompok</span>
                        </div>
                        <span class="text-[11px] text-slate-400 mt-1 block">
                            Rata-rata ~{{ $students->count() > 0 ? round($students->count() / max(1, (int)request('num_groups', 4)), 1) : 0 }} siswa/kelompok
                        </span>
                    </div>

                    <!-- Tema Penamaan -->
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            Format Nama Kelompok
                        </label>
                        <select name="naming_theme" class="w-full text-sm rounded-lg border-slate-300 focus:border-blue-500 focus:ring-blue-500">
                            <option value="numbers" {{ request('naming_theme') === 'numbers' ? 'selected' : '' }}>Angka (Kelompok 1, 2, ...)</option>
                            <option value="birds" {{ request('naming_theme') === 'birds' ? 'selected' : '' }}>Nama Satwa (Garuda, Rajawali, ...)</option>
                            <option value="colors" {{ request('naming_theme') === 'colors' ? 'selected' : '' }}>Warna (Merah, Biru, Hijau, ...)</option>
                        </select>
                    </div>
                </div>

                <!-- Gender Balance Option -->
                <div class="pt-2 border-t border-slate-100 flex flex-wrap items-center justify-between gap-3">
                    <label class="inline-flex items-center cursor-pointer">
                        <input type="checkbox"
                               name="balance_gender"
                               value="1"
                               {{ request()->has('generate') ? (request()->boolean('balance_gender') ? 'checked' : '') : 'checked' }}
                               class="w-4 h-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                        <span class="ms-2 text-xs font-bold text-slate-800">Seimbangkan Gender (Jumlah Laki-laki & Perempuan Merata di Tiap Kelompok)</span>
                    </label>

                    <button type="submit" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-bold shadow-sm transition flex items-center space-x-1.5">
                        <svg class="w-4 h-4 text-amber-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/>
                        </svg>
                        <span>🎲 Acak Kelompok Sekarang</span>
                    </button>
                </div>
            </form>
        </div>

        <!-- Group Results Section -->
        @if($groups)
            <div class="space-y-4" id="printArea">
                <!-- Action Bar & Print Header -->
                <div class="flex flex-wrap items-center justify-between gap-3 bg-white p-4 rounded-xl border border-slate-200 shadow-xs">
                    <div>
                        <h3 class="font-extrabold text-base text-slate-900">
                            Hasil Pembagian Kelompok • Kelas {{ $selectedClassSubject?->schoolClass->name }} ({{ $selectedClassSubject?->subject->name }})
                        </h3>
                        <p class="text-xs text-slate-500 mt-0.5">
                            Total {{ $students->count() }} siswa dibagi menjadi {{ count($groups) }} kelompok.
                        </p>
                    </div>

                    <div class="flex items-center space-x-2 no-print">
                        <!-- Re-shuffle Button -->
                        <form method="GET" action="{{ route('teacher.groups.index') }}" class="inline">
                            <input type="hidden" name="generate" value="1">
                            <input type="hidden" name="class_subject_id" value="{{ $selectedClassSubject?->id }}">
                            <input type="hidden" name="num_groups" value="{{ request('num_groups', 4) }}">
                            <input type="hidden" name="naming_theme" value="{{ request('naming_theme', 'numbers') }}">
                            <input type="hidden" name="balance_gender" value="{{ request('balance_gender', '1') }}">
                            <button type="submit" class="px-3.5 py-2 bg-slate-100 hover:bg-slate-200 text-slate-800 rounded-lg text-xs font-bold transition flex items-center space-x-1.5 border border-slate-200">
                                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                </svg>
                                <span>Acak Ulang</span>
                            </button>
                        </form>

                        <!-- Copy to Clipboard Button -->
                        <button type="button"
                                onclick="copyGroupsToClipboard()"
                                class="px-3.5 py-2 bg-emerald-50 hover:bg-emerald-100 text-emerald-800 rounded-lg text-xs font-bold transition flex items-center space-x-1.5 border border-emerald-200">
                            <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"/>
                            </svg>
                            <span>Salin Teks (WA)</span>
                        </button>

                        <!-- Print Button -->
                        <button type="button"
                                onclick="window.print()"
                                class="px-3.5 py-2 bg-blue-700 hover:bg-blue-800 text-white rounded-lg text-xs font-bold transition flex items-center space-x-1.5 shadow-xs">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                            </svg>
                            <span>Cetak / Simpan PDF</span>
                        </button>
                    </div>
                </div>

                <!-- Print Only School Header -->
                <div class="hidden print:block text-center pb-4 mb-4 border-b-2 border-black">
                    <h3 class="text-base font-bold uppercase">SMP NEGERI 2 SUBANG</h3>
                    <p class="text-xs">Daftar Kelompok Belajar • Kelas {{ $selectedClassSubject?->schoolClass->name }} • Mapel: {{ $selectedClassSubject?->subject->name }}</p>
                    <p class="text-[10px] text-gray-500">Guru Pengampu: {{ auth()->user()->name }} | Tanggal Cetak: {{ date('d/m/Y') }}</p>
                </div>

                <!-- Grid of Group Cards -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($groups as $grp)
                        <div class="bg-white rounded-xl border border-slate-200 shadow-xs overflow-hidden flex flex-col justify-between">
                            <div>
                                <!-- Card Header -->
                                <div class="px-4 py-3 bg-gradient-to-r from-slate-50 to-blue-50/50 border-b border-slate-200 flex justify-between items-center">
                                    <div class="flex items-center space-x-2">
                                        <div class="w-7 h-7 rounded-lg bg-blue-600 text-white font-black text-xs flex items-center justify-center">
                                            {{ substr($grp['name'], -1) }}
                                        </div>
                                        <span class="font-black text-sm text-slate-900">{{ $grp['name'] }}</span>
                                    </div>
                                    <span class="text-xs font-bold px-2 py-0.5 rounded-full bg-blue-100 text-blue-800">
                                        {{ count($grp['students']) }} Siswa
                                    </span>
                                </div>

                                <!-- Gender Summary -->
                                <div class="px-4 py-2 bg-slate-50/50 border-b border-slate-100 flex items-center space-x-3 text-[11px] text-slate-500">
                                    <span class="flex items-center">
                                        <span class="w-2 h-2 rounded-full bg-blue-500 me-1"></span>
                                        Laki-laki: <strong class="ms-1 text-slate-800">{{ $grp['male_count'] }}</strong>
                                    </span>
                                    <span>•</span>
                                    <span class="flex items-center">
                                        <span class="w-2 h-2 rounded-full bg-pink-500 me-1"></span>
                                        Perempuan: <strong class="ms-1 text-slate-800">{{ $grp['female_count'] }}</strong>
                                    </span>
                                </div>

                                <!-- Members List -->
                                <div class="p-3 divide-y divide-slate-100">
                                    @foreach($grp['students'] as $idx => $student)
                                        <div class="py-2 flex items-center justify-between text-xs">
                                            <div class="flex items-center space-x-2 min-w-0">
                                                <span class="text-[11px] font-bold text-slate-400 w-4">{{ $idx + 1 }}.</span>
                                                <div class="truncate">
                                                    <span class="font-bold text-slate-800 truncate block">{{ $student->user->name }}</span>
                                                    <span class="text-[10px] text-slate-400 font-mono">NIS: {{ $student->nis }}</span>
                                                </div>
                                            </div>
                                            <span class="px-1.5 py-0.5 rounded text-[10px] font-bold whitespace-nowrap {{ $student->gender === 'male' ? 'bg-blue-50 text-blue-700' : 'bg-pink-50 text-pink-700' }}">
                                                {{ $student->gender === 'male' ? 'L' : 'P' }}
                                            </span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @else
            <div class="p-10 text-center bg-white rounded-xl border border-slate-200 text-slate-500">
                <svg class="w-12 h-12 mx-auto text-slate-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
                <h4 class="font-bold text-sm text-slate-800">Siap Mengacak Kelompok Siswa</h4>
                <p class="text-xs text-slate-400 mt-1 max-w-md mx-auto">
                    Pilih kelas yang ingin dibagi, tentukan jumlah kelompok, lalu klik tombol <strong>"🎲 Acak Kelompok Sekarang"</strong>.
                </p>
            </div>
        @endif
    </div>

    <!-- Copy to Clipboard Script -->
    <script>
        function copyGroupsToClipboard() {
            @if($groups)
                let text = "📋 *DAFTAR KELOMPOK BELAJAR SMPN 2 SUBANG*\n";
                text += "Kelas: {{ $selectedClassSubject?->schoolClass->name }} • {{ $selectedClassSubject?->subject->name }}\n";
                text += "Guru: {{ auth()->user()->name }}\n";
                text += "========================================\n\n";

                @foreach($groups as $grp)
                    text += "*{{ strtoupper($grp['name']) }}* ({{ count($grp['students']) }} Siswa)\n";
                    @foreach($grp['students'] as $idx => $student)
                        text += "{{ $idx + 1 }}. {{ $student->user->name }} ({{ $student->gender == 'male' ? 'L' : 'P' }})\n";
                    @endforeach
                    text += "\n";
                @endforeach

                navigator.clipboard.writeText(text).then(() => {
                    alert('✓ Berhasil menyalin daftar kelompok ke clipboard! Siap dipaste ke grup WhatsApp.');
                }).catch(err => {
                    console.error('Gagal menyalin: ', err);
                });
            @endif
        }
    </script>

    <!-- Print Stylesheet -->
    <style>
        @media print {
            .no-print, header, nav, aside {
                display: none !important;
            }
            body {
                background: white !important;
            }
            #printArea {
                padding: 0 !important;
            }
        }
    </style>
</x-app-layout>
