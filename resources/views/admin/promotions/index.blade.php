<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2">
            <div>
                <h2 class="font-extrabold text-xl text-slate-900 leading-tight">
                    {{ __('Kenaikan Kelas & Distribusi Acak Siswa') }}
                </h2>
                <p class="text-xs text-slate-500 mt-0.5">Menaikkan 1 kelas penuh secara massal atau mendistribusikan siswa secara acak & merata ke kelas tingkat selanjutnya.</p>
            </div>
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-purple-100 text-purple-800 border border-purple-200">
                🛠️ Khusus Administrator
            </span>
        </div>
    </x-slot>

    <div class="p-4 sm:p-6 space-y-6" x-data="{ activeTab: 'direct', previewLoading: false, previewData: null }">

        <!-- Alerts -->
        @if(session('success'))
            <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm flex items-center space-x-2">
                <svg class="w-5 h-5 text-emerald-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                <span class="font-semibold">{{ session('success') }}</span>
            </div>
        @endif

        @if(session('error'))
            <div class="p-4 rounded-xl bg-red-50 border border-red-200 text-red-800 text-sm flex items-center space-x-2">
                <svg class="w-5 h-5 text-red-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                </svg>
                <span class="font-semibold">{{ session('error') }}</span>
            </div>
        @endif

        <!-- Tab Selector -->
        <div class="flex border-b border-slate-200 space-x-4 bg-white px-4 pt-3 rounded-t-xl border-t border-x">
            <button type="button"
                    @click="activeTab = 'direct'"
                    :class="activeTab === 'direct' ? 'border-blue-600 text-blue-600 font-bold' : 'border-transparent text-slate-500 hover:text-slate-700 font-medium'"
                    class="py-3 px-4 border-b-2 text-sm transition flex items-center space-x-2">
                <span>📦 Kenaikan Langsung 1 Kelas</span>
            </button>
            <button type="button"
                    @click="activeTab = 'distribute'"
                    :class="activeTab === 'distribute' ? 'border-blue-600 text-blue-600 font-bold' : 'border-transparent text-slate-500 hover:text-slate-700 font-medium'"
                    class="py-3 px-4 border-b-2 text-sm transition flex items-center space-x-2">
                <span>🎲 Acak & Distribusi Siswa Tingkat Lanjut</span>
            </button>
        </div>

        <!-- TAB 1: Kenaikan Langsung 1 Kelas Penuh -->
        <div x-show="activeTab === 'direct'" class="bg-white p-6 rounded-b-xl border border-slate-200 shadow-xs space-y-6">
            <div>
                <h3 class="text-base font-extrabold text-slate-900">Naikkan Massal 1 Kelas Penuh</h3>
                <p class="text-xs text-slate-500 mt-0.5">Gunakan opsi ini jika seluruh siswa dalam satu kelas naik kelas bersama ke kelas tujuan tertentu (misal: Seluruh 7-A naik ke 8-A).</p>
            </div>

            <form action="{{ route('admin.promotions.direct') }}" method="POST" class="space-y-5" onsubmit="return confirm('Apakah Anda yakin ingin memindahkan seluruh siswa ke kelas tujuan?');">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 p-4 rounded-xl bg-slate-50 border border-slate-200">
                    <!-- Asal -->
                    <div class="space-y-3">
                        <div class="font-bold text-xs uppercase tracking-wider text-slate-700 flex items-center text-blue-700">
                            <span class="w-6 h-6 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center me-2 text-xs font-black">1</span>
                            Kelas Asal (Saat Ini)
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-1">Tahun Ajaran Asal</label>
                            <select name="from_academic_year_id" required class="w-full text-sm rounded-lg border-slate-300">
                                @foreach($academicYears as $y)
                                    <option value="{{ $y->id }}" {{ $activeYear?->id == $y->id ? 'selected' : '' }}>
                                        {{ $y->name }} ({{ $y->semester == 'odd' ? 'Ganjil' : 'Genap' }}) {{ $y->is_active ? '• Aktif' : '' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-1">Pilih Kelas Asal</label>
                            <select name="from_class_id" required class="w-full text-sm rounded-lg border-slate-300">
                                <option value="" disabled selected>-- Pilih Kelas Asal --</option>
                                @foreach($classes as $c)
                                    <option value="{{ $c->id }}">
                                        {{ $c->name }} (Tingkat {{ $c->grade_level }} • {{ $c->enrollments_count }} siswa)
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Tujuan -->
                    <div class="space-y-3">
                        <div class="font-bold text-xs uppercase tracking-wider text-slate-700 flex items-center text-emerald-700">
                            <span class="w-6 h-6 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center me-2 text-xs font-black">2</span>
                            Kelas Tujuan (Kenaikan)
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-1">Tahun Ajaran Tujuan</label>
                            <select name="to_academic_year_id" required class="w-full text-sm rounded-lg border-slate-300">
                                @foreach($academicYears as $y)
                                    <option value="{{ $y->id }}" {{ $activeYear?->id == $y->id ? 'selected' : '' }}>
                                        {{ $y->name }} ({{ $y->semester == 'odd' ? 'Ganjil' : 'Genap' }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-1">Pilih Kelas Tujuan</label>
                            <select name="to_class_id" required class="w-full text-sm rounded-lg border-slate-300">
                                <option value="" disabled selected>-- Pilih Kelas Tujuan --</option>
                                @foreach($classes as $c)
                                    <option value="{{ $c->id }}">
                                        {{ $c->name }} (Tingkat {{ $c->grade_level }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end pt-2">
                    <button type="submit" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-bold shadow-sm transition">
                        🚀 Proses Kenaikan 1 Kelas Penuh
                    </button>
                </div>
            </form>
        </div>

        <!-- TAB 2: Distribusi & Acak Siswa ke Tingkat Selanjutnya -->
        <div x-show="activeTab === 'distribute'" class="bg-white p-6 rounded-b-xl border border-slate-200 shadow-xs space-y-6">
            <div>
                <h3 class="text-base font-extrabold text-slate-900">Distribusi Acak & Keseimbangan Siswa</h3>
                <p class="text-xs text-slate-500 mt-0.5">Kumpulkan siswa dari beberapa kelas asal (misal: seluruh kelas 7), lalu sistem akan mengacak dan membagikan siswa secara adil, rata, dan seimbang gender ke kelas tingkat selanjutnya.</p>
            </div>

            <form action="{{ route('admin.promotions.distribute.apply') }}" method="POST" id="distributeForm" class="space-y-6">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 p-4 rounded-xl bg-slate-50 border border-slate-200">
                    <!-- Step 1: Kelas Asal -->
                    <div class="space-y-3">
                        <div class="font-bold text-xs uppercase tracking-wider text-blue-700 flex items-center">
                            <span class="w-6 h-6 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center me-2 text-xs font-black">1</span>
                            Pilih Kelas-Kelas Asal
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-1">Tahun Ajaran Asal</label>
                            <select name="from_academic_year_id" id="fromYearId" required class="w-full text-sm rounded-lg border-slate-300">
                                @foreach($academicYears as $y)
                                    <option value="{{ $y->id }}" {{ $activeYear?->id == $y->id ? 'selected' : '' }}>
                                        {{ $y->name }} ({{ $y->semester == 'odd' ? 'Ganjil' : 'Genap' }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-1.5">Centang Kelas Asal yang Mau Diacak:</label>
                            <div class="space-y-1.5 max-h-48 overflow-y-auto p-2 bg-white rounded-lg border border-slate-200">
                                @foreach($classes as $c)
                                    <label class="flex items-center space-x-2 p-1.5 rounded hover:bg-slate-50 text-xs text-slate-700 cursor-pointer">
                                        <input type="checkbox" name="from_class_ids[]" value="{{ $c->id }}" class="from-class-cb rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                                        <span class="font-bold">{{ $c->name }}</span>
                                        <span class="text-slate-400">(Tingkat {{ $c->grade_level }} • {{ $c->enrollments_count }} siswa)</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- Step 2: Kelas Tujuan -->
                    <div class="space-y-3">
                        <div class="font-bold text-xs uppercase tracking-wider text-emerald-700 flex items-center">
                            <span class="w-6 h-6 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center me-2 text-xs font-black">2</span>
                            Pilih Kelas-Kelas Tujuan (Tingkat Lanjut)
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-1">Tahun Ajaran Tujuan</label>
                            <select name="to_academic_year_id" required class="w-full text-sm rounded-lg border-slate-300">
                                @foreach($academicYears as $y)
                                    <option value="{{ $y->id }}" {{ $activeYear?->id == $y->id ? 'selected' : '' }}>
                                        {{ $y->name }} ({{ $y->semester == 'odd' ? 'Ganjil' : 'Genap' }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-1.5">Centang Kelas Tujuan Pembagian:</label>
                            <div class="space-y-1.5 max-h-48 overflow-y-auto p-2 bg-white rounded-lg border border-slate-200">
                                @foreach($classes as $c)
                                    <label class="flex items-center space-x-2 p-1.5 rounded hover:bg-slate-50 text-xs text-slate-700 cursor-pointer">
                                        <input type="checkbox" name="to_class_ids[]" value="{{ $c->id }}" class="to-class-cb rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                                        <span class="font-bold">{{ $c->name }}</span>
                                        <span class="text-slate-400">(Tingkat {{ $c->grade_level }})</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Parameter Pengacakan -->
                <div class="p-4 rounded-xl bg-purple-50/60 border border-purple-200 grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="balance_gender" value="1" checked class="w-4 h-4 rounded border-purple-300 text-purple-600 focus:ring-purple-500">
                            <span class="ms-2 text-xs font-bold text-slate-800">Seimbangkan Komposisi Gender (Laki-laki & Perempuan Sama Rata)</span>
                        </label>
                        <p class="text-[11px] text-slate-500 mt-1 ms-6">Mencegah terjadinya kelas yang mayoritas hanya cowok atau cewek.</p>
                    </div>
                    <div class="flex items-center space-x-2">
                        <label class="text-xs font-bold text-slate-700 whitespace-nowrap">Batas Kuota Siswa / Kelas:</label>
                        <input type="number" name="quota_per_class" placeholder="Otomatis" min="1" max="50" class="w-24 text-xs rounded border-slate-300">
                        <span class="text-[11px] text-slate-400">(Opsional)</span>
                    </div>
                </div>

                <!-- Action Buttons: Preview & Submit -->
                <div class="flex items-center justify-between pt-2">
                    <button type="button"
                            @click="triggerPreview()"
                            class="px-4 py-2.5 bg-slate-800 hover:bg-slate-900 text-white rounded-lg text-sm font-bold transition flex items-center space-x-2 shadow-xs">
                        <svg class="w-4 h-4 text-amber-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        <span>🎲 Pratinjau Pengacakan (Simulasi)</span>
                    </button>

                    <button type="submit"
                            onclick="return confirm('Apakah Anda yakin ingin menerapkan hasil pembagian acak ini ke database?');"
                            class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-sm font-bold shadow-sm transition">
                        ✓ Simpan & Terapkan Pembagian
                    </button>
                </div>
            </form>

            <!-- Preview Result Container -->
            <div x-show="previewLoading" class="p-8 text-center bg-slate-50 rounded-xl border border-slate-200">
                <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 mb-2"></div>
                <p class="text-xs font-semibold text-slate-600">Sedang mengacak dan menghitung komposisi seimbang...</p>
            </div>

            <div x-show="previewData && !previewLoading" class="space-y-4 pt-4 border-t border-slate-200">
                <div class="flex items-center justify-between">
                    <h4 class="font-extrabold text-sm text-slate-800 flex items-center">
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 me-2 animate-ping"></span>
                        Hasil Simulasi Pengacakan: <span class="ms-1 text-blue-600 font-mono" x-text="previewData?.total_students"></span> Siswa Dibagi ke <span class="ms-1 text-emerald-600 font-mono" x-text="previewData?.total_target_classes"></span> Kelas
                    </h4>
                    <button type="button" @click="triggerPreview()" class="text-xs text-blue-600 hover:text-blue-800 font-bold underline">
                        🎲 Acak Ulang (Re-shuffle)
                    </button>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <template x-for="item in previewData?.distribution" :key="item.class.id">
                        <div class="bg-slate-50 p-4 rounded-xl border border-slate-200 shadow-xs">
                            <div class="flex justify-between items-center pb-2 border-b border-slate-200 mb-2">
                                <span class="font-black text-sm text-slate-900" x-text="item.class.name"></span>
                                <span class="text-xs font-bold px-2 py-0.5 rounded-full bg-blue-100 text-blue-800" x-text="item.students.length + ' Siswa'"></span>
                            </div>
                            <div class="flex items-center space-x-2 text-[11px] text-slate-500 mb-2 font-medium">
                                <span>👦 L: <strong x-text="item.male_count" class="text-blue-700"></strong></span>
                                <span>•</span>
                                <span>👧 P: <strong x-text="item.female_count" class="text-pink-700"></strong></span>
                            </div>
                            <div class="max-h-48 overflow-y-auto space-y-1 text-xs">
                                <template x-for="(s, sIdx) in item.students" :key="s.id">
                                    <div class="flex justify-between items-center p-1 rounded bg-white border border-slate-100 text-[11px]">
                                        <span class="truncate" x-text="(sIdx + 1) + '. ' + s.name"></span>
                                        <span class="text-[10px] px-1 rounded font-bold"
                                              :class="s.gender === 'male' ? 'bg-blue-50 text-blue-600' : 'bg-pink-50 text-pink-600'"
                                              x-text="s.gender === 'male' ? 'L' : 'P'"></span>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>

    <!-- Preview Script -->
    <script>
        function triggerPreview() {
            const form = document.getElementById('distributeForm');
            const formData = new FormData(form);

            const fromClasses = form.querySelectorAll('.from-class-cb:checked');
            const toClasses = form.querySelectorAll('.to-class-cb:checked');

            if (fromClasses.length === 0 || toClasses.length === 0) {
                alert('Pilih minimal 1 kelas asal dan 1 kelas tujuan terlebih dahulu!');
                return;
            }

            const alpineComp = Alpine.$data(document.querySelector('[x-data]'));
            alpineComp.previewLoading = true;

            fetch("{{ route('admin.promotions.distribute.preview') }}", {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    "Accept": "application/json"
                },
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                alpineComp.previewLoading = false;
                alpineComp.previewData = data;
            })
            .catch(err => {
                alpineComp.previewLoading = false;
                alert('Terjadi kesalahan saat membuat simulasi pengacakan.');
                console.error(err);
            });
        }
    </script>
</x-app-layout>
