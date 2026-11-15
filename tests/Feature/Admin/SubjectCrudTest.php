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

class SubjectCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_subjects_list(): void
    {
        $admin = User::factory()->admin()->create();
        Subject::create(['code' => 'IPS-7', 'name' => 'Ilmu Pengetahuan Sosial']);

        $response = $this->actingAs($admin)->get('/admin/subjects');

        $response->assertStatus(200);
        $response->assertSee('IPS-7');
        $response->assertSee('Ilmu Pengetahuan Sosial');
    }

    public function test_non_admin_cannot_access_subjects(): void
    {
        $student = User::factory()->student()->create();

        $response = $this->actingAs($student)->get('/admin/subjects');

        $response->assertStatus(403);
    }

    public function test_admin_can_create_subject(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->post('/admin/subjects', [
            'code' => 'PJK-7',
            'name' => 'Pendidikan Jasmani dan Kesehatan',
            'description' => 'Materi olahraga dan kebugaran jasmani.',
        ]);

        $response->assertRedirect('/admin/subjects');
        $this->assertDatabaseHas('subjects', [
            'code' => 'PJK-7',
            'name' => 'Pendidikan Jasmani dan Kesehatan',
        ]);
    }

    public function test_subject_code_must_be_unique(): void
    {
        $admin = User::factory()->admin()->create();
        Subject::create(['code' => 'SBK-7', 'name' => 'Seni Budaya']);

        $response = $this->actingAs($admin)->post('/admin/subjects', [
            'code' => 'SBK-7', // duplicate
            'name' => 'Seni Rupa',
        ]);

        $response->assertSessionHasErrors('code');
    }

    public function test_admin_can_update_subject(): void
    {
        $admin = User::factory()->admin()->create();
        $subject = Subject::create(['code' => 'TIK-7', 'name' => 'Informatika']);

        $response = $this->actingAs($admin)->put("/admin/subjects/{$subject->id}", [
            'code' => 'INF-7',
            'name' => 'Informatika & Komputer',
        ]);

        $response->assertRedirect('/admin/subjects');
        $this->assertDatabaseHas('subjects', [
            'id' => $subject->id,
            'code' => 'INF-7',
            'name' => 'Informatika & Komputer',
        ]);
    }

    public function test_admin_can_delete_unreferenced_subject(): void
    {
        $admin = User::factory()->admin()->create();
        $subject = Subject::create(['code' => 'PRA-7', 'name' => 'Prakarya']);

        $response = $this->actingAs($admin)->delete("/admin/subjects/{$subject->id}");

        $response->assertRedirect('/admin/subjects');
        $this->assertDatabaseMissing('subjects', ['id' => $subject->id]);
    }

    public function test_cannot_delete_subject_assigned_to_class(): void
    {
        $admin = User::factory()->admin()->create();
        $subject = Subject::create(['code' => 'PAI-7', 'name' => 'Pendidikan Agama Islam']);

        $year = AcademicYear::create([
            'name' => '2026/2027',
            'semester' => 'odd',
            'start_date' => '2026-07-01',
            'end_date' => '2026-12-31',
            'is_active' => true,
        ]);
        $class = SchoolClass::create(['name' => 'VII-A', 'grade_level' => '7']);
        $teacherUser = User::factory()->teacher()->create();
        $teacher = Teacher::create(['user_id' => $teacherUser->id, 'nip' => '55443322']);

        ClassSubject::create([
            'class_id' => $class->id,
            'subject_id' => $subject->id,
            'teacher_id' => $teacher->id,
            'academic_year_id' => $year->id,
        ]);

        $response = $this->actingAs($admin)->delete("/admin/subjects/{$subject->id}");

        $response->assertRedirect('/admin/subjects');
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('subjects', ['id' => $subject->id]);
    }
}
