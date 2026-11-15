<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TeacherRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    public function rules(): array
    {
        $teacher = $this->route('teacher');
        $userId = $teacher?->user_id;
        $teacherId = $teacher?->id;

        $isCreate = $this->isMethod('POST');

        return [
            'name' => ['required', 'string', 'max:100'],
            'username' => ['required', 'string', 'max:50', Rule::unique('users', 'username')->ignore($userId)],
            'email' => ['nullable', 'email', 'max:100', Rule::unique('users', 'email')->ignore($userId)],
            'password' => [$isCreate ? 'required' : 'nullable', 'string', 'min:6'],
            'is_active' => ['nullable', 'boolean'],
            'nip' => ['nullable', 'string', 'max:30', Rule::unique('teachers', 'nip')->ignore($teacherId)],
            'gender' => ['nullable', 'in:male,female'],
            'phone' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string', 'max:500'],
            'max_hours_per_week' => ['nullable', 'integer', 'min:1', 'max:50'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Nama lengkap guru wajib diisi.',
            'username.required' => 'Nama pengguna (username) wajib diisi.',
            'username.unique' => 'Nama pengguna sudah digunakan akun lain.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah terdaftar.',
            'password.required' => 'Kata sandi akun wajib diisi.',
            'password.min' => 'Kata sandi minimal 6 karakter.',
            'nip.unique' => 'NIP sudah terdaftar.',
            'gender.in' => 'Jenis kelamin harus Laki-laki (male) atau Perempuan (female).',
        ];
    }
}
