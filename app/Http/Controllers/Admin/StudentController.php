<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StudentRequest;
use App\Models\ActivityLog;
use App\Models\Role;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class StudentController extends Controller
{
    public function index(Request $request): View
    {
        $query = Student::with(['user', 'currentEnrollment.schoolClass']);

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('nis', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('name', 'like', "%{$search}%")
                         ->orWhere('username', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('gender')) {
            $query->where('gender', $request->input('gender'));
        }

        $perPage = (int) $request->input('per_page', 10);
        if (! in_array($perPage, [10, 25, 50, 100])) {
            $perPage = 10;
        }

        $students = $query->latest()->paginate($perPage)->withQueryString();

        return view('admin.students.index', compact('students'));
    }

    public function create(): View
    {
        return view('admin.students.create');
    }

    public function store(StudentRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $studentRole = Role::firstOrCreate(['name' => 'student'], ['display_name' => 'Siswa']);

        DB::transaction(function () use ($validated, $studentRole) {
            $user = User::create([
                'role_id' => $studentRole->id,
                'name' => $validated['name'],
                'username' => $validated['username'],
                'email' => $validated['email'] ?? null,
                'password' => Hash::make($validated['password']),
                'is_active' => $validated['is_active'] ?? true,
            ]);

            $student = Student::create([
                'user_id' => $user->id,
                'nis' => $validated['nis'],
                'gender' => $validated['gender'],
                'birth_date' => $validated['birth_date'] ?? null,
                'phone' => $validated['phone'] ?? null,
                'address' => $validated['address'] ?? null,
            ]);

            ActivityLog::log('create', 'Student', $student->id, "Menambahkan data siswa {$user->name} (NIS: {$student->nis}).");
        });

        return redirect()->route('admin.students.index')
            ->with('success', 'Data siswa dan akun berhasil ditambahkan.');
    }

    public function edit(Student $student): View
    {
        $student->load('user');

        return view('admin.students.edit', compact('student'));
    }

    public function update(StudentRequest $request, Student $student): RedirectResponse
    {
        $validated = $request->validated();

        DB::transaction(function () use ($validated, $student) {
            $userData = [
                'name' => $validated['name'],
                'username' => $validated['username'],
                'email' => $validated['email'] ?? null,
                'is_active' => $validated['is_active'] ?? true,
            ];

            if (! empty($validated['password'])) {
                $userData['password'] = Hash::make($validated['password']);
            }

            $student->user->update($userData);

            $student->update([
                'nis' => $validated['nis'],
                'gender' => $validated['gender'],
                'birth_date' => $validated['birth_date'] ?? null,
                'phone' => $validated['phone'] ?? null,
                'address' => $validated['address'] ?? null,
            ]);

            ActivityLog::log('update', 'Student', $student->id, "Memperbarui data siswa {$student->user->name}.");
        });

        return redirect()->route('admin.students.index')
            ->with('success', 'Data siswa dan akun berhasil diperbarui.');
    }

    public function destroy(Student $student): RedirectResponse
    {
        if ($student->enrollments()->exists() || $student->attendances()->exists() || $student->grades()->exists()) {
            return redirect()->route('admin.students.index')
                ->with('error', 'Siswa tidak dapat dihapus karena memiliki riwayat akademik aktif (penempatan kelas, absensi, atau nilai).');
        }

        DB::transaction(function () use ($student) {
            $name = $student->user->name;
            $id = $student->id;
            $user = $student->user;

            $student->delete();
            $user->delete();

            ActivityLog::log('delete', 'Student', $id, "Menghapus data siswa {$name}.");
        });

        return redirect()->route('admin.students.index')
            ->with('success', 'Data siswa dan akun berhasil dihapus.');
    }
}
