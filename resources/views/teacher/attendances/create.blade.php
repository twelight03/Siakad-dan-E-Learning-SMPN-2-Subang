<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-slate-800 leading-tight">
                {{ __('Catat Absensi Siswa') }}
            </h2>
            <a href="{{ route('teacher.attendances.index') }}" class="text-sm text-slate-500 hover:text-blue-600 transition font-medium">
                &larr; Kembali ke Daftar Absensi
            </a>
        </div>
    </x-slot>

    <div class="p-4 sm:p-6 w-full space-y-4">
        @if($errors->any())
            <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-xl text-red-700 text-sm">
                <p class="font-bold">Mohon perbaiki kesalahan berikut:</p>
                <ul class="list-disc list-inside mt-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Step 1: Select Class & Subject -->
        <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-xs">
            <form method="GET" action="{{ route('teacher.attendances.create') }}">
                <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                    Pilih Kelas dan Mata Pelajaran yang Diampu
                </label>
                <div class="flex flex-col sm:flex-row gap-3">
                    <select name="class_subject_id" onchange="this.form.submit()" class="flex-1 rounded-lg border-slate-200 shadow-xs focus:border-blue-500 focus:ring-blue-500 text-sm">
                        <option value="">Pilih Kelas & Mata Pelajaran</option>
                        @foreach($myClassSubjects as $cs)
                            <option value="{{ $cs->id }}" {{ (request('class_subject_id') == $cs->id || ($selectedClassSubject && $selectedClassSubject->id == $cs->id)) ? 'selected' : '' }}>
                                Kelas {{ $cs->schoolClass->name }} - {{ $cs->subject->name }} (Tingkat {{ $cs->schoolClass->grade_level }})
                            </option>
                        @endforeach
                    </select>
                </div>
            </form>
        </div>

        @if($selectedClassSubject)
            <!-- Step 2: Form Absensi -->
            <form method="POST" action="{{ route('teacher.attendances.store') }}" class="space-y-4">
                @csrf
                <input type="hidden" name="class_subject_id" value="{{ $selectedClassSubject->id }}">

                <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-xs grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Tanggal Pertemuan / Absensi <span class="text-red-500">*</span></label>
                        <input type="date" name="session_date" value="{{ old('session_date', date('Y-m-d')) }}" required class="block w-full rounded-lg border-slate-200 shadow-xs focus:border-blue-500 focus:ring-blue-500 text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Materi Pokok / Topik Pembahasan</label>
                        <input type="text" name="topic" value="{{ old('topic') }}" placeholder="Contoh: Bab 2 Aljabar Linier" class="block w-full rounded-lg border-slate-200 shadow-xs focus:border-blue-500 focus:ring-blue-500 text-sm">
                    </div>
                </div>

                <!-- Students List Card -->
                <div class="bg-white rounded-xl border border-slate-200 shadow-xs overflow-hidden">
                    <div class="px-6 py-3 bg-slate-50 border-b border-slate-200 flex justify-between items-center">
                        <h3 class="font-bold text-slate-800 text-sm">
                            Daftar Siswa Kelas {{ $selectedClassSubject->schoolClass->name }} ({{ $students->count() }} Siswa)
                        </h3>
                        <div class="text-xs text-slate-400">
                            Default status: <span class="font-semibold text-green-700">Hadir</span>
                        </div>
                    </div>

                    @if($students->isEmpty())
                        <div class="p-8 text-center text-slate-400 text-sm">
                            Belum ada siswa terdaftar pada kelas ini untuk tahun ajaran aktif. Silakan hubungi admin sekolah.
                        </div>
                    @else
                        <div class="overflow-auto max-h-[calc(100vh-380px)]">
                            <table class="w-full text-left text-sm text-slate-600 border-collapse">
                                <thead class="bg-slate-50 text-xs font-semibold text-slate-500 uppercase sticky top-0 z-10 border-b border-slate-200">
                                    <tr>
                                        <th class="px-4 py-3 bg-slate-50 text-center w-12">No</th>
                                        <th class="px-4 py-3 bg-slate-50">NIS</th>
                                        <th class="px-4 py-3 bg-slate-50">Nama Siswa</th>
                                        <th class="px-4 py-3 bg-slate-50 text-center">Status Kehadiran</th>
                                        <th class="px-4 py-3 bg-slate-50">Catatan (Opsional)</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    @foreach($students as $index => $student)
                                        <tr class="hover:bg-slate-50 transition-colors">
                                            <td class="px-4 py-3 text-center text-slate-400 font-mono text-xs">
                                                {{ $index + 1 }}
                                                <input type="hidden" name="attendances[{{ $index }}][student_id]" value="{{ $student->id }}">
                                            </td>
                                            <td class="px-4 py-3 font-mono text-slate-700">
                                                {{ $student->nis }}
                                            </td>
                                            <td class="px-4 py-3 font-semibold text-slate-900">
                                                {{ $student->user->name }}
                                            </td>
                                            <td class="px-4 py-3 text-center">
                                                <div class="inline-flex space-x-3 text-xs font-medium">
                                                    <label class="inline-flex items-center text-green-700 cursor-pointer">
                                                        <input type="radio" name="attendances[{{ $index }}][status]" value="present" checked class="text-green-600 focus:ring-green-500 me-1">
                                                        Hadir
                                                    </label>
                                                    <label class="inline-flex items-center text-blue-700 cursor-pointer">
                                                        <input type="radio" name="attendances[{{ $index }}][status]" value="excused" class="text-blue-600 focus:ring-blue-500 me-1">
                                                        Izin
                                                    </label>
                                                    <label class="inline-flex items-center text-amber-700 cursor-pointer">
                                                        <input type="radio" name="attendances[{{ $index }}][status]" value="sick" class="text-amber-600 focus:ring-amber-500 me-1">
                                                        Sakit
                                                    </label>
                                                    <label class="inline-flex items-center text-red-700 cursor-pointer">
                                                        <input type="radio" name="attendances[{{ $index }}][status]" value="absent" class="text-red-600 focus:ring-red-500 me-1">
                                                        Alpa
                                                    </label>
                                                </div>
                                            </td>
                                            <td class="px-4 py-3">
                                                <input type="text" name="attendances[{{ $index }}][note]" placeholder="Keterangan..." class="w-full text-xs rounded-lg border-slate-200 shadow-xs focus:border-blue-500 focus:ring-blue-500">
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="p-4 bg-slate-50 border-t border-slate-200 flex justify-end space-x-3">
                            <a href="{{ route('teacher.attendances.index') }}" class="px-4 py-2 border border-slate-200 rounded-lg text-sm text-slate-700 bg-white hover:bg-slate-50 transition">
                                Batal
                            </a>
                            <button type="submit" class="px-6 py-2 bg-blue-600 text-white font-semibold rounded-lg shadow-xs text-sm hover:bg-blue-700 transition">
                                Simpan Absensi
                            </button>
                        </div>
                    @endif
                </div>
            </form>
        @endif
    </div>
</x-app-layout>
