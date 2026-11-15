<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Pusat Laporan Akademik & Ekspor PDF') }}
        </h2>
    </x-slot>

    <div class="p-4 sm:p-6 w-full space-y-4">
            <!-- Active Year Notice -->
            <div class="bg-blue-50 border-l-4 border-blue-600 p-4 rounded text-blue-800 text-sm flex justify-between items-center">
                <div>
                    Tahun Ajaran Aktif Saat Ini: <strong>{{ $activeYear ? $activeYear->name . ' (' . ucfirst($activeYear->semester) . ')' : 'Belum Ditentukan' }}</strong>
                </div>
                <div class="text-xs text-blue-600">
                    Total {{ $totalEnrollments }} Siswa Terdaftar di {{ $totalClasses }} Kelas
                </div>
            </div>

            <!-- Report Cards Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- 1. Siswa per Kelas -->
                <div class="bg-white p-6 rounded-lg shadow border border-gray-100 flex flex-col justify-between hover:shadow-md transition">
                    <div>
                        <div class="w-12 h-12 bg-blue-100 text-blue-700 rounded-lg flex items-center justify-center text-xl font-bold mb-3">
                            👥
                        </div>
                        <h3 class="text-lg font-bold text-gray-900">Laporan Daftar Siswa per Kelas</h3>
                        <p class="text-sm text-gray-600 mt-2">
                            Cetak daftar rombel (rombongan belajar) siswa aktif berdasarkan kelas dan tahun ajaran, lengkap dengan NIS, data pribadi, dan wali kelas.
                        </p>
                    </div>
                    <div class="mt-6 flex space-x-3">
                        <a href="{{ route('admin.reports.students') }}" class="px-4 py-2 bg-blue-600 text-white rounded-md text-xs font-semibold hover:bg-blue-700">
                            Buka Laporan
                        </a>
                        <a href="{{ route('admin.reports.students', ['print' => 1]) }}" target="_blank" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-md text-xs font-semibold hover:bg-gray-200">
                            🖨️ Cetak Langsung
                        </a>
                    </div>
                </div>

                <!-- 2. Rekap Absensi -->
                <div class="bg-white p-6 rounded-lg shadow border border-gray-100 flex flex-col justify-between hover:shadow-md transition">
                    <div>
                        <div class="w-12 h-12 bg-green-100 text-green-700 rounded-lg flex items-center justify-center text-xl font-bold mb-3">
                            📅
                        </div>
                        <h3 class="text-lg font-bold text-gray-900">Laporan Rekap Absensi Siswa</h3>
                        <p class="text-sm text-gray-600 mt-2">
                            Rekapitulasi kehadiran siswa per mata pelajaran dan kelas, mencakup akumulasi Hadir, Izin, Sakit, Alpa, dan persentase kehadiran kumulatif.
                        </p>
                    </div>
                    <div class="mt-6 flex space-x-3">
                        <a href="{{ route('admin.reports.attendances') }}" class="px-4 py-2 bg-green-600 text-white rounded-md text-xs font-semibold hover:bg-green-700">
                            Buka Laporan
                        </a>
                        <a href="{{ route('admin.reports.attendances', ['print' => 1]) }}" target="_blank" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-md text-xs font-semibold hover:bg-gray-200">
                            🖨️ Cetak Langsung
                        </a>
                    </div>
                </div>

                <!-- 3. Rekap Nilai -->
                <div class="bg-white p-6 rounded-lg shadow border border-gray-100 flex flex-col justify-between hover:shadow-md transition">
                    <div>
                        <div class="w-12 h-12 bg-purple-100 text-purple-700 rounded-lg flex items-center justify-center text-xl font-bold mb-3">
                            📊
                        </div>
                        <h3 class="text-lg font-bold text-gray-900">Laporan Rekap Nilai Akademik</h3>
                        <p class="text-sm text-gray-600 mt-2">
                            Daftar nilai seluruh siswa per kelas dan mata pelajaran, rincian skor per komponen berbobot, nilai akhir tertimbang, status KKM, dan predikat.
                        </p>
                    </div>
                    <div class="mt-6 flex space-x-3">
                        <a href="{{ route('admin.reports.grades') }}" class="px-4 py-2 bg-purple-600 text-white rounded-md text-xs font-semibold hover:bg-purple-700">
                            Buka Laporan
                        </a>
                        <a href="{{ route('admin.reports.grades', ['print' => 1]) }}" target="_blank" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-md text-xs font-semibold hover:bg-gray-200">
                            🖨️ Cetak Langsung
                        </a>
                    </div>
                </div>

                <!-- 4. Rekap Penugasan -->
                <div class="bg-white p-6 rounded-lg shadow border border-gray-100 flex flex-col justify-between hover:shadow-md transition">
                    <div>
                        <div class="w-12 h-12 bg-amber-100 text-amber-700 rounded-lg flex items-center justify-center text-xl font-bold mb-3">
                            📝
                        </div>
                        <h3 class="text-lg font-bold text-gray-900">Laporan Penugasan E-Learning</h3>
                        <p class="text-sm text-gray-600 mt-2">
                            Ringkasan seluruh penugasan online yang diberikan oleh guru, tingkat kepatuhan pengumpulan siswa, status keterlambatan, dan riwayat penilaian.
                        </p>
                    </div>
                    <div class="mt-6 flex space-x-3">
                        <a href="{{ route('admin.reports.assignments') }}" class="px-4 py-2 bg-amber-600 text-white rounded-md text-xs font-semibold hover:bg-amber-700">
                            Buka Laporan
                        </a>
                        <a href="{{ route('admin.reports.assignments', ['print' => 1]) }}" target="_blank" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-md text-xs font-semibold hover:bg-gray-200">
                            🖨️ Cetak Langsung
                        </a>
                    </div>
                </div>
            </div>
        </div>
</x-app-layout>
