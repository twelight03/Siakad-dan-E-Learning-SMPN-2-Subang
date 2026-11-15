<?php

namespace Tests\Feature\Teacher;

use App\Models\AcademicYear;
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

class GradeTest extends TestCase
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
            'code' => 'IPA-7',
            'name' => 'Ilmu Pengetahuan Alam',
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

    public function test_unauthenticated_user_cannot_access_grade_components(): void
    {
        $response = $this->get(route('teacher.grade-components.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_student_cannot_access_teacher_grade_components(): void
    {
        $response = $this->actingAs($this->studentUser)->get(route('teacher.grade-components.index'));
        $response->assertForbidden();
    }

    public function test_teacher_can_view_grade_components_index(): void
    {
        $response = $this->actingAs($this->teacherUser)->get(route('teacher.grade-components.index'));
        $response->assertOk();
        $response->assertSee('Komponen Penilaian & Bobot Nilai');
    }

    public function test_teacher_can_create_grade_component(): void
    {
        $response = $this->actingAs($this->teacherUser)->post(route('teacher.grade-components.store'), [
            'class_subject_id' => $this->classSubject->id,
            'name' => 'Tugas 1',
            'weight' => 20,
            'max_score' => 100,
        ]);

        $response->assertRedirect(route('teacher.grade-components.index', ['class_subject_id' => $this->classSubject->id]));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('grade_components', [
            'class_subject_id' => $this->classSubject->id,
            'name' => 'Tugas 1',
            'weight' => 20.00,
        ]);

        $this->assertDatabaseHas('activity_logs', [
            'user_id' => $this->teacherUser->id,
            'action' => 'create',
            'entity_type' => 'GradeComponent',
        ]);
    }

    public function test_grade_component_weight_cannot_exceed_100_percent_total(): void
    {
        GradeComponent::create([
            'class_subject_id' => $this->classSubject->id,
            'name' => 'UTS',
            'weight' => 70,
            'max_score' => 100,
        ]);

        // Attempt to add component with 40% weight (total = 110%)
        $response = $this->actingAs($this->teacherUser)->post(route('teacher.grade-components.store'), [
            'class_subject_id' => $this->classSubject->id,
            'name' => 'UAS',
            'weight' => 40,
            'max_score' => 100,
        ]);

        $response->assertSessionHasErrors(['weight']);
        $this->assertDatabaseMissing('grade_components', ['name' => 'UAS']);
    }

    public function test_teacher_cannot_create_component_for_unassigned_class(): void
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

        $response = $this->actingAs($otherTeacherUser)->post(route('teacher.grade-components.store'), [
            'class_subject_id' => $this->classSubject->id,
            'name' => 'Tugas Ilegal',
            'weight' => 20,
        ]);

        $response->assertForbidden();
    }

    public function test_teacher_can_update_grade_component(): void
    {
        $component = GradeComponent::create([
            'class_subject_id' => $this->classSubject->id,
            'name' => 'Tugas Awal',
            'weight' => 20,
            'max_score' => 100,
        ]);

        $response = $this->actingAs($this->teacherUser)->put(route('teacher.grade-components.update', $component->id), [
            'name' => 'Tugas Terupdate',
            'weight' => 30,
            'max_score' => 100,
        ]);

        $response->assertRedirect(route('teacher.grade-components.index', ['class_subject_id' => $this->classSubject->id]));

        $this->assertDatabaseHas('grade_components', [
            'id' => $component->id,
            'name' => 'Tugas Terupdate',
            'weight' => 30.00,
        ]);

        $this->assertDatabaseHas('activity_logs', [
            'user_id' => $this->teacherUser->id,
            'action' => 'update',
            'entity_type' => 'GradeComponent',
        ]);
    }

    public function test_teacher_can_delete_grade_component(): void
    {
        $component = GradeComponent::create([
            'class_subject_id' => $this->classSubject->id,
            'name' => 'Tugas Dihapus',
            'weight' => 20,
            'max_score' => 100,
        ]);

        $response = $this->actingAs($this->teacherUser)->delete(route('teacher.grade-components.destroy', $component->id));
        $response->assertRedirect(route('teacher.grade-components.index', ['class_subject_id' => $this->classSubject->id]));

        $this->assertDatabaseMissing('grade_components', ['id' => $component->id]);
    }

    public function test_teacher_can_view_grade_input_page(): void
    {
        $component = GradeComponent::create([
            'class_subject_id' => $this->classSubject->id,
            'name' => 'Tugas 1',
            'weight' => 20,
            'max_score' => 100,
        ]);

        $response = $this->actingAs($this->teacherUser)->get(route('teacher.grades.index', $component->id));
        $response->assertOk();
        $response->assertSee('Input Nilai: Tugas 1');
        $response->assertSee($this->studentUser->name);
    }

    public function test_teacher_can_input_and_update_student_grades(): void
    {
        $component = GradeComponent::create([
            'class_subject_id' => $this->classSubject->id,
            'name' => 'Tugas 1',
            'weight' => 20,
            'max_score' => 100,
        ]);

        $response = $this->actingAs($this->teacherUser)->post(route('teacher.grades.store', $component->id), [
            'grades' => [
                [
                    'student_id' => $this->student->id,
                    'score' => 88.5,
                    'feedback' => 'Penjelasan sangat komprehensif',
                ],
            ],
        ]);

        $response->assertRedirect(route('teacher.grades.index', $component->id));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('grades', [
            'grade_component_id' => $component->id,
            'student_id' => $this->student->id,
            'score' => 88.50,
            'feedback' => 'Penjelasan sangat komprehensif',
        ]);
    }

    public function test_grade_validation_score_must_be_between_0_and_100(): void
    {
        $component = GradeComponent::create([
            'class_subject_id' => $this->classSubject->id,
            'name' => 'Tugas 1',
            'weight' => 20,
            'max_score' => 100,
        ]);

        $response = $this->actingAs($this->teacherUser)->post(route('teacher.grades.store', $component->id), [
            'grades' => [
                [
                    'student_id' => $this->student->id,
                    'score' => 120, // exceeds 100
                ],
            ],
        ]);

        $response->assertSessionHasErrors(['grades.0.score']);
    }

    public function test_student_can_view_own_grades_and_final_score(): void
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
            'score' => 92,
            'feedback' => 'Pertahankan prestasimu',
            'graded_by' => $this->teacherUser->id,
        ]);

        $response = $this->actingAs($this->studentUser)->get(route('student.grades.index'));
        $response->assertOk();
        $response->assertSee('Transkrip Nilai Akademik Siswa');
        $response->assertSee('Ilmu Pengetahuan Alam');
        $response->assertSee('92');
        $response->assertSee('Tuntas');
        $response->assertSee('Pertahankan prestasimu');
    }
}
