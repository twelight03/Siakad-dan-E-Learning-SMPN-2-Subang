<?php

namespace Tests\Feature\Elearning;

use App\Models\AcademicYear;
use App\Models\Assignment;
use App\Models\AssignmentGrade;
use App\Models\ClassSubject;
use App\Models\Role;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\StudentEnrollment;
use App\Models\Submission;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AssignmentTest extends TestCase
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
        Storage::fake('local');

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
            'code' => 'TIK-7',
            'name' => 'Informatika',
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

    public function test_unauthenticated_user_cannot_access_assignments(): void
    {
        $response = $this->get(route('teacher.assignments.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_teacher_can_view_assignments_index(): void
    {
        $response = $this->actingAs($this->teacherUser)->get(route('teacher.assignments.index'));
        $response->assertOk();
        $response->assertSee('Kelola Tugas Siswa (E-Learning)');
    }

    public function test_teacher_can_create_assignment_with_attachment(): void
    {
        $attachment = UploadedFile::fake()->create('soal_latihan.pdf', 500, 'application/pdf');

        $response = $this->actingAs($this->teacherUser)->post(route('teacher.assignments.store'), [
            'class_subject_id' => $this->classSubject->id,
            'title' => 'Tugas 1 - Pemrograman Dasar',
            'description' => 'Kerjakan latihan soal di file lampiran.',
            'deadline' => now()->addDays(5)->format('Y-m-d H:i:s'),
            'attachment' => $attachment,
        ]);

        $response->assertRedirect(route('teacher.assignments.index', ['class_subject_id' => $this->classSubject->id]));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('assignments', [
            'class_subject_id' => $this->classSubject->id,
            'title' => 'Tugas 1 - Pemrograman Dasar',
        ]);

        $assignment = Assignment::where('title', 'Tugas 1 - Pemrograman Dasar')->first();
        $this->assertNotNull($assignment->attachment_path);
        Storage::disk('local')->assertExists($assignment->attachment_path);

        $this->assertDatabaseHas('activity_logs', [
            'user_id' => $this->teacherUser->id,
            'action' => 'create',
            'entity_type' => 'Assignment',
        ]);
    }

    public function test_assignment_validation_rules(): void
    {
        $response = $this->actingAs($this->teacherUser)->post(route('teacher.assignments.store'), [
            'class_subject_id' => '',
            'title' => '',
            'description' => '',
            'deadline' => 'invalid-date',
        ]);

        $response->assertSessionHasErrors(['class_subject_id', 'title', 'description', 'deadline']);
    }

    public function test_teacher_cannot_create_assignment_for_unassigned_class(): void
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

        $response = $this->actingAs($otherTeacherUser)->post(route('teacher.assignments.store'), [
            'class_subject_id' => $this->classSubject->id,
            'title' => 'Tugas Ilegal',
            'description' => 'Test',
            'deadline' => now()->addDays(2)->format('Y-m-d H:i:s'),
        ]);

        $response->assertForbidden();
    }

    public function test_enrolled_student_can_view_assignment_and_download_attachment(): void
    {
        $path = Storage::disk('local')->putFile('assignments', UploadedFile::fake()->create('lembar_kerja.pdf', 100, 'application/pdf'));

        $assignment = Assignment::create([
            'class_subject_id' => $this->classSubject->id,
            'title' => 'Tugas Lembar Kerja',
            'description' => 'Silakan isi berkas ini.',
            'attachment_path' => $path,
            'deadline' => now()->addDays(3),
            'created_by' => $this->teacherUser->id,
        ]);

        $responseIndex = $this->actingAs($this->studentUser)->get(route('student.assignments.index'));
        $responseIndex->assertOk();
        $responseIndex->assertSee('Tugas Lembar Kerja');

        $responseShow = $this->actingAs($this->studentUser)->get(route('student.assignments.show', $assignment->id));
        $responseShow->assertOk();
        $responseShow->assertSee('Silakan isi berkas ini.');

        $responseDownload = $this->actingAs($this->studentUser)->get(route('student.assignments.attachment.download', $assignment->id));
        $responseDownload->assertOk();
    }

    public function test_unenrolled_student_cannot_access_assignment(): void
    {
        $studentRole = Role::where('name', 'student')->first();
        $otherStudentUser = User::factory()->create([
            'role_id' => $studentRole->id,
            'is_active' => true,
        ]);
        Student::create([
            'user_id' => $otherStudentUser->id,
            'nis' => '99999999',
            'gender' => 'female',
        ]);

        $assignment = Assignment::create([
            'class_subject_id' => $this->classSubject->id,
            'title' => 'Tugas Khusus 7-A',
            'description' => 'Hanya untuk siswa 7-A.',
            'deadline' => now()->addDays(3),
            'created_by' => $this->teacherUser->id,
        ]);

        $response = $this->actingAs($otherStudentUser)->get(route('student.assignments.show', $assignment->id));
        $response->assertForbidden();
    }

    public function test_student_can_submit_assignment_before_deadline(): void
    {
        $assignment = Assignment::create([
            'class_subject_id' => $this->classSubject->id,
            'title' => 'Tugas HTML Dasar',
            'description' => 'Buat halaman profil sederhana.',
            'deadline' => now()->addDays(2),
            'created_by' => $this->teacherUser->id,
        ]);

        $submissionFile = UploadedFile::fake()->create('profil.zip', 200, 'application/zip');

        $response = $this->actingAs($this->studentUser)->post(route('student.assignments.submit', $assignment->id), [
            'submission_file' => $submissionFile,
            'text_answer' => 'Berikut adalah berkas profil tugas saya.',
        ]);

        $response->assertRedirect(route('student.assignments.show', $assignment->id));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('submissions', [
            'assignment_id' => $assignment->id,
            'student_id' => $this->student->id,
            'status' => 'submitted',
        ]);

        $this->assertDatabaseHas('activity_logs', [
            'user_id' => $this->studentUser->id,
            'action' => 'create',
            'entity_type' => 'Submission',
        ]);
    }

    public function test_student_submission_after_deadline_marked_as_late(): void
    {
        $assignment = Assignment::create([
            'class_subject_id' => $this->classSubject->id,
            'title' => 'Tugas Deadline Lewat',
            'description' => 'Sudah lewat.',
            'deadline' => now()->subDay(), // Past deadline
            'created_by' => $this->teacherUser->id,
        ]);

        $response = $this->actingAs($this->studentUser)->post(route('student.assignments.submit', $assignment->id), [
            'text_answer' => 'Mohon maaf mengumpulkan terlambat karena sakit.',
        ]);

        $response->assertRedirect(route('student.assignments.show', $assignment->id));

        $this->assertDatabaseHas('submissions', [
            'assignment_id' => $assignment->id,
            'student_id' => $this->student->id,
            'status' => 'late',
        ]);
    }

    public function test_student_submission_fails_if_both_file_and_text_empty(): void
    {
        $assignment = Assignment::create([
            'class_subject_id' => $this->classSubject->id,
            'title' => 'Tugas Wajib Isi',
            'description' => 'Harus ada jawaban.',
            'deadline' => now()->addDays(2),
            'created_by' => $this->teacherUser->id,
        ]);

        $response = $this->actingAs($this->studentUser)->post(route('student.assignments.submit', $assignment->id), [
            'text_answer' => '',
        ]);

        $response->assertSessionHasErrors(['submission_file']);
    }

    public function test_teacher_can_view_submission_and_grade_it(): void
    {
        $assignment = Assignment::create([
            'class_subject_id' => $this->classSubject->id,
            'title' => 'Tugas Dinilai',
            'description' => 'Deskripsi.',
            'deadline' => now()->addDays(2),
            'created_by' => $this->teacherUser->id,
        ]);

        $submission = Submission::create([
            'assignment_id' => $assignment->id,
            'student_id' => $this->student->id,
            'text_answer' => 'Jawaban lengkap saya.',
            'submitted_at' => now(),
            'status' => 'submitted',
        ]);

        $responseShow = $this->actingAs($this->teacherUser)->get(route('teacher.assignments.show', $assignment->id));
        $responseShow->assertOk();
        $responseShow->assertSee('Jawaban lengkap saya.');

        $responseGrade = $this->actingAs($this->teacherUser)->post(route('teacher.assignments.submissions.grade', $submission->id), [
            'score' => 95,
            'feedback' => 'Hasil pengerjaan sangat rapi dan tepat.',
        ]);

        $responseGrade->assertRedirect(route('teacher.assignments.show', $assignment->id));

        $this->assertDatabaseHas('assignment_grades', [
            'submission_id' => $submission->id,
            'score' => 95.00,
            'feedback' => 'Hasil pengerjaan sangat rapi dan tepat.',
            'graded_by' => $this->teacherUser->id,
        ]);

        $this->assertDatabaseHas('activity_logs', [
            'user_id' => $this->teacherUser->id,
            'action' => 'update',
            'entity_type' => 'AssignmentGrade',
        ]);
    }

    public function test_grade_validation_score_range(): void
    {
        $assignment = Assignment::create([
            'class_subject_id' => $this->classSubject->id,
            'title' => 'Tugas Range Skor',
            'description' => 'Deskripsi.',
            'deadline' => now()->addDays(2),
            'created_by' => $this->teacherUser->id,
        ]);

        $submission = Submission::create([
            'assignment_id' => $assignment->id,
            'student_id' => $this->student->id,
            'text_answer' => 'Jawaban',
            'submitted_at' => now(),
            'status' => 'submitted',
        ]);

        $response = $this->actingAs($this->teacherUser)->post(route('teacher.assignments.submissions.grade', $submission->id), [
            'score' => 150, // exceeds 100
        ]);

        $response->assertSessionHasErrors(['score']);
    }

    public function test_student_cannot_download_another_students_submission(): void
    {
        $studentRole = Role::where('name', 'student')->first();
        $otherStudentUser = User::factory()->create([
            'role_id' => $studentRole->id,
            'is_active' => true,
        ]);
        $otherStudent = Student::create([
            'user_id' => $otherStudentUser->id,
            'nis' => '88888888',
            'gender' => 'female',
        ]);

        $assignment = Assignment::create([
            'class_subject_id' => $this->classSubject->id,
            'title' => 'Tugas Anti-IDOR',
            'description' => 'Deskripsi.',
            'deadline' => now()->addDays(2),
            'created_by' => $this->teacherUser->id,
        ]);

        $path = Storage::disk('local')->putFile('submissions', UploadedFile::fake()->create('jawaban_rahasia.pdf', 100, 'application/pdf'));

        $submission = Submission::create([
            'assignment_id' => $assignment->id,
            'student_id' => $otherStudent->id,
            'file_path' => $path,
            'submitted_at' => now(),
            'status' => 'submitted',
        ]);

        // Student $this->student tries to download $otherStudent's submission
        $response = $this->actingAs($this->studentUser)->get(route('student.assignments.submissions.download', $submission->id));
        $response->assertForbidden();
    }

    public function test_teacher_can_delete_assignment_and_cascade_submissions(): void
    {
        $path = Storage::disk('local')->putFile('assignments', UploadedFile::fake()->create('attachment.pdf', 100, 'application/pdf'));

        $assignment = Assignment::create([
            'class_subject_id' => $this->classSubject->id,
            'title' => 'Tugas Dihapus',
            'description' => 'Akan dihapus.',
            'attachment_path' => $path,
            'deadline' => now()->addDays(2),
            'created_by' => $this->teacherUser->id,
        ]);

        $subPath = Storage::disk('local')->putFile('submissions', UploadedFile::fake()->create('sub.pdf', 100, 'application/pdf'));

        $submission = Submission::create([
            'assignment_id' => $assignment->id,
            'student_id' => $this->student->id,
            'file_path' => $subPath,
            'submitted_at' => now(),
            'status' => 'submitted',
        ]);

        $response = $this->actingAs($this->teacherUser)->delete(route('teacher.assignments.destroy', $assignment->id));
        $response->assertRedirect(route('teacher.assignments.index', ['class_subject_id' => $this->classSubject->id]));

        $this->assertDatabaseMissing('assignments', ['id' => $assignment->id]);
        $this->assertDatabaseMissing('submissions', ['id' => $submission->id]);
        Storage::disk('local')->assertMissing($path);
        Storage::disk('local')->assertMissing($subPath);
    }
}
