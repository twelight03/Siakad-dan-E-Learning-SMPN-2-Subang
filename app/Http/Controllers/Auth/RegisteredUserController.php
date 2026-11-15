<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\Student;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'alpha_dash', 'min:3', 'max:50', 'unique:users,username'],
            'nis' => ['required', 'string', 'numeric', 'digits_between:5,15', 'unique:students,nis'],
            'gender' => ['required', 'in:male,female'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()->min(8)],
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'username.required' => 'Nama pengguna (username) wajib diisi.',
            'username.unique' => 'Nama pengguna (username) sudah digunakan.',
            'username.alpha_dash' => 'Username hanya boleh berupa huruf, angka, strip, dan garis bawah.',
            'nis.required' => 'Nomor Induk Siswa (NIS) wajib diisi.',
            'nis.unique' => 'Nomor Induk Siswa (NIS) sudah terdaftar.',
            'nis.numeric' => 'NIS harus berupa angka.',
            'gender.required' => 'Jenis kelamin wajib dipilih.',
            'email.required' => 'Alamat email wajib diisi.',
            'email.unique' => 'Alamat email sudah terdaftar.',
            'password.required' => 'Kata sandi wajib diisi.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
        ]);

        $studentRole = Role::where('name', 'student')->firstOrFail();

        $user = DB::transaction(function () use ($request, $studentRole) {
            $user = User::create([
                'role_id' => $studentRole->id,
                'name' => $request->name,
                'username' => strtolower($request->username),
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'is_active' => true,
            ]);

            Student::create([
                'user_id' => $user->id,
                'nis' => $request->nis,
                'gender' => $request->gender,
                'birth_date' => $request->birth_date ?? null,
                'phone' => $request->phone ?? null,
                'address' => $request->address ?? null,
            ]);

            return $user;
        });

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}
