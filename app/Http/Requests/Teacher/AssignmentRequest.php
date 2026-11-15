<?php

namespace App\Http\Requests\Teacher;

use App\Models\ClassSubject;
use Illuminate\Foundation\Http\FormRequest;

class AssignmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        $teacher = $this->user()?->teacher;
        if (! $teacher) {
            return false;
        }

        $classSubjectId = $this->input('class_subject_id') ?? $this->route('assignment')?->class_subject_id;
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
            'class_subject_id' => $this->isMethod('POST') ? ['required', 'exists:class_subjects,id'] : ['sometimes', 'exists:class_subjects,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'deadline' => ['required', 'date'],
            'attachment' => ['nullable', 'file', 'mimes:pdf,doc,docx,ppt,pptx,zip,jpg,jpeg,png', 'max:10240'],
        ];
    }

    public function messages(): array
    {
        return [
            'class_subject_id.required' => 'Mata pelajaran dan kelas wajib dipilih.',
            'title.required' => 'Judul tugas wajib diisi.',
            'description.required' => 'Instruksi tugas wajib diisi.',
            'deadline.required' => 'Batas waktu pengumpulan tugas wajib ditentukan.',
            'deadline.date' => 'Format batas waktu tidak valid.',
            'attachment.mimes' => 'Lampiran harus berformat: PDF, DOC, DOCX, PPT, PPTX, ZIP, JPG, atau PNG.',
            'attachment.max' => 'Ukuran berkas lampiran maksimal 10 MB.',
        ];
    }
}
