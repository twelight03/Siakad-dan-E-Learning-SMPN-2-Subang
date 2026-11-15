<x-guest-layout>
    <div class="min-h-screen flex flex-col justify-center items-center p-4 sm:p-6 bg-slate-100">
        <div class="w-full max-w-md bg-white p-6 sm:p-8 rounded-2xl shadow-sm border border-slate-200">
            <div class="text-center mb-6">
                <a href="/">
                    <img src="{{ asset('images/logo-smpn2.png') }}" alt="Logo SMP Negeri 2 Subang" class="w-16 h-16 mx-auto object-contain mb-2">
                </a>
                <h2 class="text-xl font-black text-slate-900">Perbarui Kata Sandi</h2>
                <p class="text-xs text-slate-500 mt-1">SIAKAD & E-Learning SMPN 2 Subang</p>
            </div>

            <form method="POST" action="{{ route('password.store') }}" class="space-y-4">
                @csrf

                <!-- Password Reset Token -->
                <input type="hidden" name="token" value="{{ $request->route('token') }}">

                <!-- Email Address -->
                <div>
                    <x-input-label for="email" value="Alamat Email" />
                    <x-text-input id="email" class="block mt-1 w-full text-sm" type="email" name="email" :value="old('email', $request->email)" required autofocus autocomplete="username" />
                    <x-input-error :messages="$errors->get('email')" class="mt-1" />
                </div>

                <!-- Password -->
                <div>
                    <x-input-label for="password" value="Kata Sandi Baru" />
                    <x-text-input id="password" class="block mt-1 w-full text-sm" type="password" name="password" required autocomplete="new-password" />
                    <x-input-error :messages="$errors->get('password')" class="mt-1" />
                </div>

                <!-- Confirm Password -->
                <div>
                    <x-input-label for="password_confirmation" value="Konfirmasi Kata Sandi Baru" />
                    <x-text-input id="password_confirmation" class="block mt-1 w-full text-sm"
                                        type="password"
                                        name="password_confirmation" required autocomplete="new-password" />
                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1" />
                </div>

                <div class="pt-2">
                    <x-primary-button class="w-full justify-center bg-blue-700 hover:bg-blue-800 text-white font-bold py-2.5 shadow-sm text-sm">
                        Simpan Kata Sandi Baru
                    </x-primary-button>
                </div>
            </form>
        </div>
    </div>
</x-guest-layout>
