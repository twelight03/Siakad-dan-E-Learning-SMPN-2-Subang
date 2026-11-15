<x-guest-layout>
    <div class="mb-6 text-center">
        <h2 class="text-2xl font-bold text-gray-800">SIAKAD SMPN 2 Subang</h2>
        <p class="text-sm text-gray-600 mt-1">Sistem Informasi Akademik & E-Learning</p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Username or Email -->
        <div>
            <x-input-label for="username" value="Nama Pengguna atau Email" />
            <x-text-input id="username" class="block mt-1 w-full" type="text" name="username" :value="old('username')" required autofocus autocomplete="username" placeholder="Masukkan username atau email" />
            <x-input-error :messages="$errors->get('username')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" value="Kata Sandi" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="current-password"
                            placeholder="Masukkan kata sandi" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="block mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="remember">
                <span class="ms-2 text-sm text-gray-600">Ingat Saya</span>
            </label>
        </div>

        <div class="flex items-center justify-between mt-6">
            @if (Route::has('password.request'))
                <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('password.request') }}">
                    Lupa kata sandi?
                </a>
            @else
                <div></div>
            @endif

            <x-primary-button class="ms-3 bg-blue-600 hover:bg-blue-700">
                Masuk
            </x-primary-button>
        </div>

        <div class="mt-6 pt-4 border-t border-slate-200 text-center">
            <p class="text-xs text-gray-600">
                Belum memiliki akun siswa?
                <a href="{{ route('register') }}" class="font-semibold text-blue-600 hover:text-blue-800 underline ml-1">
                    Daftar Akun Siswa Baru
                </a>
            </p>
        </div>
    </form>
</x-guest-layout>
