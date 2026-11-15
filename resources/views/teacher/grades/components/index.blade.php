<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 leading-tight">
            {{ __('Komponen Penilaian & Bobot Nilai') }}
        </h2>
    </x-slot>

    <div class="p-4 sm:p-6 w-full space-y-4">
        @if($errors->any())
            <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-lg text-red-700 text-sm">
                <p class="font-bold">Gagal menyimpan komponen:</p>
                <ul class="list-disc list-inside mt-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Class & Subject Selector -->
        <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-xs">
            <form method="GET" action="{{ route('teacher.grade-components.index') }}">
                <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                    Pilih Kelas & Mata Pelajaran yang Anda Ajar
                </label>
                <select name="class_subject_id" onchange="this.form.submit()" class="w-full sm:w-1/2 rounded-lg border-slate-200 shadow-xs focus:border-blue-500 focus:ring-blue-500 text-sm">
                    @foreach($myClassSubjects as $cs)
                        <option value="{{ $cs->id }}" {{ $selectedClassSubject && $selectedClassSubject->id == $cs->id ? 'selected' : '' }}>
                            Kelas {{ $cs->schoolClass->name }} - {{ $cs->subject->name }} (KKM: {{ $cs->subject->kkm }})
                        </option>
                    @endforeach
                </select>
            </form>
        </div>

        @if($selectedClassSubject)
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
                <!-- Form Tambah Komponen -->
                <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-xs h-fit">
                    <h3 class="font-bold text-slate-800 text-sm mb-4 border-b border-slate-100 pb-2">
                        + Tambah Komponen Penilaian
                    </h3>
                    <form method="POST" action="{{ route('teacher.grade-components.store') }}" class="space-y-3.5">
                        @csrf
                        <input type="hidden" name="class_subject_id" value="{{ $selectedClassSubject->id }}">

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Nama Komponen <span class="text-red-500">*</span></label>
                            <input type="text" name="name" value="{{ old('name') }}" placeholder="Contoh: Tugas 1, UTS, UAS, Praktik" required class="w-full text-sm rounded-lg border-slate-200 shadow-xs focus:border-blue-500 focus:ring-blue-500">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Bobot Persentase (%) <span class="text-red-500">*</span></label>
                            <input type="number" name="weight" value="{{ old('weight') }}" min="1" max="100" step="0.5" required placeholder="Contoh: 20" class="w-full text-sm rounded-lg border-slate-200 shadow-xs focus:border-blue-500 focus:ring-blue-500">
                            <span class="text-xs text-slate-400 mt-1 block">Sisa bobot: <strong>{{ 100 - $totalWeight }}%</strong></span>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Skor Maksimal</label>
                            <input type="number" name="max_score" value="{{ old('max_score', 100) }}" min="1" max="100" class="w-full text-sm rounded-lg border-slate-200 shadow-xs focus:border-blue-500 focus:ring-blue-500">
                        </div>

                        <button type="submit" class="w-full py-2 px-4 bg-blue-600 text-white rounded-lg text-sm font-semibold hover:bg-blue-700 shadow-xs transition">
                            Simpan Komponen
                        </button>
                    </form>
                </div>

                <!-- List Komponen -->
                <div class="lg:col-span-2 space-y-4">
                    <!-- Weight Status Banner -->
                    <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-xs flex items-center justify-between border-l-4 {{ $totalWeight == 100 ? 'border-green-500' : 'border-amber-500' }}">
                        <div>
                            <span class="text-xs font-semibold uppercase text-slate-400">Total Bobot Komponen Saat Ini</span>
                            <h4 class="text-2xl font-black {{ $totalWeight == 100 ? 'text-green-600' : 'text-amber-600' }}">
                                {{ $totalWeight }}% / 100%
                            </h4>
                        </div>
                        <div class="text-right text-xs">
                            @if($totalWeight == 100)
                                <span class="px-2.5 py-1 bg-green-100 text-green-800 rounded-full font-bold">Lengkap (100%)</span>
                            @else
                                <span class="px-2.5 py-1 bg-amber-100 text-amber-800 rounded-full font-bold">Kurang {{ 100 - $totalWeight }}%</span>
                            @endif
                        </div>
                    </div>

                    <!-- Table -->
                    <div class="bg-white rounded-xl border border-slate-200 shadow-xs overflow-hidden">
                        <div class="overflow-auto max-h-[calc(100vh-320px)]">
                            <table class="w-full text-left text-sm text-slate-600 border-collapse">
                                <thead class="bg-slate-50 text-xs font-semibold text-slate-500 uppercase sticky top-0 z-10 border-b border-slate-200">
                                    <tr>
                                        <th class="px-4 py-3 bg-slate-50">Nama Komponen</th>
                                        <th class="px-4 py-3 bg-slate-50 text-center">Bobot</th>
                                        <th class="px-4 py-3 bg-slate-50 text-center">Skor Maks</th>
                                        <th class="px-4 py-3 bg-slate-50 text-center">Siswa Dinilai</th>
                                        <th class="px-4 py-3 bg-slate-50 text-right">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    @forelse($components as $comp)
                                        <tr class="hover:bg-slate-50 transition-colors">
                                            <td class="px-4 py-3 font-semibold text-slate-900">
                                                {{ $comp->name }}
                                            </td>
                                            <td class="px-4 py-3 text-center font-bold text-blue-600">
                                                {{ $comp->weight }}%
                                            </td>
                                            <td class="px-4 py-3 text-center text-slate-700">
                                                {{ $comp->max_score }}
                                            </td>
                                            <td class="px-4 py-3 text-center">
                                                <span class="px-2 py-0.5 rounded text-xs font-semibold bg-slate-100 text-slate-800">
                                                    {{ $comp->grades_count }} Siswa
                                                </span>
                                            </td>
                                            <td class="px-4 py-3 text-right space-x-2 whitespace-nowrap">
                                                <a href="{{ route('teacher.grades.index', $comp->id) }}" class="inline-flex items-center px-3 py-1 bg-green-600 text-white rounded-md text-xs font-semibold hover:bg-green-700 shadow-xs transition">
                                                    Input Nilai
                                                </a>
                                                <form action="{{ route('teacher.grade-components.destroy', $comp->id) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus komponen nilai ini beserta seluruh nilai siswa di dalamnya?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-xs bg-red-50 text-red-700 hover:bg-red-100 px-2.5 py-1 rounded-md font-semibold transition">
                                                        Hapus
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="px-4 py-8 text-center text-slate-400">
                                                Belum ada komponen penilaian untuk kelas & mapel ini. Tambahkan melalui form di samping.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</x-app-layout>
