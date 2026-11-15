<?php

namespace Tests\Feature\Admin;

use App\Models\AcademicYear;
use App\Models\ClassSubject;
use App\Models\SchoolClass;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClassSubjectAssignmentTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_class_subjects_list(): void
    {
        $admin = User::factory()->admin()->create();
        $year = AcademicYear::create([
            'name' => '2026/2027',
            'semester' => 'odd',
            'start_date' => '2026-07-01',
            'end_date' => '2026-12-31',
            'is_active' => true,
        ]);
        $class = SchoolClass::create(['name' => 'VII-A', 'grade_level' => '7']);
        $subject = Subject::create(['code' => 'BIG-7', 'name' => 'Bahasa Inggris']);
        $teacherUser = User::factory()->teacher()->create(['name' => 'Mrs. Smith']);
        $teacher = Teacher::create(['user_id' => $teacherUser->id, 'nip' => '334455']);

        ClassSubject::create([
            'class_id' => $class->id,
            'subject_id' => $subject->id,
            'teacher_id' => $teacher->id,
            'academic_year_id' => $year->id,
        ]);

        $response = $this->actingAs($admin)->get('/admin/class-subjects');

        $response->assertStatus(200);
        $response->assertSee('BIG-7');
        $response->assertSee('Bahasa Inggris');
        $response->assertSee('Mrs. Smith');
    }

    public function test_non_admin_cannot_access_class_subjects(): void
    {
        $student = User::factory()->student()->create();

        $response = $this->actingAs($student)->get('/admin/class-subjects');

        $response->assertStatus(403);
    }

    public function test_admin_can_assign_teacher_to_class_subject(): void
    {
        $admin = User::factory()->admin()->create();
        $year = AcademicYear::create([
            'name' => '2026/2027',
            'semester' => 'odd',
            'start_date' => '2026-07-01',
            'end_date' => '2026-12-31',
            'is_active' => true,
        ]);
        $class = SchoolClass::create(['name' => 'VII-B', 'grade_level' => '7']);
        $subject = Subject::create(['code' => 'PKN-7', 'name' => 'Pendidikan Kewarganegaraan']);
        $teacherUser = User::factory()->teacher()->create();
        $teacher = Teacher::create(['user_id' => $teacherUser->id, 'nip' => '667788']);

        $response = $this->actingAs($admin)->post('/admin/class-subjects', [
            'class_id' => $class->id,
            'subject_id' => $subject->id,
            'teacher_id' => $teacher->id,
            'academic_year_id' => $year->id,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('class_subjects', [
            'class_id' => $class->id,
            'subject_id' => $subject->id,
            'teacher_id' => $teacher->id,
            'academic_year_id' => $year->id,
        ]);
    }

    public function test_cannot_assign_duplicate_subject_to_class_in_same_year(): void
    {
        $admin = User::factory()->admin()->create();
        $year = AcademicYear::create([
            'name' => '2026/2027',
            'semester' => 'odd',
            'start_date' => '2026-07-01',
            'end_date' => '2026-12-31',
            'is_active' => true,
        ]);
        $class = SchoolClass::create(['name' => 'VII-C', 'grade_level' => '7']);
        $subject = Subject::create(['code' => 'MAT-7', 'name' => 'Matematika']);

        $teacherUser1 = User::factory()->teacher()->create();
        $teacher1 = Teacher::create(['user_id' => $teacherUser1->id, 'nip' => '1111']);

        $teacherUser2 = User::factory()->teacher()->create();
        $teacher2 = Teacher::create(['user_id' => $teacherUser2->id, 'nip' => '2222']);

        // First assignment
        ClassSubject::create([
            'class_id' => $class->id,
            'subject_id' => $subject->id,
            'teacher_id' => $teacher1->id,
            'academic_year_id' => $year->id,
        ]);

        // Second assignment attempt with different teacher for the same class and subject in the same year
        $response = $this->actingAs($admin)->post('/admin/class-subjects', [
            'class_id' => $class->id,
            'subject_id' => $subject->id,
            'teacher_id' => $teacher2->id,
            'academic_year_id' => $year->id,
        ]);

        $response->assertSessionHasErrors('subject_id');
        $this->assertEquals(1, ClassSubject::where('class_id', $class->id)->where('subject_id', $subject->id)->where('academic_year_id', $year->id)->count());
    }

    public function test_admin_can_update_class_subject_assignment(): void
    {
        $admin = User::factory()->admin()->create();
        $year = AcademicYear::create([
            'name' => '2026/2027',
            'semester' => 'odd',
            'start_date' => '2026-07-01',
            'end_date' => '2026-12-31',
            'is_active' => true,
        ]);
        $class = SchoolClass::create(['name' => 'VII-D', 'grade_level' => '7']);
        $subject = Subject::create(['code' => 'IPA-7', 'name' => 'IPA']);

        $teacherUser1 = User::factory()->teacher()->create();
        $teacher1 = Teacher::create(['user_id' => $teacherUser1->id, 'nip' => '3333']);

        $teacherUser2 = User::factory()->teacher()->create();
        $teacher2 = Teacher::create(['user_id' => $teacherUser2->id, 'nip' => '4444']);

        $cs = ClassSubject::create([
            'class_id' => $class->id,
            'subject_id' => $subject->id,
            'teacher_id' => $teacher1->id,
            'academic_year_id' => $year->id,
        ]);

        $response = $this->actingAs($admin)->put("/admin/class-subjects/{$cs->id}", [
            'class_id' => $class->id,
            'subject_id' => $subject->id,
            'teacher_id' => $teacher2->id, // reassign to teacher 2
            'academic_year_id' => $year->id,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('class_subjects', [
            'id' => $cs->id,
            'teacher_id' => $teacher2->id,
        ]);
    }

    public function test_admin_can_delete_unreferenced_class_subject(): void
    {
        $admin = User::factory()->admin()->create();
        $year = AcademicYear::create([
            'name' => '2026/2027',
            'semester' => 'odd',
            'start_date' => '2026-07-01',
            'end_date' => '2026-12-31',
            'is_active' => true,
        ]);
        $class = SchoolClass::create(['name' => 'VII-E', 'grade_level' => '7']);
        $subject = Subject::create(['code' => 'IPS-7', 'name' => 'IPS']);
        $teacherUser = User::factory()->teacher()->create();
        $teacher = Teacher::create(['user_id' => $teacherUser->id, 'nip' => '5555']);

        $cs = ClassSubject::create([
            'class_id' => $class->id,
            'subject_id' => $subject->id,
            'teacher_id' => $teacher->id,
            'academic_year_id' => $year->id,
        ]);

        $response = $this->actingAs($admin)->delete("/admin/class-subjects/{$cs->id}");

        $response->assertRedirect();
        $this->assertDatabaseMissing('class_subjects', ['id' => $cs->id]);
    }
}
