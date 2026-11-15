<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SchoolClassRequest;
use App\Models\ActivityLog;
use App\Models\SchoolClass;
use App\Models\Teacher;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SchoolClassController extends Controller
{
    public function index(Request $request): View
    {
        $query = SchoolClass::with(['homeroomTeacher.user'])->withCount('enrollments');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where('name', 'like', "%{$search}%");
        }

        if ($request->filled('grade_level')) {
            $query->where('grade_level', $request->input('grade_level'));
        }

        $perPage = (int) $request->input('per_page', 10);
        if (! in_array($perPage, [10, 25, 50, 100])) {
            $perPage = 10;
        }

        $classes = $query->orderBy('grade_level')->orderBy('name')->paginate($perPage)->withQueryString();

        return view('admin.classes.index', compact('classes'));
    }

    public function create(): View
    {
        $teachers = Teacher::with('user')->get();

        return view('admin.classes.create', compact('teachers'));
    }

    public function store(SchoolClassRequest $request): RedirectResponse
    {
        $class = SchoolClass::create($request->validated());

        ActivityLog::log('create', 'SchoolClass', $class->id, "Menambahkan kelas {$class->name} (Tingkat {$class->grade_level}).");

        return redirect()->route('admin.classes.index')
            ->with('success', 'Data rombel / kelas berhasil ditambahkan.');
    }

    public function edit(SchoolClass $class): View
    {
        $teachers = Teacher::with('user')->get();

        return view('admin.classes.edit', compact('class', 'teachers'));
    }

    public function update(SchoolClassRequest $request, SchoolClass $class): RedirectResponse
    {
        $class->update($request->validated());

        ActivityLog::log('update', 'SchoolClass', $class->id, "Memperbarui kelas {$class->name}.");

        return redirect()->route('admin.classes.index')
            ->with('success', 'Data rombel / kelas berhasil diperbarui.');
    }

    public function destroy(SchoolClass $class): RedirectResponse
    {
        if ($class->enrollments()->exists() || $class->classSubjects()->exists()) {
            return redirect()->route('admin.classes.index')
                ->with('error', 'Kelas tidak dapat dihapus karena masih memiliki data siswa terdaftar atau jadwal penugasan mapel.');
        }

        $name = $class->name;
        $id = $class->id;
        $class->delete();

        ActivityLog::log('delete', 'SchoolClass', $id, "Menghapus kelas {$name}.");

        return redirect()->route('admin.classes.index')
            ->with('success', 'Data rombel / kelas berhasil dihapus.');
    }
}
