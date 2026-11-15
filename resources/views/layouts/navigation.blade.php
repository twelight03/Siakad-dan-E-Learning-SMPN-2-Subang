<nav x-data="{ open: false }" class="bg-white border-b border-gray-100 shadow-sm">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}" class="flex items-center space-x-2">
                        <div class="w-8 h-8 rounded bg-blue-600 text-white font-black flex items-center justify-center text-sm shadow">
                            S2
                        </div>
                        <span class="font-bold text-gray-900 hidden md:inline text-base">SIAKAD SMPN 2</span>
                    </a>
                </div>

                <!-- Navigation Links based on Role -->
                <div class="hidden space-x-4 sm:-my-px sm:ms-8 lg:flex">
                    @if(Auth::user()->isAdmin())
                        <x-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')">
                            Dashboard
                        </x-nav-link>
                        <x-nav-link :href="Route::has('admin.students.index') ? route('admin.students.index') : '#'" :active="request()->routeIs('admin.students.*')">
                            Siswa
                        </x-nav-link>
                        <x-nav-link :href="Route::has('admin.teachers.index') ? route('admin.teachers.index') : '#'" :active="request()->routeIs('admin.teachers.*')">
                            Guru
                        </x-nav-link>
                        <x-nav-link :href="Route::has('admin.classes.index') ? route('admin.classes.index') : '#'" :active="request()->routeIs('admin.classes.*')">
                            Kelas
                        </x-nav-link>
                        <x-nav-link :href="Route::has('admin.subjects.index') ? route('admin.subjects.index') : '#'" :active="request()->routeIs('admin.subjects.*')">
                            Mapel
                        </x-nav-link>
                        <x-nav-link :href="Route::has('admin.academic-years.index') ? route('admin.academic-years.index') : '#'" :active="request()->routeIs('admin.academic-years.*')">
                            Tahun Ajaran
                        </x-nav-link>
                        <x-nav-link :href="Route::has('admin.enrollments.index') ? route('admin.enrollments.index') : '#'" :active="request()->routeIs('admin.enrollments.*')">
                            Penempatan
                        </x-nav-link>
                        <x-nav-link :href="Route::has('admin.class-subjects.index') ? route('admin.class-subjects.index') : '#'" :active="request()->routeIs('admin.class-subjects.*')">
                            Penugasan Guru
                        </x-nav-link>
                        <x-nav-link :href="Route::has('admin.schedules.index') ? route('admin.schedules.index') : '#'" :active="request()->routeIs('admin.schedules.*')">
                            Jadwal
                        </x-nav-link>
                        <x-nav-link :href="Route::has('admin.reports.index') ? route('admin.reports.index') : '#'" :active="request()->routeIs('admin.reports.*')">
                            Laporan
                        </x-nav-link>
                        <x-nav-link :href="Route::has('admin.activity-logs.index') ? route('admin.activity-logs.index') : '#'" :active="request()->routeIs('admin.activity-logs.*')">
                            Log Audit
                        </x-nav-link>
                    @elseif(Auth::user()->isTeacher())
                        <x-nav-link :href="route('teacher.dashboard')" :active="request()->routeIs('teacher.dashboard')">
                            Dashboard
                        </x-nav-link>
                        <x-nav-link :href="Route::has('teacher.attendances.index') ? route('teacher.attendances.index') : '#'" :active="request()->routeIs('teacher.attendances.*')">
                            Absensi
                        </x-nav-link>
                        <x-nav-link :href="Route::has('teacher.grade-components.index') ? route('teacher.grade-components.index') : '#'" :active="request()->routeIs('teacher.grade-components.*') || request()->routeIs('teacher.grades.*')">
                            Nilai
                        </x-nav-link>
                        <x-nav-link :href="Route::has('teacher.materials.index') ? route('teacher.materials.index') : '#'" :active="request()->routeIs('teacher.materials.*')">
                            Materi
                        </x-nav-link>
                        <x-nav-link :href="Route::has('teacher.assignments.index') ? route('teacher.assignments.index') : '#'" :active="request()->routeIs('teacher.assignments.*')">
                            Tugas
                        </x-nav-link>
                    @elseif(Auth::user()->isStudent())
                        <x-nav-link :href="route('student.dashboard')" :active="request()->routeIs('student.dashboard')">
                            Dashboard
                        </x-nav-link>
                        <x-nav-link :href="Route::has('student.schedules.index') ? route('student.schedules.index') : '#'" :active="request()->routeIs('student.schedules.*')">
                            Jadwal
                        </x-nav-link>
                        <x-nav-link :href="Route::has('student.materials.index') ? route('student.materials.index') : '#'" :active="request()->routeIs('student.materials.*')">
                            Materi
                        </x-nav-link>
                        <x-nav-link :href="Route::has('student.assignments.index') ? route('student.assignments.index') : '#'" :active="request()->routeIs('student.assignments.*')">
                            Tugas
                        </x-nav-link>
                        <x-nav-link :href="Route::has('student.grades.index') ? route('student.grades.index') : '#'" :active="request()->routeIs('student.grades.*')">
                            Nilai
                        </x-nav-link>
                        <x-nav-link :href="Route::has('student.attendances.index') ? route('student.attendances.index') : '#'" :active="request()->routeIs('student.attendances.*')">
                            Absensi
                        </x-nav-link>
                    @endif
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <!-- Role Tag -->
                <span class="me-3 px-2.5 py-0.5 rounded text-xs font-semibold {{ Auth::user()->isAdmin() ? 'bg-purple-100 text-purple-800' : (Auth::user()->isTeacher() ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800') }}">
                    {{ Auth::user()->role?->display_name ?? 'Pengguna' }}
                </span>

                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:text-gray-900 focus:outline-none transition ease-in-out duration-150">
                            <div>{{ Auth::user()->name }}</div>

                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            Profil Saya
                        </x-dropdown-link>

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();" class="text-red-600 hover:text-red-800">
                                Keluar
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center lg:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden lg:hidden">
        <div class="pt-2 pb-3 space-y-1">
            @if(Auth::user()->isAdmin())
                <x-responsive-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')">
                    Dashboard
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="Route::has('admin.students.index') ? route('admin.students.index') : '#'" :active="request()->routeIs('admin.students.*')">
                    Data Siswa
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="Route::has('admin.teachers.index') ? route('admin.teachers.index') : '#'" :active="request()->routeIs('admin.teachers.*')">
                    Data Guru
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="Route::has('admin.classes.index') ? route('admin.classes.index') : '#'" :active="request()->routeIs('admin.classes.*')">
                    Data Kelas
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="Route::has('admin.subjects.index') ? route('admin.subjects.index') : '#'" :active="request()->routeIs('admin.subjects.*')">
                    Mata Pelajaran
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="Route::has('admin.academic-years.index') ? route('admin.academic-years.index') : '#'" :active="request()->routeIs('admin.academic-years.*')">
                    Tahun Ajaran
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="Route::has('admin.enrollments.index') ? route('admin.enrollments.index') : '#'" :active="request()->routeIs('admin.enrollments.*')">
                    Penempatan Siswa
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="Route::has('admin.class-subjects.index') ? route('admin.class-subjects.index') : '#'" :active="request()->routeIs('admin.class-subjects.*')">
                    Penugasan Guru
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="Route::has('admin.schedules.index') ? route('admin.schedules.index') : '#'" :active="request()->routeIs('admin.schedules.*')">
                    Jadwal Pelajaran
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="Route::has('admin.reports.index') ? route('admin.reports.index') : '#'" :active="request()->routeIs('admin.reports.*')">
                    Laporan
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="Route::has('admin.activity-logs.index') ? route('admin.activity-logs.index') : '#'" :active="request()->routeIs('admin.activity-logs.*')">
                    Log Audit Aktivitas
                </x-responsive-nav-link>
            @elseif(Auth::user()->isTeacher())
                <x-responsive-nav-link :href="route('teacher.dashboard')" :active="request()->routeIs('teacher.dashboard')">
                    Dashboard
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="Route::has('teacher.attendances.index') ? route('teacher.attendances.index') : '#'" :active="request()->routeIs('teacher.attendances.*')">
                    Absensi
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="Route::has('teacher.grade-components.index') ? route('teacher.grade-components.index') : '#'" :active="request()->routeIs('teacher.grade-components.*') || request()->routeIs('teacher.grades.*')">
                    Nilai
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="Route::has('teacher.materials.index') ? route('teacher.materials.index') : '#'" :active="request()->routeIs('teacher.materials.*')">
                    Materi
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="Route::has('teacher.assignments.index') ? route('teacher.assignments.index') : '#'" :active="request()->routeIs('teacher.assignments.*')">
                    Tugas
                </x-responsive-nav-link>
            @elseif(Auth::user()->isStudent())
                <x-responsive-nav-link :href="route('student.dashboard')" :active="request()->routeIs('student.dashboard')">
                    Dashboard
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="Route::has('student.schedules.index') ? route('student.schedules.index') : '#'" :active="request()->routeIs('student.schedules.*')">
                    Jadwal
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="Route::has('student.materials.index') ? route('student.materials.index') : '#'" :active="request()->routeIs('student.materials.*')">
                    Materi
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="Route::has('student.assignments.index') ? route('student.assignments.index') : '#'" :active="request()->routeIs('student.assignments.*')">
                    Tugas
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="Route::has('student.grades.index') ? route('student.grades.index') : '#'" :active="request()->routeIs('student.grades.*')">
                    Nilai
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="Route::has('student.attendances.index') ? route('student.attendances.index') : '#'" :active="request()->routeIs('student.attendances.*')">
                    Absensi
                </x-responsive-nav-link>
            @endif
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200 bg-gray-50">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->username }} ({{ Auth::user()->role?->display_name }})</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    Profil Saya
                </x-responsive-nav-link>

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();" class="text-red-600">
                        Keluar
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
