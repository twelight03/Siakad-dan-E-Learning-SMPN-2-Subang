<!-- Mobile Backdrop -->
<div 
    x-show="sidebarOpen && isMobile" 
    x-transition:enter="transition-opacity ease-linear duration-200"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition-opacity ease-linear duration-200"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    class="fixed inset-0 z-40 bg-slate-900/40 backdrop-blur-sm lg:hidden"
    @click="sidebarOpen = false"
    style="display: none;">
</div>

<!-- Sidebar Component (Collapsible on Desktop & Mobile) -->
<aside 
    :class="sidebarOpen ? 'translate-x-0 lg:ml-0' : '-translate-x-full lg:-ml-64'"
    class="fixed inset-y-0 left-0 z-50 w-64 bg-white border-r border-slate-200 flex flex-col justify-between transition-all duration-300 ease-in-out lg:static flex-shrink-0">
    
    <!-- Top Brand & Navigation -->
    <div class="flex-1 flex flex-col min-h-0 overflow-y-auto">
        <!-- Brand Header -->
        <div class="h-16 px-4 border-b border-slate-200 flex items-center justify-between bg-white sticky top-0 z-10">
            <a href="{{ route('dashboard') }}" class="flex items-center space-x-2.5">
                <img src="{{ asset('images/logo-smpn2.png') }}" alt="Logo SMPN 2 Subang" class="w-9 h-9 object-contain flex-shrink-0">
                <div>
                    <span class="block text-base font-extrabold text-slate-900 leading-tight">SIAKAD</span>
                    <span class="block text-[10px] font-bold text-blue-600 tracking-wider">SMPN 2 SUBANG</span>
                </div>
            </a>
            <!-- Collapse Button (Both Mobile & Desktop) -->
            <button @click="toggleSidebar()" class="p-1.5 rounded-lg text-slate-400 hover:text-blue-600 hover:bg-slate-100 transition focus:outline-none" title="Sembunyikan Sidebar">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7" />
                </svg>
            </button>
        </div>

        <!-- Menu Navigation List -->
        <nav class="p-3 space-y-1" @click="if(isMobile) sidebarOpen = false">
            @if(Auth::user()->isAdmin())
                <div class="px-3 pt-2 pb-1 text-[11px] font-bold tracking-wider text-slate-400 uppercase">
                    Menu Utama
                </div>
                <a href="{{ route('admin.dashboard') }}" class="flex items-center px-3 py-2 text-sm rounded-lg transition-colors {{ request()->routeIs('admin.dashboard') ? 'bg-blue-50 text-blue-700 font-bold border-l-4 border-blue-600 rounded-l-none' : 'text-slate-600 hover:text-blue-600 hover:bg-slate-50 font-medium' }}">
                    <svg class="w-5 h-5 me-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                    Dashboard
                </a>

                <div class="px-3 pt-3 pb-1 text-[11px] font-bold tracking-wider text-slate-400 uppercase">
                    Master Data
                </div>
                <a href="{{ route('admin.students.index') }}" class="flex items-center px-3 py-2 text-sm rounded-lg transition-colors {{ request()->routeIs('admin.students.*') ? 'bg-blue-50 text-blue-700 font-bold border-l-4 border-blue-600 rounded-l-none' : 'text-slate-600 hover:text-blue-600 hover:bg-slate-50 font-medium' }}">
                    <svg class="w-5 h-5 me-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                    Data Siswa
                </a>
                <a href="{{ route('admin.teachers.index') }}" class="flex items-center px-3 py-2 text-sm rounded-lg transition-colors {{ request()->routeIs('admin.teachers.*') ? 'bg-blue-50 text-blue-700 font-bold border-l-4 border-blue-600 rounded-l-none' : 'text-slate-600 hover:text-blue-600 hover:bg-slate-50 font-medium' }}">
                    <svg class="w-5 h-5 me-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                    Data Guru
                </a>
                <a href="{{ route('admin.classes.index') }}" class="flex items-center px-3 py-2 text-sm rounded-lg transition-colors {{ request()->routeIs('admin.classes.*') ? 'bg-blue-50 text-blue-700 font-bold border-l-4 border-blue-600 rounded-l-none' : 'text-slate-600 hover:text-blue-600 hover:bg-slate-50 font-medium' }}">
                    <svg class="w-5 h-5 me-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                    Data Kelas
                </a>
                <a href="{{ route('admin.subjects.index') }}" class="flex items-center px-3 py-2 text-sm rounded-lg transition-colors {{ request()->routeIs('admin.subjects.*') ? 'bg-blue-50 text-blue-700 font-bold border-l-4 border-blue-600 rounded-l-none' : 'text-slate-600 hover:text-blue-600 hover:bg-slate-50 font-medium' }}">
                    <svg class="w-5 h-5 me-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                    Mata Pelajaran
                </a>
                <a href="{{ route('admin.academic-years.index') }}" class="flex items-center px-3 py-2 text-sm rounded-lg transition-colors {{ request()->routeIs('admin.academic-years.*') ? 'bg-blue-50 text-blue-700 font-bold border-l-4 border-blue-600 rounded-l-none' : 'text-slate-600 hover:text-blue-600 hover:bg-slate-50 font-medium' }}">
                    <svg class="w-5 h-5 me-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    Tahun Ajaran
                </a>

                <div class="px-3 pt-3 pb-1 text-[11px] font-bold tracking-wider text-slate-400 uppercase">
                    Penugasan & Jadwal
                </div>
                <a href="{{ route('admin.enrollments.index') }}" class="flex items-center px-3 py-2 text-sm rounded-lg transition-colors {{ request()->routeIs('admin.enrollments.*') ? 'bg-blue-50 text-blue-700 font-bold border-l-4 border-blue-600 rounded-l-none' : 'text-slate-600 hover:text-blue-600 hover:bg-slate-50 font-medium' }}">
                    <svg class="w-5 h-5 me-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                    </svg>
                    Penempatan Siswa
                </a>
                <a href="{{ route('admin.class-subjects.index') }}" class="flex items-center px-3 py-2 text-sm rounded-lg transition-colors {{ request()->routeIs('admin.class-subjects.*') ? 'bg-blue-50 text-blue-700 font-bold border-l-4 border-blue-600 rounded-l-none' : 'text-slate-600 hover:text-blue-600 hover:bg-slate-50 font-medium' }}">
                    <svg class="w-5 h-5 me-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    Penugasan Guru
                </a>
                <a href="{{ route('admin.schedules.index') }}" class="flex items-center px-3 py-2 text-sm rounded-lg transition-colors {{ request()->routeIs('admin.schedules.*') ? 'bg-blue-50 text-blue-700 font-bold border-l-4 border-blue-600 rounded-l-none' : 'text-slate-600 hover:text-blue-600 hover:bg-slate-50 font-medium' }}">
                    <svg class="w-5 h-5 me-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Jadwal Pelajaran
                </a>

                <div class="px-3 pt-3 pb-1 text-[11px] font-bold tracking-wider text-slate-400 uppercase">
                    Laporan & Keamanan
                </div>
                <a href="{{ route('admin.reports.index') }}" class="flex items-center px-3 py-2 text-sm rounded-lg transition-colors {{ request()->routeIs('admin.reports.*') ? 'bg-blue-50 text-blue-700 font-bold border-l-4 border-blue-600 rounded-l-none' : 'text-slate-600 hover:text-blue-600 hover:bg-slate-50 font-medium' }}">
                    <svg class="w-5 h-5 me-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Pusat Laporan
                </a>
                <a href="{{ route('admin.activity-logs.index') }}" class="flex items-center px-3 py-2 text-sm rounded-lg transition-colors {{ request()->routeIs('admin.activity-logs.*') ? 'bg-blue-50 text-blue-700 font-bold border-l-4 border-blue-600 rounded-l-none' : 'text-slate-600 hover:text-blue-600 hover:bg-slate-50 font-medium' }}">
                    <svg class="w-5 h-5 me-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                    Log Audit Aktivitas
                </a>

            @elseif(Auth::user()->isTeacher())
                <div class="px-3 pt-2 pb-1 text-[11px] font-bold tracking-wider text-slate-400 uppercase">
                    Menu Guru
                </div>
                <a href="{{ route('teacher.dashboard') }}" class="flex items-center px-3 py-2 text-sm rounded-lg transition-colors {{ request()->routeIs('teacher.dashboard') ? 'bg-blue-50 text-blue-700 font-bold border-l-4 border-blue-600 rounded-l-none' : 'text-slate-600 hover:text-blue-600 hover:bg-slate-50 font-medium' }}">
                    <svg class="w-5 h-5 me-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                    Dashboard
                </a>
                <a href="{{ route('teacher.attendances.index') }}" class="flex items-center px-3 py-2 text-sm rounded-lg transition-colors {{ request()->routeIs('teacher.attendances.*') ? 'bg-blue-50 text-blue-700 font-bold border-l-4 border-blue-600 rounded-l-none' : 'text-slate-600 hover:text-blue-600 hover:bg-slate-50 font-medium' }}">
                    <svg class="w-5 h-5 me-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                    </svg>
                    Absensi Siswa
                </a>
                <a href="{{ route('teacher.grade-components.index') }}" class="flex items-center px-3 py-2 text-sm rounded-lg transition-colors {{ request()->routeIs('teacher.grade-components.*') || request()->routeIs('teacher.grades.*') ? 'bg-blue-50 text-blue-700 font-bold border-l-4 border-blue-600 rounded-l-none' : 'text-slate-600 hover:text-blue-600 hover:bg-slate-50 font-medium' }}">
                    <svg class="w-5 h-5 me-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"/>
                    </svg>
                    Nilai & Komponen
                </a>

                <div class="px-3 pt-3 pb-1 text-[11px] font-bold tracking-wider text-slate-400 uppercase">
                    E-Learning
                </div>
                <a href="{{ route('teacher.materials.index') }}" class="flex items-center px-3 py-2 text-sm rounded-lg transition-colors {{ request()->routeIs('teacher.materials.*') ? 'bg-blue-50 text-blue-700 font-bold border-l-4 border-blue-600 rounded-l-none' : 'text-slate-600 hover:text-blue-600 hover:bg-slate-50 font-medium' }}">
                    <svg class="w-5 h-5 me-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                    Materi Pembelajaran
                </a>
                <a href="{{ route('teacher.assignments.index') }}" class="flex items-center px-3 py-2 text-sm rounded-lg transition-colors {{ request()->routeIs('teacher.assignments.*') ? 'bg-blue-50 text-blue-700 font-bold border-l-4 border-blue-600 rounded-l-none' : 'text-slate-600 hover:text-blue-600 hover:bg-slate-50 font-medium' }}">
                    <svg class="w-5 h-5 me-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    Tugas E-Learning
                </a>

            @elseif(Auth::user()->isStudent())
                <div class="px-3 pt-2 pb-1 text-[11px] font-bold tracking-wider text-slate-400 uppercase">
                    Menu Siswa
                </div>
                <a href="{{ route('student.dashboard') }}" class="flex items-center px-3 py-2 text-sm rounded-lg transition-colors {{ request()->routeIs('student.dashboard') ? 'bg-blue-50 text-blue-700 font-bold border-l-4 border-blue-600 rounded-l-none' : 'text-slate-600 hover:text-blue-600 hover:bg-slate-50 font-medium' }}">
                    <svg class="w-5 h-5 me-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                    Dashboard
                </a>
                <a href="{{ route('student.schedules.index') }}" class="flex items-center px-3 py-2 text-sm rounded-lg transition-colors {{ request()->routeIs('student.schedules.*') ? 'bg-blue-50 text-blue-700 font-bold border-l-4 border-blue-600 rounded-l-none' : 'text-slate-600 hover:text-blue-600 hover:bg-slate-50 font-medium' }}">
                    <svg class="w-5 h-5 me-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Jadwal Pelajaran
                </a>
                <a href="{{ route('student.materials.index') }}" class="flex items-center px-3 py-2 text-sm rounded-lg transition-colors {{ request()->routeIs('student.materials.*') ? 'bg-blue-50 text-blue-700 font-bold border-l-4 border-blue-600 rounded-l-none' : 'text-slate-600 hover:text-blue-600 hover:bg-slate-50 font-medium' }}">
                    <svg class="w-5 h-5 me-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                    Materi Pembelajaran
                </a>
                <a href="{{ route('student.assignments.index') }}" class="flex items-center px-3 py-2 text-sm rounded-lg transition-colors {{ request()->routeIs('student.assignments.*') ? 'bg-blue-50 text-blue-700 font-bold border-l-4 border-blue-600 rounded-l-none' : 'text-slate-600 hover:text-blue-600 hover:bg-slate-50 font-medium' }}">
                    <svg class="w-5 h-5 me-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                    </svg>
                    Tugas E-Learning
                </a>
                <a href="{{ route('student.grades.index') }}" class="flex items-center px-3 py-2 text-sm rounded-lg transition-colors {{ request()->routeIs('student.grades.*') ? 'bg-blue-50 text-blue-700 font-bold border-l-4 border-blue-600 rounded-l-none' : 'text-slate-600 hover:text-blue-600 hover:bg-slate-50 font-medium' }}">
                    <svg class="w-5 h-5 me-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                    Nilai Akademik
                </a>
                <a href="{{ route('student.attendances.index') }}" class="flex items-center px-3 py-2 text-sm rounded-lg transition-colors {{ request()->routeIs('student.attendances.*') ? 'bg-blue-50 text-blue-700 font-bold border-l-4 border-blue-600 rounded-l-none' : 'text-slate-600 hover:text-blue-600 hover:bg-slate-50 font-medium' }}">
                    <svg class="w-5 h-5 me-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    Rekap Kehadiran
                </a>
            @endif
        </nav>
    </div>
</aside>
