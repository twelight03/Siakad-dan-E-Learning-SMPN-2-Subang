<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SchoolClassRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    public function rules(): array
    {
        $classId = $this->route('class')?->id;

        return [
            'name' => ['required', 'string', 'max:50', Rule::unique('classes', 'name')->ignore($classId)],
            'grade_level' => ['required', 'in:7,8,9'],
            'homeroom_teacher_id' => ['nullable', 'exists:teachers,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Nama rombel / kelas wajib diisi.',
            'name.unique' => 'Nama rombel / kelas sudah digunakan.',
            'grade_level.required' => 'Tingkat kelas wajib dipilih.',
            'grade_level.in' => 'Tingkat kelas harus bernilai 7, 8, atau 9.',
            'homeroom_teacher_id.exists' => 'Wali kelas yang dipilih tidak valid.',
        ];
    }
}
