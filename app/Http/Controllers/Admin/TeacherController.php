<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\TeacherRequest;
use App\Models\ActivityLog;
use App\Models\Role;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class TeacherController extends Controller
{
    public function index(Request $request): View
    {
        $query = Teacher::with('user');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('nip', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('name', 'like', "%{$search}%")
                         ->orWhere('username', 'like', "%{$search}%");
                  });
            });
        }

        $perPage = (int) $request->input('per_page', 10);
        if (! in_array($perPage, [10, 25, 50, 100])) {
            $perPage = 10;
        }

        $teachers = $query->latest()->paginate($perPage)->withQueryString();

        return view('admin.teachers.index', compact('teachers'));
    }

    public function create(): View
    {
        return view('admin.teachers.create');
    }

    public function store(TeacherRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $teacherRole = Role::firstOrCreate(['name' => 'teacher'], ['display_name' => 'Guru']);

        DB::transaction(function () use ($validated, $teacherRole) {
            $user = User::create([
                'role_id' => $teacherRole->id,
                'name' => $validated['name'],
                'username' => $validated['username'],
                'email' => $validated['email'] ?? null,
                'password' => Hash::make($validated['password']),
                'is_active' => $validated['is_active'] ?? true,
            ]);

            $teacher = Teacher::create([
                'user_id' => $user->id,
                'nip' => $validated['nip'] ?? null,
                'gender' => $validated['gender'] ?? null,
                'phone' => $validated['phone'] ?? null,
                'address' => $validated['address'] ?? null,
            ]);

            ActivityLog::log('create', 'Teacher', $teacher->id, "Menambahkan data guru {$user->name} (NIP: {$teacher->nip}).");
        });

        return redirect()->route('admin.teachers.index')
            ->with('success', 'Data guru dan akun berhasil ditambahkan.');
    }

    public function edit(Teacher $teacher): View
    {
        $teacher->load('user');

        return view('admin.teachers.edit', compact('teacher'));
    }

    public function update(TeacherRequest $request, Teacher $teacher): RedirectResponse
    {
        $validated = $request->validated();

        DB::transaction(function () use ($validated, $teacher) {
            $userData = [
                'name' => $validated['name'],
                'username' => $validated['username'],
                'email' => $validated['email'] ?? null,
                'is_active' => $validated['is_active'] ?? true,
            ];

            if (! empty($validated['password'])) {
                $userData['password'] = Hash::make($validated['password']);
            }

            $teacher->user->update($userData);

            $teacher->update([
                'nip' => $validated['nip'] ?? null,
                'gender' => $validated['gender'] ?? null,
                'phone' => $validated['phone'] ?? null,
                'address' => $validated['address'] ?? null,
            ]);

            ActivityLog::log('update', 'Teacher', $teacher->id, "Memperbarui data guru {$teacher->user->name}.");
        });

        return redirect()->route('admin.teachers.index')
            ->with('success', 'Data guru dan akun berhasil diperbarui.');
    }

    public function destroy(Teacher $teacher): RedirectResponse
    {
        if ($teacher->homeroomClasses()->exists() || $teacher->classSubjects()->exists()) {
            return redirect()->route('admin.teachers.index')
                ->with('error', 'Guru tidak dapat dihapus karena masih menjabat sebagai wali kelas atau memiliki penugasan mata pelajaran aktif.');
        }

        DB::transaction(function () use ($teacher) {
            $name = $teacher->user->name;
            $id = $teacher->id;
            $user = $teacher->user;

            $teacher->delete();
            $user->delete();

            ActivityLog::log('delete', 'Teacher', $id, "Menghapus data guru {$name}.");
        });

        return redirect()->route('admin.teachers.index')
            ->with('success', 'Data guru dan akun berhasil dihapus.');
    }
}
