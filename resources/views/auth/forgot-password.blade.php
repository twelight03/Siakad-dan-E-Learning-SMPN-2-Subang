<x-guest-layout>
    <div class="min-h-screen flex flex-col justify-center items-center p-4 sm:p-6 bg-slate-100">
        <div class="w-full max-w-md bg-white p-6 sm:p-8 rounded-2xl shadow-sm border border-slate-200">
            <div class="text-center mb-6">
                <a href="/">
                    <img src="{{ asset('images/logo-smpn2.png') }}" alt="Logo SMP Negeri 2 Subang" class="w-16 h-16 mx-auto object-contain mb-2">
                </a>
                <h2 class="text-xl font-black text-slate-900">Reset Kata Sandi</h2>
                <p class="text-xs text-slate-500 mt-1">SIAKAD & E-Learning SMPN 2 Subang</p>
            </div>

            <div class="mb-4 text-xs sm:text-sm text-slate-600 leading-relaxed">
                Lupa kata sandi akun Anda? Masukkan alamat email terdaftar dan kami akan mengirimkan tautan untuk mengatur ulang kata sandi baru.
            </div>

            <!-- Session Status -->
            <x-auth-session-status class="mb-4" :status="session('status')" />

            <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
                @csrf

                <!-- Email Address -->
                <div>
                    <x-input-label for="email" value="Alamat Email Terdaftar" />
                    <x-text-input id="email" class="block mt-1 w-full text-sm" type="email" name="email" :value="old('email')" required autofocus />
                    <x-input-error :messages="$errors->get('email')" class="mt-1" />
                </div>

                <div class="pt-2">
                    <x-primary-button class="w-full justify-center bg-blue-700 hover:bg-blue-800 text-white font-bold py-2.5 shadow-sm text-sm">
                        Kirim Tautan Reset Password
                    </x-primary-button>
                </div>

                <div class="text-center pt-2">
                    <a href="{{ route('login') }}" class="text-xs text-blue-600 hover:text-blue-800 font-semibold underline">
                        Kembali ke Halaman Login
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-guest-layout>
