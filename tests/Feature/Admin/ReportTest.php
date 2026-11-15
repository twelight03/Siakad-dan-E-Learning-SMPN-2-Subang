<?php

namespace Tests\Feature\Admin;

use App\Models\AcademicYear;
use App\Models\ActivityLog;
use App\Models\Assignment;
use App\Models\Attendance;
use App\Models\AttendanceSession;
use App\Models\ClassSubject;
use App\Models\Grade;
use App\Models\GradeComponent;
use App\Models\Role;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\StudentEnrollment;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportTest extends TestCase
{
    use RefreshDatabase;

    private User $adminUser;
    private User $teacherUser;
    private User $studentUser;
    private Teacher $teacher;
    private Student $student;
    private SchoolClass $schoolClass;
    private Subject $subject;
    private AcademicYear $academicYear;
    private ClassSubject $classSubject;

    protected function setUp(): void
    {
        parent::setUp();

        Role::create(['name' => 'admin', 'display_name' => 'Administrator']);
        Role::create(['name' => 'teacher', 'display_name' => 'Guru']);
        Role::create(['name' => 'student', 'display_name' => 'Siswa']);

        $adminRole = Role::where('name', 'admin')->first();
        $teacherRole = Role::where('name', 'teacher')->first();
        $studentRole = Role::where('name', 'student')->first();

        $this->academicYear = AcademicYear::create([
            'name' => '2025/2026',
            'semester' => 'odd',
            'is_active' => true,
            'start_date' => '2025-07-15',
            'end_date' => '2025-12-20',
        ]);

        $this->schoolClass = SchoolClass::create([
            'name' => '7-A',
            'grade_level' => '7',
        ]);

        $this->subject = Subject::create([
            'code' => 'BIN-7',
            'name' => 'Bahasa Indonesia',
            'kkm' => 75,
        ]);

        $this->adminUser = User::factory()->create([
            'role_id' => $adminRole->id,
            'is_active' => true,
        ]);

        $this->teacherUser = User::factory()->create([
            'role_id' => $teacherRole->id,
            'is_active' => true,
        ]);
        $this->teacher = Teacher::create([
            'user_id' => $this->teacherUser->id,
            'nip' => '198501012010011001',
            'gender' => 'male',
        ]);

        $this->classSubject = ClassSubject::create([
            'class_id' => $this->schoolClass->id,
            'subject_id' => $this->subject->id,
            'teacher_id' => $this->teacher->id,
            'academic_year_id' => $this->academicYear->id,
        ]);

        $this->studentUser = User::factory()->create([
            'role_id' => $studentRole->id,
            'is_active' => true,
        ]);
        $this->student = Student::create([
            'user_id' => $this->studentUser->id,
            'nis' => '12345678',
            'gender' => 'male',
        ]);

        StudentEnrollment::create([
            'student_id' => $this->student->id,
            'class_id' => $this->schoolClass->id,
            'academic_year_id' => $this->academicYear->id,
        ]);
    }

    public function test_non_admin_cannot_access_activity_logs(): void
    {
        $response = $this->actingAs($this->studentUser)->get(route('admin.activity-logs.index'));
        $response->assertForbidden();
    }

    public function test_admin_can_view_activity_logs_index_and_filter(): void
    {
        ActivityLog::create([
            'user_id' => $this->adminUser->id,
            'action' => 'create',
            'entity_type' => 'User',
            'entity_id' => 1,
            'description' => 'Membuat akun pengguna baru.',
            'ip_address' => '127.0.0.1',
        ]);

        $response = $this->actingAs($this->adminUser)->get(route('admin.activity-logs.index'));
        $response->assertOk();
        $response->assertSee('Log Audit Aktivitas Sistem');
        $response->assertSee('Membuat akun pengguna baru.');
    }

    public function test_non_admin_cannot_access_reports(): void
    {
        $response = $this->actingAs($this->studentUser)->get(route('admin.reports.index'));
        $response->assertForbidden();
    }

    public function test_admin_can_view_reports_index(): void
    {
        $response = $this->actingAs($this->adminUser)->get(route('admin.reports.index'));
        $response->assertOk();
        $response->assertSee('Pusat Laporan Akademik');
        $response->assertSee('Laporan Daftar Siswa per Kelas');
        $response->assertSee('Laporan Rekap Absensi Siswa');
        $response->assertSee('Laporan Rekap Nilai Akademik');
    }

    public function test_admin_can_generate_student_list_report(): void
    {
        $response = $this->actingAs($this->adminUser)->get(route('admin.reports.students', [
            'class_id' => $this->schoolClass->id,
            'academic_year_id' => $this->academicYear->id,
        ]));

        $response->assertOk();
        $response->assertSee('Daftar Siswa Kelas 7-A');
        $response->assertSee($this->studentUser->name);
        $response->assertSee($this->student->nis);
    }

    public function test_admin_can_print_student_list_report(): void
    {
        $response = $this->actingAs($this->adminUser)->get(route('admin.reports.students', [
            'class_id' => $this->schoolClass->id,
            'academic_year_id' => $this->academicYear->id,
            'print' => 1,
        ]));

        $response->assertOk();
        $response->assertSee('SMP Negeri 2 Subang');
        $response->assertSee('DAFTAR SISWA ROMBONGAN BELAJAR');
        $response->assertSee($this->studentUser->name);
    }

    public function test_admin_can_generate_attendance_recap_report(): void
    {
        $session = AttendanceSession::create([
            'class_subject_id' => $this->classSubject->id,
            'session_date' => '2025-08-01',
            'created_by' => $this->teacherUser->id,
        ]);

        Attendance::create([
            'attendance_session_id' => $session->id,
            'student_id' => $this->student->id,
            'status' => 'present',
            'recorded_by' => $this->teacherUser->id,
        ]);

        $response = $this->actingAs($this->adminUser)->get(route('admin.reports.attendances', [
            'class_subject_id' => $this->classSubject->id,
        ]));

        $response->assertOk();
        $response->assertSee('Laporan Rekapitulasi Absensi Siswa');
        $response->assertSee($this->studentUser->name);
        $response->assertSee('100%');
    }

    public function test_admin_can_print_attendance_recap_report(): void
    {
        $response = $this->actingAs($this->adminUser)->get(route('admin.reports.attendances', [
            'class_subject_id' => $this->classSubject->id,
            'print' => 1,
        ]));

        $response->assertOk();
        $response->assertSee('REKAPITULASI KEHADIRAN / ABSENSI SISWA');
        $response->assertSee('SMP Negeri 2 Subang');
    }

    public function test_admin_can_generate_grade_recap_report(): void
    {
        $component = GradeComponent::create([
            'class_subject_id' => $this->classSubject->id,
            'name' => 'UTS',
            'weight' => 100,
            'max_score' => 100,
        ]);

        Grade::create([
            'grade_component_id' => $component->id,
            'student_id' => $this->student->id,
            'score' => 88,
            'graded_by' => $this->teacherUser->id,
        ]);

        $response = $this->actingAs($this->adminUser)->get(route('admin.reports.grades', [
            'class_subject_id' => $this->classSubject->id,
        ]));

        $response->assertOk();
        $response->assertSee('Laporan Rekapitulasi Nilai Siswa');
        $response->assertSee('88');
        $response->assertSee('Tuntas');
    }

    public function test_admin_can_print_grade_recap_report(): void
    {
        $response = $this->actingAs($this->adminUser)->get(route('admin.reports.grades', [
            'class_subject_id' => $this->classSubject->id,
            'print' => 1,
        ]));

        $response->assertOk();
        $response->assertSee('DAFTAR REKAPITULASI NILAI AKADEMIK SISWA');
        $response->assertSee('SMP Negeri 2 Subang');
    }

    public function test_admin_can_generate_assignment_summary_report(): void
    {
        Assignment::create([
            'class_subject_id' => $this->classSubject->id,
            'title' => 'Tugas Menulis Cerpen',
            'description' => 'Tulis cerpen bertema persahabatan.',
            'deadline' => now()->addDays(5),
            'created_by' => $this->teacherUser->id,
        ]);

        $response = $this->actingAs($this->adminUser)->get(route('admin.reports.assignments', [
            'class_subject_id' => $this->classSubject->id,
        ]));

        $response->assertOk();
        $response->assertSee('Laporan Penugasan E-Learning');
        $response->assertSee('Tugas Menulis Cerpen');
    }

    public function test_admin_can_print_assignment_summary_report(): void
    {
        $response = $this->actingAs($this->adminUser)->get(route('admin.reports.assignments', [
            'class_subject_id' => $this->classSubject->id,
            'print' => 1,
        ]));

        $response->assertOk();
        $response->assertSee('REKAPITULASI PENUGASAN E-LEARNING');
        $response->assertSee('SMP Negeri 2 Subang');
    }
}
