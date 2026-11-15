<x-guest-layout>
    <div class="mb-5 text-center">
        <h2 class="text-xl sm:text-2xl font-black text-slate-800 tracking-tight">Pendaftaran Akun Siswa</h2>
        <p class="text-xs sm:text-sm text-slate-600 mt-1">SIAKAD & E-Learning SMP Negeri 2 Subang</p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-4">
        @csrf

        <!-- Name -->
        <div>
            <x-input-label for="name" value="Nama Lengkap Siswa" />
            <x-text-input id="name" class="block mt-1 w-full text-sm" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" placeholder="Contoh: Ahmad Fauzan" />
            <x-input-error :messages="$errors->get('name')" class="mt-1" />
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <!-- Username -->
            <div>
                <x-input-label for="username" value="Username" />
                <x-text-input id="username" class="block mt-1 w-full text-sm" type="text" name="username" :value="old('username')" required autocomplete="username" placeholder="ahmad123" />
                <x-input-error :messages="$errors->get('username')" class="mt-1" />
            </div>

            <!-- NIS -->
            <div>
                <x-input-label for="nis" value="Nomor Induk Siswa (NIS)" />
                <x-text-input id="nis" class="block mt-1 w-full text-sm" type="text" name="nis" :value="old('nis')" required placeholder="Contoh: 202607005" />
                <x-input-error :messages="$errors->get('nis')" class="mt-1" />
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <!-- Gender -->
            <div>
                <x-input-label for="gender" value="Jenis Kelamin" />
                <select id="gender" name="gender" class="block mt-1 w-full text-sm border-slate-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm" required>
                    <option value="" disabled {{ old('gender') ? '' : 'selected' }}>Pilih jenis kelamin</option>
                    <option value="male" {{ old('gender') === 'male' ? 'selected' : '' }}>Laki-laki</option>
                    <option value="female" {{ old('gender') === 'female' ? 'selected' : '' }}>Perempuan</option>
                </select>
                <x-input-error :messages="$errors->get('gender')" class="mt-1" />
            </div>

            <!-- Email Address -->
            <div>
                <x-input-label for="email" value="Alamat Email" />
                <x-text-input id="email" class="block mt-1 w-full text-sm" type="email" name="email" :value="old('email')" required autocomplete="email" placeholder="nama@email.com" />
                <x-input-error :messages="$errors->get('email')" class="mt-1" />
            </div>
        </div>

        <!-- Password -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <div>
                <x-input-label for="password" value="Kata Sandi (Min. 8 Karakter)" />
                <x-text-input id="password" class="block mt-1 w-full text-sm"
                                type="password"
                                name="password"
                                required autocomplete="new-password"
                                placeholder="Minimal 8 karakter" />
                <x-input-error :messages="$errors->get('password')" class="mt-1" />
            </div>

            <!-- Confirm Password -->
            <div>
                <x-input-label for="password_confirmation" value="Konfirmasi Kata Sandi" />
                <x-text-input id="password_confirmation" class="block mt-1 w-full text-sm"
                                type="password"
                                name="password_confirmation" required autocomplete="new-password"
                                placeholder="Ulangi kata sandi" />
                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1" />
            </div>
        </div>

        <div class="pt-2">
            <x-primary-button class="w-full justify-center bg-blue-600 hover:bg-blue-700 text-white font-bold py-2.5 shadow-sm text-sm">
                Daftar Akun Siswa Baru
            </x-primary-button>
        </div>

        <div class="text-center pt-2 border-t border-slate-100">
            <p class="text-xs text-slate-600">
                Sudah memiliki akun?
                <a class="text-blue-600 hover:text-blue-800 font-semibold underline underline-offset-2 ml-1" href="{{ route('login') }}">
                    Masuk ke Portal
                </a>
            </p>
        </div>
    </form>
</x-guest-layout>
