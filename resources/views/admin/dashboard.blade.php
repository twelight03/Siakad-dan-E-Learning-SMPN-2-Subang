<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-2">
            <h2 class="font-semibold text-lg sm:text-xl text-gray-800 leading-tight">
                {{ __('Dashboard Administrator') }}
            </h2>
            <span class="text-xs sm:text-sm bg-blue-100 text-blue-800 font-medium px-2.5 py-1 rounded-full self-start sm:self-auto">
                Tahun Ajaran: {{ $stats['active_academic_year']->name ?? 'Belum ada tahun aktif' }} ({{ $stats['active_academic_year']?->semester == 'odd' ? 'Ganjil' : 'Genap' }})
            </span>
        </div>
    </x-slot>

    <div class="p-4 sm:p-6 w-full space-y-4">
            <!-- Stat Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-blue-500">
                    <div class="text-sm font-medium text-gray-500">Total Siswa</div>
                    <div class="mt-2 text-3xl font-bold text-gray-900">{{ $stats['total_students'] }}</div>
                    <div class="mt-1 text-xs text-gray-400">Terdaftar di database</div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-green-500">
                    <div class="text-sm font-medium text-gray-500">Total Guru</div>
                    <div class="mt-2 text-3xl font-bold text-gray-900">{{ $stats['total_teachers'] }}</div>
                    <div class="mt-1 text-xs text-gray-400">Pengajar aktif</div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-purple-500">
                    <div class="text-sm font-medium text-gray-500">Total Rombel / Kelas</div>
                    <div class="mt-2 text-3xl font-bold text-gray-900">{{ $stats['total_classes'] }}</div>
                    <div class="mt-1 text-xs text-gray-400">Kelas aktif</div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-yellow-500">
                    <div class="text-sm font-medium text-gray-500">Mata Pelajaran</div>
                    <div class="mt-2 text-3xl font-bold text-gray-900">{{ $stats['total_subjects'] }}</div>
                    <div class="mt-1 text-xs text-gray-400">Kurikulum aktif</div>
                </div>
            </div>

            <!-- Quick Navigation Shortcuts -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Navigasi Manajemen Utama</h3>
                <div class="grid grid-cols-2 md:grid-cols-6 gap-4 text-center">
                    <a href="{{ Route::has('admin.students.index') ? route('admin.students.index') : '#' }}" class="p-4 rounded-lg bg-blue-50 hover:bg-blue-100 text-blue-700 transition">
                        <div class="font-semibold text-sm">Data Siswa</div>
                    </a>
                    <a href="{{ Route::has('admin.teachers.index') ? route('admin.teachers.index') : '#' }}" class="p-4 rounded-lg bg-green-50 hover:bg-green-100 text-green-700 transition">
                        <div class="font-semibold text-sm">Data Guru</div>
                    </a>
                    <a href="{{ Route::has('admin.classes.index') ? route('admin.classes.index') : '#' }}" class="p-4 rounded-lg bg-purple-50 hover:bg-purple-100 text-purple-700 transition">
                        <div class="font-semibold text-sm">Data Kelas</div>
                    </a>
                    <a href="{{ Route::has('admin.subjects.index') ? route('admin.subjects.index') : '#' }}" class="p-4 rounded-lg bg-yellow-50 hover:bg-yellow-100 text-yellow-700 transition">
                        <div class="font-semibold text-sm">Mata Pelajaran</div>
                    </a>
                    <a href="{{ Route::has('admin.academic-years.index') ? route('admin.academic-years.index') : '#' }}" class="p-4 rounded-lg bg-indigo-50 hover:bg-indigo-100 text-indigo-700 transition">
                        <div class="font-semibold text-sm">Tahun Ajaran</div>
                    </a>
                    <a href="{{ Route::has('admin.enrollments.index') ? route('admin.enrollments.index') : '#' }}" class="p-4 rounded-lg bg-red-50 hover:bg-red-100 text-red-700 transition">
                        <div class="font-semibold text-sm">Penempatan Siswa</div>
                    </a>
                </div>
            </div>

            <!-- Visual Charts -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-base font-semibold text-gray-800 mb-4">Distribusi Siswa per Kelas</h3>
                    <div class="h-64">
                        <canvas id="classChart"></canvas>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-base font-semibold text-gray-800 mb-4">Distribusi Akun Pengguna</h3>
                    <div class="h-64 flex justify-center">
                        <canvas id="roleChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Recent Activity Log -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Log Aktivitas Terbaru</h3>
                @if($recentActivities->isEmpty())
                    <p class="text-gray-500 text-sm">Belum ada riwayat aktivitas yang tercatat.</p>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 text-sm">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-2 text-left font-medium text-gray-500">Waktu</th>
                                    <th class="px-4 py-2 text-left font-medium text-gray-500">Pengguna</th>
                                    <th class="px-4 py-2 text-left font-medium text-gray-500">Aksi</th>
                                    <th class="px-4 py-2 text-left font-medium text-gray-500">Deskripsi</th>
                                    <th class="px-4 py-2 text-left font-medium text-gray-500">IP Address</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($recentActivities as $log)
                                    <tr>
                                        <td class="px-4 py-2 text-gray-600 whitespace-nowrap">{{ $log->created_at->format('d/m/Y H:i:s') }}</td>
                                        <td class="px-4 py-2 font-medium text-gray-800">{{ $log->user?->name ?? 'Sistem' }}</td>
                                        <td class="px-4 py-2">
                                            <span class="px-2 py-0.5 rounded text-xs font-semibold {{ $log->action == 'login' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800' }}">
                                                {{ strtoupper($log->action) }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-2 text-gray-600">{{ $log->description }}</td>
                                        <td class="px-4 py-2 text-gray-500 font-mono text-xs">{{ $log->ip_address ?? '-' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            if (window.Chart) {
                // Class distribution chart
                const ctxClass = document.getElementById('classChart');
                if (ctxClass) {
                    new Chart(ctxClass, {
                        type: 'bar',
                        data: {
                            labels: @json($classLabels),
                            datasets: [{
                                label: 'Jumlah Siswa',
                                data: @json($classCounts),
                                backgroundColor: 'rgba(59, 130, 246, 0.7)',
                                borderColor: 'rgb(59, 130, 246)',
                                borderWidth: 1,
                                borderRadius: 4,
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    ticks: { precision: 0 }
                                }
                            }
                        }
                    });
                }

                // Role distribution chart
                const ctxRole = document.getElementById('roleChart');
                if (ctxRole) {
                    new Chart(ctxRole, {
                        type: 'doughnut',
                        data: {
                            labels: @json($roleLabels),
                            datasets: [{
                                data: @json($roleCounts),
                                backgroundColor: [
                                    'rgba(147, 51, 234, 0.7)',
                                    'rgba(16, 185, 129, 0.7)',
                                    'rgba(59, 130, 246, 0.7)',
                                ],
                                borderWidth: 1
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                        }
                    });
                }
            }
        });
    </script>
    @endpush
</x-app-layout>
