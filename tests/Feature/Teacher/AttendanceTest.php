<?php

namespace Tests\Feature\Teacher;

use App\Models\AcademicYear;
use App\Models\Attendance;
use App\Models\AttendanceSession;
use App\Models\ClassSubject;
use App\Models\Role;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\StudentEnrollment;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AttendanceTest extends TestCase
{
    use RefreshDatabase;

    private User $teacherUser;
    private Teacher $teacher;
    private User $studentUser;
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
            'code' => 'MAT-7',
            'name' => 'Matematika',
            'kkm' => 75,
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

    public function test_unauthenticated_user_cannot_access_attendances(): void
    {
        $response = $this->get(route('teacher.attendances.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_student_cannot_access_teacher_attendance_routes(): void
    {
        $response = $this->actingAs($this->studentUser)->get(route('teacher.attendances.index'));
        $response->assertForbidden();
    }

    public function test_teacher_can_view_attendance_index(): void
    {
        $response = $this->actingAs($this->teacherUser)->get(route('teacher.attendances.index'));
        $response->assertOk();
        $response->assertSee('Absensi & Kehadiran Siswa');
    }

    public function test_teacher_can_view_create_attendance_page(): void
    {
        $response = $this->actingAs($this->teacherUser)->get(
            route('teacher.attendances.create', ['class_subject_id' => $this->classSubject->id])
        );
        $response->assertOk();
        $response->assertSee($this->studentUser->name);
        $response->assertSee($this->student->nis);
    }

    public function test_teacher_can_record_attendance_session_and_student_records(): void
    {
        $response = $this->actingAs($this->teacherUser)->post(route('teacher.attendances.store'), [
            'class_subject_id' => $this->classSubject->id,
            'session_date' => '2025-08-01',
            'topic' => 'Pengenalan Bilangan Bulat',
            'attendances' => [
                [
                    'student_id' => $this->student->id,
                    'status' => 'present',
                    'note' => 'Hadir tepat waktu',
                ],
            ],
        ]);

        $response->assertRedirect(route('teacher.attendances.index', ['class_subject_id' => $this->classSubject->id]));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('attendance_sessions', [
            'class_subject_id' => $this->classSubject->id,
            'session_date' => '2025-08-01 00:00:00',
            'topic' => 'Pengenalan Bilangan Bulat',
        ]);

        $this->assertDatabaseHas('attendances', [
            'student_id' => $this->student->id,
            'status' => 'present',
            'note' => 'Hadir tepat waktu',
        ]);

        $this->assertDatabaseHas('activity_logs', [
            'user_id' => $this->teacherUser->id,
            'action' => 'create',
            'entity_type' => 'AttendanceSession',
        ]);
    }

    public function test_attendance_validation_rules(): void
    {
        $response = $this->actingAs($this->teacherUser)->post(route('teacher.attendances.store'), [
            'class_subject_id' => '',
            'session_date' => 'invalid-date',
            'attendances' => [],
        ]);

        $response->assertSessionHasErrors(['class_subject_id', 'session_date', 'attendances']);
    }

    public function test_teacher_cannot_record_attendance_for_unassigned_class(): void
    {
        $teacherRole = Role::where('name', 'teacher')->first();
        $otherTeacherUser = User::factory()->create([
            'role_id' => $teacherRole->id,
            'is_active' => true,
        ]);
        Teacher::create([
            'user_id' => $otherTeacherUser->id,
            'nip' => '198901012015011002',
            'gender' => 'female',
        ]);

        // Attempting to post attendance for classSubject that belongs to this->teacher, but logged in as other teacher
        $response = $this->actingAs($otherTeacherUser)->post(route('teacher.attendances.store'), [
            'class_subject_id' => $this->classSubject->id,
            'session_date' => '2025-08-01',
            'attendances' => [
                [
                    'student_id' => $this->student->id,
                    'status' => 'present',
                ],
            ],
        ]);

        $response->assertForbidden();
    }

    public function test_teacher_can_update_attendance_session_and_student_statuses(): void
    {
        $session = AttendanceSession::create([
            'class_subject_id' => $this->classSubject->id,
            'session_date' => '2025-08-01',
            'topic' => 'Topik Awal',
            'created_by' => $this->teacherUser->id,
        ]);

        $attendance = Attendance::create([
            'attendance_session_id' => $session->id,
            'student_id' => $this->student->id,
            'status' => 'present',
            'recorded_by' => $this->teacherUser->id,
        ]);

        $response = $this->actingAs($this->teacherUser)->put(route('teacher.attendances.update', $session->id), [
            'session_date' => '2025-08-02',
            'topic' => 'Topik Diperbarui',
            'attendances' => [
                $attendance->id => [
                    'status' => 'sick',
                    'note' => 'Surat dokter terlampir',
                ],
            ],
        ]);

        $response->assertRedirect(route('teacher.attendances.show', $session->id));

        $this->assertDatabaseHas('attendance_sessions', [
            'id' => $session->id,
            'session_date' => '2025-08-02 00:00:00',
            'topic' => 'Topik Diperbarui',
        ]);

        $this->assertDatabaseHas('attendances', [
            'id' => $attendance->id,
            'status' => 'sick',
            'note' => 'Surat dokter terlampir',
        ]);
    }

    public function test_teacher_cannot_update_or_delete_other_teachers_session(): void
    {
        $teacherRole = Role::where('name', 'teacher')->first();
        $otherTeacherUser = User::factory()->create([
            'role_id' => $teacherRole->id,
            'is_active' => true,
        ]);
        Teacher::create([
            'user_id' => $otherTeacherUser->id,
            'nip' => '199002022019012001',
            'gender' => 'female',
        ]);

        $session = AttendanceSession::create([
            'class_subject_id' => $this->classSubject->id,
            'session_date' => '2025-08-01',
            'created_by' => $this->teacherUser->id,
        ]);

        $response = $this->actingAs($otherTeacherUser)->put(route('teacher.attendances.update', $session->id), [
            'session_date' => '2025-08-02',
            'attendances' => [],
        ]);
        $response->assertForbidden();

        $responseDelete = $this->actingAs($otherTeacherUser)->delete(route('teacher.attendances.destroy', $session->id));
        $responseDelete->assertForbidden();
    }

    public function test_teacher_can_delete_attendance_session(): void
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

        $response = $this->actingAs($this->teacherUser)->delete(route('teacher.attendances.destroy', $session->id));
        $response->assertRedirect(route('teacher.attendances.index', ['class_subject_id' => $this->classSubject->id]));

        $this->assertDatabaseMissing('attendance_sessions', ['id' => $session->id]);
        $this->assertDatabaseMissing('attendances', ['attendance_session_id' => $session->id]);
    }

    public function test_student_can_view_own_attendance_recap(): void
    {
        $session = AttendanceSession::create([
            'class_subject_id' => $this->classSubject->id,
            'session_date' => '2025-08-01',
            'topic' => 'Matematika Dasar',
            'created_by' => $this->teacherUser->id,
        ]);

        Attendance::create([
            'attendance_session_id' => $session->id,
            'student_id' => $this->student->id,
            'status' => 'present',
            'note' => 'Hadir aktif',
            'recorded_by' => $this->teacherUser->id,
        ]);

        $response = $this->actingAs($this->studentUser)->get(route('student.attendances.index'));
        $response->assertOk();
        $response->assertSee('Rekap Kehadiran Siswa');
        $response->assertSee('Matematika Dasar');
        $response->assertSee('Hadir aktif');
    }

    public function test_student_can_view_own_schedule(): void
    {
        $response = $this->actingAs($this->studentUser)->get(route('student.schedules.index'));
        $response->assertOk();
        $response->assertSee('Jadwal Pelajaran Saya');
        $response->assertSee('7-A');
    }
}
