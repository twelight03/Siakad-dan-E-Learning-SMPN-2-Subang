<header class="h-16 bg-white border-b border-slate-200 px-4 sm:px-6 flex items-center justify-between sticky top-0 z-30">
    <!-- Left: Mobile Toggle & Brand Indicator -->
    <div class="flex items-center space-x-3">
        <!-- Toggle Button (Desktop & Mobile) -->
        <button 
            @click="toggleSidebar()" 
            class="p-2 rounded-lg text-slate-500 hover:text-blue-600 hover:bg-slate-100 focus:outline-none transition"
            title="Buka / Tutup Sidebar">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7" />
            </svg>
        </button>

        <!-- Current Academic Year Badge -->
        <div class="hidden sm:flex items-center space-x-2">
            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-100">
                <span class="w-2 h-2 rounded-full bg-blue-600 me-1.5 animate-pulse"></span>
                T.A. 2026/2027 Ganjil
            </span>
            <span class="text-xs text-slate-400 font-medium hidden md:inline">SMP Negeri 2 Subang</span>
        </div>
    </div>

    <!-- Right: User Dropdown -->
    <div class="flex items-center space-x-4">
        <!-- Role Tag -->
        <span class="hidden sm:inline-block px-2.5 py-1 rounded-full text-xs font-bold {{ Auth::user()->isAdmin() ? 'bg-purple-100 text-purple-800' : (Auth::user()->isTeacher() ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800') }}">
            {{ Auth::user()->role?->display_name ?? 'Pengguna' }}
        </span>

        <!-- Dropdown Menu -->
        <div class="relative" x-data="{ userMenuOpen: false }">
            <button 
                @click="userMenuOpen = !userMenuOpen" 
                @click.away="userMenuOpen = false"
                class="flex items-center space-x-2.5 p-1.5 rounded-lg hover:bg-slate-50 border border-transparent hover:border-slate-200 transition focus:outline-none">
                <div class="w-8 h-8 rounded-full bg-blue-600 text-white font-black flex items-center justify-center text-xs shadow-sm">
                    {{ substr(Auth::user()->name, 0, 1) }}
                </div>
                <div class="text-left hidden md:block">
                    <p class="text-xs font-bold text-slate-800 leading-tight">{{ Auth::user()->name }}</p>
                    <p class="text-[10px] text-slate-400">{{ Auth::user()->email }}</p>
                </div>
                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </button>

            <!-- Dropdown Body -->
            <div 
                x-show="userMenuOpen" 
                x-transition:enter="transition ease-out duration-100"
                x-transition:enter-start="transform opacity-0 scale-95"
                x-transition:enter-end="transform opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-75"
                x-transition:leave-start="transform opacity-100 scale-100"
                x-transition:leave-end="transform opacity-0 scale-95"
                class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-slate-200 py-1.5 z-50"
                style="display: none;">
                
                <div class="px-4 py-2 border-b border-slate-100 md:hidden">
                    <p class="text-xs font-bold text-slate-800 truncate">{{ Auth::user()->name }}</p>
                    <p class="text-[10px] text-slate-400 truncate">{{ Auth::user()->email }}</p>
                </div>

                <a href="{{ route('profile.edit') }}" class="flex items-center px-4 py-2 text-xs font-medium text-slate-700 hover:bg-blue-50 hover:text-blue-700">
                    <svg class="w-4 h-4 me-2 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    Profil Saya
                </a>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full flex items-center px-4 py-2 text-xs font-medium text-red-600 hover:bg-red-50">
                        <svg class="w-4 h-4 me-2 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                        Keluar
                    </button>
                </form>
            </div>
        </div>
    </div>
</header>
