<?php

namespace Tests\Feature\Teacher;

use App\Models\AcademicYear;
use App\Models\ClassSubject;
use App\Models\Role;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\StudentEnrollment;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GroupGeneratorTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);
    }

    public function test_teacher_can_view_group_generator_page(): void
    {
        $teacherRole = Role::where('name', 'teacher')->first();
        $teacherUser = User::factory()->create(['role_id' => $teacherRole->id]);
        Teacher::create(['user_id' => $teacherUser->id, 'nip' => '12345678']);

        $response = $this->actingAs($teacherUser)->get('/teacher/group-generator');

        $response->assertStatus(200);
        $response->assertSee('Generator Acak Kelompok');
    }

    public function test_teacher_can_generate_groups_for_class(): void
    {
        $teacherRole = Role::where('name', 'teacher')->first();
        $teacherUser = User::factory()->create(['role_id' => $teacherRole->id]);
        $teacher = Teacher::create(['user_id' => $teacherUser->id, 'nip' => '12345678']);

        $year = AcademicYear::create([
            'name' => '2026/2027',
            'semester' => 'odd',
            'start_date' => '2026-07-01',
            'end_date' => '2026-12-31',
            'is_active' => true,
        ]);

        $class = SchoolClass::create(['name' => 'VII-A', 'grade_level' => '7']);
        $subject = Subject::create(['code' => 'MAT-7', 'name' => 'Matematika']);

        $cs = ClassSubject::create([
            'class_id' => $class->id,
            'subject_id' => $subject->id,
            'teacher_id' => $teacher->id,
            'academic_year_id' => $year->id,
        ]);

        $studentRole = Role::where('name', 'student')->first();
        for ($i = 1; $i <= 6; $i++) {
            $user = User::factory()->create(['role_id' => $studentRole->id]);
            $student = Student::create([
                'user_id' => $user->id,
                'nis' => '20260730'.$i,
                'gender' => $i % 2 === 0 ? 'female' : 'male',
            ]);
            StudentEnrollment::create([
                'student_id' => $student->id,
                'class_id' => $class->id,
                'academic_year_id' => $year->id,
            ]);
        }

        $response = $this->actingAs($teacherUser)->get('/teacher/group-generator?generate=1&class_subject_id='.$cs->id.'&num_groups=2&balance_gender=1');

        $response->assertStatus(200);
        $response->assertSee('Kelompok 1');
        $response->assertSee('Kelompok 2');
    }
}
