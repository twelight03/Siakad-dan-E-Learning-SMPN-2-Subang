<?php

namespace Tests\Feature\Admin;

use App\Models\AcademicYear;
use App\Models\ClassSubject;
use App\Models\Role;
use App\Models\Schedule;
use App\Models\SchoolClass;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ScheduleGeneratorTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);
    }

    public function test_admin_can_view_auto_generator_page(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->get('/admin/schedules/auto-generate');

        $response->assertStatus(200);
        $response->assertSee('Generator Jadwal Otomatis');
    }

    public function test_admin_can_auto_generate_schedules_without_conflicts(): void
    {
        $admin = User::factory()->admin()->create();

        $year = AcademicYear::create([
            'name' => '2026/2027',
            'semester' => 'odd',
            'start_date' => '2026-07-01',
            'end_date' => '2026-12-31',
            'is_active' => true,
        ]);

        $class7A = SchoolClass::create(['name' => 'VII-A', 'grade_level' => '7']);
        $class8A = SchoolClass::create(['name' => 'VIII-A', 'grade_level' => '8']);

        $subjectMath = Subject::create(['code' => 'MAT-7', 'name' => 'Matematika']);
        $subjectIpa = Subject::create(['code' => 'IPA-7', 'name' => 'IPA']);

        $teacherRole = Role::where('name', 'teacher')->first();

        $userTeacher1 = User::factory()->create(['role_id' => $teacherRole->id]);
        $teacher1 = Teacher::create(['user_id' => $userTeacher1->id, 'nip' => '1111', 'max_hours_per_week' => 24]);

        $userTeacher2 = User::factory()->create(['role_id' => $teacherRole->id]);
        $teacher2 = Teacher::create(['user_id' => $userTeacher2->id, 'nip' => '2222', 'max_hours_per_week' => 24]);

        $cs1 = ClassSubject::create([
            'class_id' => $class7A->id,
            'subject_id' => $subjectMath->id,
            'teacher_id' => $teacher1->id,
            'academic_year_id' => $year->id,
        ]);

        $cs2 = ClassSubject::create([
            'class_id' => $class8A->id,
            'subject_id' => $subjectIpa->id,
            'teacher_id' => $teacher2->id,
            'academic_year_id' => $year->id,
        ]);

        $response = $this->actingAs($admin)->post('/admin/schedules/auto-generate', [
            'academic_year_id' => $year->id,
            'class_ids' => [$class7A->id, $class8A->id],
            'clear_existing' => 1,
            'enforce_teacher_limits' => 1,
            'sessions_per_subject' => 1,
        ]);

        $response->assertRedirect(route('admin.schedules.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('schedules', [
            'class_subject_id' => $cs1->id,
        ]);
        $this->assertDatabaseHas('schedules', [
            'class_subject_id' => $cs2->id,
        ]);

        // Verify schedules are created with standard school start time
        $sched7 = Schedule::where('class_subject_id', $cs1->id)->first();
        $sched8 = Schedule::where('class_subject_id', $cs2->id)->first();

        $this->assertNotNull($sched7);
        $this->assertNotNull($sched8);
        $this->assertEquals('07:00', substr($sched7->start_time, 0, 5));
        $this->assertEquals('07:00', substr($sched8->start_time, 0, 5));
    }

    public function test_auto_generator_respects_teacher_max_hours_limit(): void
    {
        $admin = User::factory()->admin()->create();

        $year = AcademicYear::create([
            'name' => '2026/2027',
            'semester' => 'odd',
            'start_date' => '2026-07-01',
            'end_date' => '2026-12-31',
            'is_active' => true,
        ]);

        $class7A = SchoolClass::create(['name' => 'VII-A', 'grade_level' => '7']);
        $subject1 = Subject::create(['code' => 'SBJ-1', 'name' => 'Mapel Satu']);
        $subject2 = Subject::create(['code' => 'SBJ-2', 'name' => 'Mapel Dua']);

        $teacherRole = Role::where('name', 'teacher')->first();
        $userTeacher = User::factory()->create(['role_id' => $teacherRole->id]);

        // Teacher limit set to 2 hours only (allows only 1 session)
        $teacher = Teacher::create([
            'user_id' => $userTeacher->id,
            'nip' => '3333',
            'max_hours_per_week' => 2,
        ]);

        $cs1 = ClassSubject::create([
            'class_id' => $class7A->id,
            'subject_id' => $subject1->id,
            'teacher_id' => $teacher->id,
            'academic_year_id' => $year->id,
        ]);

        $cs2 = ClassSubject::create([
            'class_id' => $class7A->id,
            'subject_id' => $subject2->id,
            'teacher_id' => $teacher->id,
            'academic_year_id' => $year->id,
        ]);

        $response = $this->actingAs($admin)->post('/admin/schedules/auto-generate', [
            'academic_year_id' => $year->id,
            'class_ids' => [$class7A->id],
            'clear_existing' => 1,
            'enforce_teacher_limits' => 1,
            'sessions_per_subject' => 1,
        ]);

        $response->assertRedirect(route('admin.schedules.index'));
        $response->assertSessionHas('generator_warnings');

        // Only 1 schedule created because of max 2 hours limit
        $this->assertEquals(1, Schedule::whereIn('class_subject_id', [$cs1->id, $cs2->id])->count());
    }
}
