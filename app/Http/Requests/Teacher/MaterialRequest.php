<?php

namespace App\Http\Requests\Teacher;

use App\Models\ClassSubject;
use Illuminate\Foundation\Http\FormRequest;

class MaterialRequest extends FormRequest
{
    public function authorize(): bool
    {
        $teacher = $this->user()?->teacher;
        if (! $teacher) {
            return false;
        }

        $classSubjectId = $this->input('class_subject_id');
        if ($classSubjectId) {
            return ClassSubject::where('id', $classSubjectId)
                ->where('teacher_id', $teacher->id)
                ->exists();
        }

        return true;
    }

    public function rules(): array
    {
        return [
            'class_subject_id' => ['required', 'exists:class_subjects,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'material_file' => ['required', 'file', 'mimes:pdf,doc,docx,ppt,pptx,zip,jpg,jpeg,png', 'max:10240'],
        ];
    }

    public function messages(): array
    {
        return [
            'class_subject_id.required' => 'Mata pelajaran dan kelas wajib dipilih.',
            'title.required' => 'Judul materi pembelajaran wajib diisi.',
            'title.max' => 'Judul materi maksimal 255 karakter.',
            'material_file.required' => 'File materi pembelajaran wajib diunggah.',
            'material_file.file' => 'Berkas yang diunggah harus berupa file yang valid.',
            'material_file.mimes' => 'Format file harus berupa: PDF, DOC, DOCX, PPT, PPTX, ZIP, JPG, atau PNG.',
            'material_file.max' => 'Ukuran file maksimal adalah 10 MB.',
        ];
    }
}
