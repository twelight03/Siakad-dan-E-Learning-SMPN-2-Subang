<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Http\Requests\Teacher\MaterialRequest;
use App\Models\ActivityLog;
use App\Models\ClassSubject;
use App\Models\Material;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class MaterialController extends Controller
{
    private function getTeacher()
    {
        return auth()->user()->teacher;
    }

    public function index(Request $request): View
    {
        $teacher = $this->getTeacher();
        abort_if(! $teacher, 403, 'Akses khusus guru.');

        $myClassSubjects = ClassSubject::with(['schoolClass', 'subject'])
            ->where('teacher_id', $teacher->id)
            ->whereHas('academicYear', fn ($q) => $q->where('is_active', true))
            ->get();

        $query = Material::with(['classSubject.schoolClass', 'classSubject.subject', 'uploader'])
            ->whereHas('classSubject', fn ($q) => $q->where('teacher_id', $teacher->id));

        if ($request->filled('class_subject_id')) {
            $query->where('class_subject_id', $request->input('class_subject_id'));
        }

        $perPage = (int) $request->input('per_page', 10);
        if (! in_array($perPage, [10, 25, 50, 100])) {
            $perPage = 10;
        }

        $materials = $query->latest('id')->paginate($perPage)->withQueryString();

        return view('teacher.materials.index', compact('materials', 'myClassSubjects'));
    }

    public function create(): View
    {
        $teacher = $this->getTeacher();
        abort_if(! $teacher, 403, 'Akses khusus guru.');

        $myClassSubjects = ClassSubject::with(['schoolClass', 'subject'])
            ->where('teacher_id', $teacher->id)
            ->whereHas('academicYear', fn ($q) => $q->where('is_active', true))
            ->get();

        return view('teacher.materials.create', compact('myClassSubjects'));
    }

    public function store(MaterialRequest $request): RedirectResponse
    {
        $teacher = $this->getTeacher();
        $validated = $request->validated();

        $file = $request->file('material_file');
        $storedPath = $file->store('materials', 'local');

        $classSubject = ClassSubject::find($validated['class_subject_id']);

        $material = Material::create([
            'class_subject_id' => $classSubject->id,
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'file_path' => $storedPath,
            'original_filename' => $file->getClientOriginalName(),
            'mime_type' => $file->getClientMimeType() ?: $file->getMimeType(),
            'file_size' => $file->getSize(),
            'uploaded_by' => auth()->id(),
        ]);

        ActivityLog::log(
            'create',
            'Material',
            $material->id,
            "Mengunggah materi '{$material->title}' untuk kelas {$classSubject->schoolClass->name} mapel {$classSubject->subject->name}."
        );

        return redirect()->route('teacher.materials.index', ['class_subject_id' => $classSubject->id])
            ->with('success', "Materi '{$material->title}' berhasil diunggah.");
    }

    public function download(Material $material): StreamedResponse
    {
        $teacher = $this->getTeacher();
        abort_if(! $teacher, 403, 'Akses khusus guru.');
        abort_if($material->classSubject->teacher_id !== $teacher->id, 403, 'Akses ditolak.');

        abort_if(! Storage::disk('local')->exists($material->file_path), 404, 'Berkas materi tidak ditemukan.');

        return Storage::disk('local')->download($material->file_path, $material->original_filename);
    }

    public function destroy(Material $material): RedirectResponse
    {
        $teacher = $this->getTeacher();
        abort_if(! $teacher, 403, 'Akses khusus guru.');
        abort_if($material->classSubject->teacher_id !== $teacher->id, 403, 'Akses ditolak.');

        $title = $material->title;
        $id = $material->id;
        $classSubjectId = $material->class_subject_id;

        if (Storage::disk('local')->exists($material->file_path)) {
            Storage::disk('local')->delete($material->file_path);
        }

        $material->delete();

        ActivityLog::log(
            'delete',
            'Material',
            $id,
            "Menghapus materi pembelajaran '{$title}'."
        );

        return redirect()->route('teacher.materials.index', ['class_subject_id' => $classSubjectId])
            ->with('success', "Materi '{$title}' berhasil dihapus.");
    }
}
