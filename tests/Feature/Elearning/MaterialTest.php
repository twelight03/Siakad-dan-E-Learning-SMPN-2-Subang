<?php

namespace Tests\Feature\Elearning;

use App\Models\AcademicYear;
use App\Models\ClassSubject;
use App\Models\Material;
use App\Models\Role;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\StudentEnrollment;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class MaterialTest extends TestCase
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

    public function test_unauthenticated_user_cannot_access_materials(): void
    {
        $response = $this->get(route('teacher.materials.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_teacher_can_view_materials_index(): void
    {
        $response = $this->actingAs($this->teacherUser)->get(route('teacher.materials.index'));
        $response->assertOk();
        $response->assertSee('Materi Pembelajaran E-Learning');
    }

    public function test_teacher_can_upload_valid_material(): void
    {
        $file = UploadedFile::fake()->create('modul_informatika.pdf', 1024, 'application/pdf');

        $response = $this->actingAs($this->teacherUser)->post(route('teacher.materials.store'), [
            'class_subject_id' => $this->classSubject->id,
            'title' => 'Pengenalan Jaringan Komputer',
            'description' => 'Materi dasar topologi jaringan.',
            'material_file' => $file,
        ]);

        $response->assertRedirect(route('teacher.materials.index', ['class_subject_id' => $this->classSubject->id]));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('materials', [
            'class_subject_id' => $this->classSubject->id,
            'title' => 'Pengenalan Jaringan Komputer',
            'original_filename' => 'modul_informatika.pdf',
        ]);

        $material = Material::where('title', 'Pengenalan Jaringan Komputer')->first();
        Storage::disk('local')->assertExists($material->file_path);

        $this->assertDatabaseHas('activity_logs', [
            'user_id' => $this->teacherUser->id,
            'action' => 'create',
            'entity_type' => 'Material',
        ]);
    }

    public function test_material_upload_rejects_disallowed_mime_types(): void
    {
        $file = UploadedFile::fake()->create('malicious_script.exe', 500, 'application/x-msdownload');

        $response = $this->actingAs($this->teacherUser)->post(route('teacher.materials.store'), [
            'class_subject_id' => $this->classSubject->id,
            'title' => 'Berkas Berbahaya',
            'material_file' => $file,
        ]);

        $response->assertSessionHasErrors(['material_file']);
        $this->assertDatabaseMissing('materials', ['title' => 'Berkas Berbahaya']);
    }

    public function test_material_upload_rejects_oversized_file(): void
    {
        // 12MB exceeds 10MB limit
        $file = UploadedFile::fake()->create('large_video.pdf', 12288, 'application/pdf');

        $response = $this->actingAs($this->teacherUser)->post(route('teacher.materials.store'), [
            'class_subject_id' => $this->classSubject->id,
            'title' => 'Berkas Terlalu Besar',
            'material_file' => $file,
        ]);

        $response->assertSessionHasErrors(['material_file']);
    }

    public function test_teacher_cannot_upload_material_to_unassigned_class(): void
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

        $file = UploadedFile::fake()->create('modul.pdf', 500, 'application/pdf');

        $response = $this->actingAs($otherTeacherUser)->post(route('teacher.materials.store'), [
            'class_subject_id' => $this->classSubject->id,
            'title' => 'Materi Tidak Sah',
            'material_file' => $file,
        ]);

        $response->assertForbidden();
    }

    public function test_teacher_can_download_their_class_material(): void
    {
        $path = Storage::disk('local')->putFile('materials', UploadedFile::fake()->create('test.pdf', 100, 'application/pdf'));

        $material = Material::create([
            'class_subject_id' => $this->classSubject->id,
            'title' => 'Materi Tes',
            'file_path' => $path,
            'original_filename' => 'test.pdf',
            'mime_type' => 'application/pdf',
            'file_size' => 102400,
            'uploaded_by' => $this->teacherUser->id,
        ]);

        $response = $this->actingAs($this->teacherUser)->get(route('teacher.materials.download', $material->id));
        $response->assertOk();
    }

    public function test_enrolled_student_can_view_and_download_material(): void
    {
        $path = Storage::disk('local')->putFile('materials', UploadedFile::fake()->create('catatan.pdf', 100, 'application/pdf'));

        $material = Material::create([
            'class_subject_id' => $this->classSubject->id,
            'title' => 'Catatan Siswa',
            'file_path' => $path,
            'original_filename' => 'catatan.pdf',
            'mime_type' => 'application/pdf',
            'file_size' => 102400,
            'uploaded_by' => $this->teacherUser->id,
        ]);

        $responseIndex = $this->actingAs($this->studentUser)->get(route('student.materials.index'));
        $responseIndex->assertOk();
        $responseIndex->assertSee('Catatan Siswa');

        $responseDownload = $this->actingAs($this->studentUser)->get(route('student.materials.download', $material->id));
        $responseDownload->assertOk();
    }

    public function test_unenrolled_student_cannot_download_material(): void
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

        $path = Storage::disk('local')->putFile('materials', UploadedFile::fake()->create('secret.pdf', 100, 'application/pdf'));

        $material = Material::create([
            'class_subject_id' => $this->classSubject->id,
            'title' => 'Materi Rahasia Kelas Lain',
            'file_path' => $path,
            'original_filename' => 'secret.pdf',
            'mime_type' => 'application/pdf',
            'file_size' => 102400,
            'uploaded_by' => $this->teacherUser->id,
        ]);

        // Other student not enrolled in 7-A
        $response = $this->actingAs($otherStudentUser)->get(route('student.materials.download', $material->id));
        $response->assertForbidden();
    }

    public function test_teacher_can_delete_material(): void
    {
        $path = Storage::disk('local')->putFile('materials', UploadedFile::fake()->create('delete_me.pdf', 100, 'application/pdf'));

        $material = Material::create([
            'class_subject_id' => $this->classSubject->id,
            'title' => 'Materi Akan Dihapus',
            'file_path' => $path,
            'original_filename' => 'delete_me.pdf',
            'mime_type' => 'application/pdf',
            'file_size' => 102400,
            'uploaded_by' => $this->teacherUser->id,
        ]);

        $response = $this->actingAs($this->teacherUser)->delete(route('teacher.materials.destroy', $material->id));
        $response->assertRedirect(route('teacher.materials.index', ['class_subject_id' => $this->classSubject->id]));

        $this->assertDatabaseMissing('materials', ['id' => $material->id]);
        Storage::disk('local')->assertMissing($path);
    }
}
