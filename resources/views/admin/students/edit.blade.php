<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Ubah Data Siswa') }}
            </h2>
            <a href="{{ route('admin.students.index') }}" class="text-sm text-gray-600 hover:text-gray-900 font-medium">
                &larr; Kembali ke Daftar
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <form method="POST" action="{{ route('admin.students.update', $student->id) }}" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <h3 class="text-base font-bold text-gray-800 border-b pb-2">Informasi Akun Login</h3>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <x-input-label for="name" value="Nama Lengkap Siswa" />
                            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $student->user->name)" required autofocus />
                            <x-input-error class="mt-2" :messages="$errors->get('name')" />
                        </div>

                        <div>
                            <x-input-label for="username" value="Nama Pengguna (Username)" />
                            <x-text-input id="username" name="username" type="text" class="mt-1 block w-full" :value="old('username', $student->user->username)" required />
                            <x-input-error class="mt-2" :messages="$errors->get('username')" />
                        </div>

                        <div>
                            <x-input-label for="email" value="Email (Opsional)" />
                            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $student->user->email)" />
                            <x-input-error class="mt-2" :messages="$errors->get('email')" />
                        </div>

                        <div>
                            <x-input-label for="password" value="Kata Sandi Baru (Kosongkan jika tidak diubah)" />
                            <x-text-input id="password" name="password" type="password" class="mt-1 block w-full" placeholder="Kosongkan jika tetap" />
                            <x-input-error class="mt-2" :messages="$errors->get('password')" />
                        </div>
                    </div>

                    <div class="flex items-center">
                        <input id="is_active" name="is_active" type="checkbox" value="1" {{ old('is_active', $student->user->is_active) ? 'checked' : '' }} class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500" />
                        <label for="is_active" class="ms-2 text-sm text-gray-700 font-medium">Status akun aktif</label>
                    </div>

                    <h3 class="text-base font-bold text-gray-800 border-b pb-2 pt-4">Data Pokok Siswa</h3>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <x-input-label for="nis" value="NIS (Nomor Induk Siswa)" />
                            <x-text-input id="nis" name="nis" type="text" class="mt-1 block w-full" :value="old('nis', $student->nis)" required />
                            <x-input-error class="mt-2" :messages="$errors->get('nis')" />
                        </div>

                        <div>
                            <x-input-label for="gender" value="Jenis Kelamin" />
                            <select id="gender" name="gender" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm" required>
                                <option value="male" {{ old('gender', $student->gender) == 'male' ? 'selected' : '' }}>Laki-laki</option>
                                <option value="female" {{ old('gender', $student->gender) == 'female' ? 'selected' : '' }}>Perempuan</option>
                            </select>
                            <x-input-error class="mt-2" :messages="$errors->get('gender')" />
                        </div>

                        <div>
                            <x-input-label for="birth_date" value="Tanggal Lahir" />
                            <x-text-input id="birth_date" name="birth_date" type="date" class="mt-1 block w-full" :value="old('birth_date', $student->birth_date?->format('Y-m-d'))" />
                            <x-input-error class="mt-2" :messages="$errors->get('birth_date')" />
                        </div>

                        <div>
                            <x-input-label for="phone" value="Nomor HP Siswa / Orang Tua" />
                            <x-text-input id="phone" name="phone" type="text" class="mt-1 block w-full" :value="old('phone', $student->phone)" />
                            <x-input-error class="mt-2" :messages="$errors->get('phone')" />
                        </div>

                        <div class="sm:col-span-2">
                            <x-input-label for="address" value="Alamat Tempat Tinggal" />
                            <x-text-input id="address" name="address" type="text" class="mt-1 block w-full" :value="old('address', $student->address)" />
                            <x-input-error class="mt-2" :messages="$errors->get('address')" />
                        </div>
                    </div>

                    <div class="flex items-center justify-end space-x-3 pt-4 border-t">
                        <a href="{{ route('admin.students.index') }}" class="px-4 py-2 border rounded-lg text-sm text-gray-700 hover:bg-gray-50">
                            Batal
                        </a>
                        <x-primary-button class="bg-blue-600 hover:bg-blue-700">
                            Perbarui Data Siswa
                        </x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
