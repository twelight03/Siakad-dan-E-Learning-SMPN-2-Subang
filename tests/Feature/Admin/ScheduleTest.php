<?php

namespace Tests\Feature\Admin;

use App\Models\AcademicYear;
use App\Models\ClassSubject;
use App\Models\Schedule;
use App\Models\SchoolClass;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ScheduleTest extends TestCase
{
    use RefreshDatabase;

    private function setupAcademicData()
    {
        $year = AcademicYear::create([
            'name' => '2026/2027',
            'semester' => 'odd',
            'start_date' => '2026-07-01',
            'end_date' => '2026-12-31',
            'is_active' => true,
        ]);
        $classA = SchoolClass::create(['name' => 'VII-A', 'grade_level' => '7']);
        $classB = SchoolClass::create(['name' => 'VII-B', 'grade_level' => '7']);

        $subjectMath = Subject::create(['code' => 'MAT-7', 'name' => 'Matematika']);
        $subjectIpa = Subject::create(['code' => 'IPA-7', 'name' => 'IPA']);

        $teacherUser1 = User::factory()->teacher()->create(['name' => 'Pak Budi']);
        $teacher1 = Teacher::create(['user_id' => $teacherUser1->id, 'nip' => '1111']);

        $teacherUser2 = User::factory()->teacher()->create(['name' => 'Bu Siti']);
        $teacher2 = Teacher::create(['user_id' => $teacherUser2->id, 'nip' => '2222']);

        $csMath7A = ClassSubject::create([
            'class_id' => $classA->id,
            'subject_id' => $subjectMath->id,
            'teacher_id' => $teacher1->id,
            'academic_year_id' => $year->id,
        ]);

        $csIpa7A = ClassSubject::create([
            'class_id' => $classA->id,
            'subject_id' => $subjectIpa->id,
            'teacher_id' => $teacher2->id,
            'academic_year_id' => $year->id,
        ]);

        $csMath7B = ClassSubject::create([
            'class_id' => $classB->id,
            'subject_id' => $subjectMath->id,
            'teacher_id' => $teacher1->id,
            'academic_year_id' => $year->id,
        ]);

        return compact('year', 'classA', 'classB', 'subjectMath', 'subjectIpa', 'teacher1', 'teacher2', 'csMath7A', 'csIpa7A', 'csMath7B');
    }

    public function test_admin_can_view_schedules_list(): void
    {
        $admin = User::factory()->admin()->create();
        $data = $this->setupAcademicData();

        Schedule::create([
            'class_subject_id' => $data['csMath7A']->id,
            'day_of_week' => 'monday',
            'start_time' => '07:30',
            'end_time' => '09:00',
            'room' => '7-A',
        ]);

        $response = $this->actingAs($admin)->get('/admin/schedules');

        $response->assertStatus(200);
        $response->assertSee('Matematika');
        $response->assertSee('VII-A');
        $response->assertSee('07:30');
    }

    public function test_non_admin_cannot_access_admin_schedules(): void
    {
        $student = User::factory()->student()->create();

        $response = $this->actingAs($student)->get('/admin/schedules');

        $response->assertStatus(403);
    }

    public function test_admin_can_create_valid_schedule(): void
    {
        $admin = User::factory()->admin()->create();
        $data = $this->setupAcademicData();

        $response = $this->actingAs($admin)->post('/admin/schedules', [
            'class_subject_id' => $data['csMath7A']->id,
            'day_of_week' => 'monday',
            'start_time' => '07:30',
            'end_time' => '09:00',
            'room' => 'Ruang 7-A',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('schedules', [
            'class_subject_id' => $data['csMath7A']->id,
            'day_of_week' => 'monday',
            'start_time' => '07:30:00',
            'end_time' => '09:00:00',
        ]);
    }

    public function test_schedule_rejected_when_end_time_is_not_after_start_time(): void
    {
        $admin = User::factory()->admin()->create();
        $data = $this->setupAcademicData();

        $response = $this->actingAs($admin)->post('/admin/schedules', [
            'class_subject_id' => $data['csMath7A']->id,
            'day_of_week' => 'monday',
            'start_time' => '09:00',
            'end_time' => '08:00', // invalid: before start
        ]);

        $response->assertSessionHasErrors('end_time');
    }

    public function test_schedule_rejected_when_class_schedule_conflicts(): void
    {
        $admin = User::factory()->admin()->create();
        $data = $this->setupAcademicData();

        // Existing schedule for Class VII-A: Monday 07:30 - 09:00 (Math)
        Schedule::create([
            'class_subject_id' => $data['csMath7A']->id,
            'day_of_week' => 'monday',
            'start_time' => '07:30',
            'end_time' => '09:00',
        ]);

        // Attempt overlapping schedule for Class VII-A: Monday 08:30 - 10:00 (IPA)
        $response = $this->actingAs($admin)->post('/admin/schedules', [
            'class_subject_id' => $data['csIpa7A']->id,
            'day_of_week' => 'monday',
            'start_time' => '08:30',
            'end_time' => '10:00',
        ]);

        $response->assertSessionHasErrors('start_time');
        $this->assertEquals(1, Schedule::count());
    }

    public function test_schedule_rejected_when_teacher_schedule_conflicts(): void
    {
        $admin = User::factory()->admin()->create();
        $data = $this->setupAcademicData();

        // Teacher 1 (Pak Budi) is already teaching Class VII-A on Monday 07:30 - 09:00
        Schedule::create([
            'class_subject_id' => $data['csMath7A']->id,
            'day_of_week' => 'monday',
            'start_time' => '07:30',
            'end_time' => '09:00',
        ]);

        // Attempt scheduling Teacher 1 for Class VII-B on Monday 08:00 - 09:30
        $response = $this->actingAs($admin)->post('/admin/schedules', [
            'class_subject_id' => $data['csMath7B']->id,
            'day_of_week' => 'monday',
            'start_time' => '08:00',
            'end_time' => '09:30',
        ]);

        $response->assertSessionHasErrors('start_time');
        $this->assertEquals(1, Schedule::count());
    }

    public function test_admin_can_update_schedule(): void
    {
        $admin = User::factory()->admin()->create();
        $data = $this->setupAcademicData();

        $schedule = Schedule::create([
            'class_subject_id' => $data['csMath7A']->id,
            'day_of_week' => 'monday',
            'start_time' => '07:30',
            'end_time' => '09:00',
        ]);

        $response = $this->actingAs($admin)->put("/admin/schedules/{$schedule->id}", [
            'class_subject_id' => $data['csMath7A']->id,
            'day_of_week' => 'tuesday', // move to Tuesday
            'start_time' => '08:00',
            'end_time' => '09:30',
            'room' => 'Ruang 7-A Baru',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('schedules', [
            'id' => $schedule->id,
            'day_of_week' => 'tuesday',
            'start_time' => '08:00:00',
            'room' => 'Ruang 7-A Baru',
        ]);
    }

    public function test_admin_can_delete_schedule(): void
    {
        $admin = User::factory()->admin()->create();
        $data = $this->setupAcademicData();

        $schedule = Schedule::create([
            'class_subject_id' => $data['csMath7A']->id,
            'day_of_week' => 'friday',
            'start_time' => '07:30',
            'end_time' => '08:30',
        ]);

        $response = $this->actingAs($admin)->delete("/admin/schedules/{$schedule->id}");

        $response->assertRedirect();
        $this->assertDatabaseMissing('schedules', ['id' => $schedule->id]);
    }
}
