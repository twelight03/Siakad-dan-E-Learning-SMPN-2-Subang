<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-slate-800 leading-tight">
                {{ __('Edit Sesi Absensi') }}
            </h2>
            <a href="{{ route('teacher.attendances.show', $session->id) }}" class="text-sm text-slate-500 hover:text-blue-600 transition font-medium">
                &larr; Batal & Kembali
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

        <form method="POST" action="{{ route('teacher.attendances.update', $session->id) }}" class="space-y-4">
            @csrf
            @method('PUT')

            <!-- Session Meta -->
            <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-xs grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-400 uppercase">Kelas & Mata Pelajaran</label>
                    <p class="text-sm font-bold text-slate-800 mt-1">
                        {{ $session->classSubject->schoolClass->name }} - {{ $session->classSubject->subject->name }}
                    </p>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Tanggal Sesi <span class="text-red-500">*</span></label>
                    <input type="date" name="session_date" value="{{ old('session_date', $session->session_date->format('Y-m-d')) }}" required class="w-full text-sm rounded-lg border-slate-200 shadow-xs focus:border-blue-500 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Topik / Materi Pembahasan</label>
                    <input type="text" name="topic" value="{{ old('topic', $session->topic) }}" class="w-full text-sm rounded-lg border-slate-200 shadow-xs focus:border-blue-500 focus:ring-blue-500">
                </div>
            </div>

            <!-- Students Attendance Edit Card -->
            <div class="bg-white rounded-xl border border-slate-200 shadow-xs overflow-hidden">
                <div class="px-6 py-3 bg-slate-50 border-b border-slate-200">
                    <h3 class="font-bold text-slate-800 text-sm">
                        Perbarui Status Kehadiran Siswa
                    </h3>
                </div>
                <div class="overflow-auto max-h-[calc(100vh-380px)]">
                    <table class="w-full text-left text-sm text-slate-600 border-collapse">
                        <thead class="bg-slate-50 text-xs font-semibold text-slate-500 uppercase sticky top-0 z-10 border-b border-slate-200">
                            <tr>
                                <th class="px-4 py-3 bg-slate-50 text-center w-12">No</th>
                                <th class="px-4 py-3 bg-slate-50">NIS</th>
                                <th class="px-4 py-3 bg-slate-50">Nama Siswa</th>
                                <th class="px-4 py-3 bg-slate-50 text-center">Status Kehadiran</th>
                                <th class="px-4 py-3 bg-slate-50">Catatan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($session->attendances as $index => $att)
                                <tr class="hover:bg-slate-50 transition-colors">
                                    <td class="px-4 py-3 text-center text-slate-400 font-mono text-xs">
                                        {{ $index + 1 }}
                                        <input type="hidden" name="attendances[{{ $index }}][id]" value="{{ $att->id }}">
                                        <input type="hidden" name="attendances[{{ $index }}][student_id]" value="{{ $att->student_id }}">
                                    </td>
                                    <td class="px-4 py-3 font-mono text-slate-700">
                                        {{ $att->student->nis }}
                                    </td>
                                    <td class="px-4 py-3 font-semibold text-slate-900">
                                        {{ $att->student->user->name }}
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <div class="inline-flex space-x-3 text-xs font-medium">
                                            <label class="inline-flex items-center text-green-700 cursor-pointer">
                                                <input type="radio" name="attendances[{{ $index }}][status]" value="present" {{ $att->status === 'present' ? 'checked' : '' }} class="text-green-600 focus:ring-green-500 me-1">
                                                Hadir
                                            </label>
                                            <label class="inline-flex items-center text-blue-700 cursor-pointer">
                                                <input type="radio" name="attendances[{{ $index }}][status]" value="excused" {{ $att->status === 'excused' ? 'checked' : '' }} class="text-blue-600 focus:ring-blue-500 me-1">
                                                Izin
                                            </label>
                                            <label class="inline-flex items-center text-amber-700 cursor-pointer">
                                                <input type="radio" name="attendances[{{ $index }}][status]" value="sick" {{ $att->status === 'sick' ? 'checked' : '' }} class="text-amber-600 focus:ring-amber-500 me-1">
                                                Sakit
                                            </label>
                                            <label class="inline-flex items-center text-red-700 cursor-pointer">
                                                <input type="radio" name="attendances[{{ $index }}][status]" value="absent" {{ $att->status === 'absent' ? 'checked' : '' }} class="text-red-600 focus:ring-red-500 me-1">
                                                Alpa
                                            </label>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3">
                                        <input type="text" name="attendances[{{ $index }}][note]" value="{{ old("attendances.{$index}.note", $att->note) }}" placeholder="Catatan..." class="w-full text-xs rounded-lg border-slate-200 shadow-xs focus:border-blue-500 focus:ring-blue-500">
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="p-4 bg-slate-50 border-t border-slate-200 flex justify-end space-x-3">
                    <a href="{{ route('teacher.attendances.show', $session->id) }}" class="px-4 py-2 border border-slate-200 rounded-lg text-sm text-slate-700 bg-white hover:bg-slate-50 transition">
                        Batal
                    </a>
                    <button type="submit" class="px-6 py-2 bg-blue-600 text-white font-semibold rounded-lg shadow-xs text-sm hover:bg-blue-700 transition">
                        Simpan Perubahan
                    </button>
                </div>
            </div>
        </form>
    </div>
</x-app-layout>
