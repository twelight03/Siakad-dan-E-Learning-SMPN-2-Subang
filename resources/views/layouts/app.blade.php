<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-50">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'SIAKAD SMPN 2 Subang') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body 
        class="h-full font-sans antialiased text-slate-800 bg-slate-50 selection:bg-blue-600 selection:text-white overflow-hidden" 
        x-data="{ 
            sidebarOpen: localStorage.getItem('siakad_sidebar') !== 'closed',
            isMobile: window.innerWidth < 1024,
            toggleSidebar() {
                this.sidebarOpen = !this.sidebarOpen;
                if (window.innerWidth >= 1024) {
                    localStorage.setItem('siakad_sidebar', this.sidebarOpen ? 'open' : 'closed');
                }
            }
        }"
        @resize.window="isMobile = window.innerWidth < 1024">
        
        <div class="flex h-screen w-screen overflow-hidden bg-slate-50">
            <!-- Sidebar Navigation (Collapsible / Toggleable) -->
            @include('layouts.sidebar')

            <!-- Main Content Area -->
            <div class="flex flex-col flex-1 w-0 min-w-0 h-screen overflow-hidden bg-slate-50 transition-all duration-300">
                <!-- Top Navbar with Toggle Button -->
                @include('layouts.topbar')

                <!-- Optional Page Heading -->
                @isset($header)
                    <div class="bg-white border-b border-slate-200 py-3.5 px-4 sm:px-6 flex-shrink-0">
                        {{ $header }}
                    </div>
                @endisset

                <!-- Flash Notification Messages -->
                @if (session('success') || session('error'))
                    <div class="w-full px-4 sm:px-6 pt-3 flex-shrink-0">
                        @if (session('success'))
                            <div class="p-3 mb-1 text-sm text-blue-900 rounded-lg bg-blue-50 border border-blue-200 flex items-center shadow-xs" role="alert">
                                <svg class="w-5 h-5 me-2.5 text-blue-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                <span>{{ session('success') }}</span>
                            </div>
                        @endif

                        @if (session('error'))
                            <div class="p-3 mb-1 text-sm text-red-800 rounded-lg bg-red-50 border border-red-200 flex items-center shadow-xs" role="alert">
                                <svg class="w-5 h-5 me-2.5 text-red-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                </svg>
                                <span>{{ session('error') }}</span>
                            </div>
                        @endif
                    </div>
                @endif

                <!-- Main Content Body: fills available viewport, child views can fill space & scroll independently -->
                <main class="flex-1 min-h-0 flex flex-col overflow-y-auto">
                    {{ $slot }}
                </main>
            </div>
        </div>
        @stack('scripts')
    </body>
</html>
