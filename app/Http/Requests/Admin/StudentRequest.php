<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StudentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    public function rules(): array
    {
        $student = $this->route('student');
        $userId = $student?->user_id;
        $studentId = $student?->id;

        $isCreate = $this->isMethod('POST');

        return [
            'name' => ['required', 'string', 'max:100'],
            'username' => ['required', 'string', 'max:50', Rule::unique('users', 'username')->ignore($userId)],
            'email' => ['nullable', 'email', 'max:100', Rule::unique('users', 'email')->ignore($userId)],
            'password' => [$isCreate ? 'required' : 'nullable', 'string', 'min:6'],
            'is_active' => ['nullable', 'boolean'],
            'nis' => ['required', 'string', 'max:20', Rule::unique('students', 'nis')->ignore($studentId)],
            'gender' => ['required', 'in:male,female'],
            'birth_date' => ['nullable', 'date'],
            'phone' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Nama lengkap siswa wajib diisi.',
            'username.required' => 'Nama pengguna (username) wajib diisi.',
            'username.unique' => 'Nama pengguna sudah terdaftar.',
            'password.required' => 'Kata sandi akun wajib diisi.',
            'password.min' => 'Kata sandi minimal 6 karakter.',
            'nis.required' => 'NIS (Nomor Induk Siswa) wajib diisi.',
            'nis.unique' => 'NIS sudah digunakan oleh siswa lain.',
            'gender.required' => 'Jenis kelamin wajib dipilih.',
            'gender.in' => 'Jenis kelamin harus Laki-laki (male) atau Perempuan (female).',
            'birth_date.date' => 'Format tanggal lahir tidak valid.',
        ];
    }
}
