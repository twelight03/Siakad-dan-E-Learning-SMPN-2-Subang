<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SubjectRequest;
use App\Models\ActivityLog;
use App\Models\Subject;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SubjectController extends Controller
{
    public function index(Request $request): View
    {
        $query = Subject::query();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%");
            });
        }

        $perPage = (int) $request->input('per_page', 10);
        if (! in_array($perPage, [10, 25, 50, 100])) {
            $perPage = 10;
        }

        $subjects = $query->orderBy('code')->paginate($perPage)->withQueryString();

        return view('admin.subjects.index', compact('subjects'));
    }

    public function create(): View
    {
        return view('admin.subjects.create');
    }

    public function store(SubjectRequest $request): RedirectResponse
    {
        $subject = Subject::create($request->validated());

        ActivityLog::log('create', 'Subject', $subject->id, "Menambahkan mata pelajaran [{$subject->code}] {$subject->name}.");

        return redirect()->route('admin.subjects.index')
            ->with('success', 'Mata pelajaran berhasil ditambahkan.');
    }

    public function edit(Subject $subject): View
    {
        return view('admin.subjects.edit', compact('subject'));
    }

    public function update(SubjectRequest $request, Subject $subject): RedirectResponse
    {
        $subject->update($request->validated());

        ActivityLog::log('update', 'Subject', $subject->id, "Memperbarui mata pelajaran [{$subject->code}] {$subject->name}.");

        return redirect()->route('admin.subjects.index')
            ->with('success', 'Mata pelajaran berhasil diperbarui.');
    }

    public function destroy(Subject $subject): RedirectResponse
    {
        if ($subject->classSubjects()->exists()) {
            return redirect()->route('admin.subjects.index')
                ->with('error', 'Mata pelajaran tidak dapat dihapus karena sudah digunakan dalam penugasan kelas.');
        }

        $name = $subject->name;
        $code = $subject->code;
        $id = $subject->id;
        $subject->delete();

        ActivityLog::log('delete', 'Subject', $id, "Menghapus mata pelajaran [{$code}] {$name}.");

        return redirect()->route('admin.subjects.index')
            ->with('success', 'Mata pelajaran berhasil dihapus.');
    }
}
