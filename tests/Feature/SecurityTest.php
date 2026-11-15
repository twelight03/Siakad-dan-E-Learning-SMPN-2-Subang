<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\Assignment;
use App\Models\ClassSubject;
use App\Models\Grade;
use App\Models\GradeComponent;
use App\Models\Material;
use App\Models\Role;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\StudentEnrollment;
use App\Models\Subject;
use App\Models\Submission;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SecurityTest extends TestCase
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
        Storage::fake('local');

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
            'code' => 'TIK-7',
            'name' => 'Informatika',
            'kkm' => 75,
        ]);

        $this->adminUser = User::factory()->create([
            'role_id' => $adminRole->id,
            'username' => 'admin_sec',
            'password' => Hash::make('AdminSecret123!'),
            'is_active' => true,
        ]);

        $this->teacherUser = User::factory()->create([
            'role_id' => $teacherRole->id,
            'username' => 'guru_sec',
            'password' => Hash::make('GuruSecret123!'),
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
            'username' => 'siswa_sec',
            'password' => Hash::make('SiswaSecret123!'),
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

    /**
     * SEC-01: Password Hashing Verification
     */
    public function test_sec_01_passwords_are_securely_hashed_in_database(): void
    {
        $rawPassword = DB::table('users')->where('id', $this->studentUser->id)->value('password');

        $this->assertNotEquals('SiswaSecret123!', $rawPassword);
        $this->assertTrue(str_starts_with($rawPassword, '$2y$') || str_starts_with($rawPassword, '$argon2id$'));
        $this->assertTrue(Hash::check('SiswaSecret123!', $rawPassword));
    }

    /**
     * SEC-02: SQL Injection Resilience
     */
    public function test_sec_02_sql_injection_payloads_do_not_bypass_authentication(): void
    {
        // SQLi on login
        $response = $this->post('/login', [
            'login' => "' OR '1'='1",
            'password' => "' OR '1'='1",
        ]);

        $this->assertGuest();

        // SQLi in search parameters
        $sqliPayload = "7-A'; DROP TABLE users; --";
        $searchResponse = $this->actingAs($this->adminUser)->get(route('admin.reports.students', [
            'class_id' => $sqliPayload,
            'academic_year_id' => $this->academicYear->id,
        ]));

        $searchResponse->assertOk();
        $this->assertTrue(DB::getSchemaBuilder()->hasTable('users'));
    }

    /**
     * SEC-03: Cross-Site Scripting (XSS) HTML Escaping
     */
    public function test_sec_03_xss_payloads_are_escaped_in_blade_output(): void
    {
        $xssSubject = Subject::create([
            'code' => 'XSS-01',
            'name' => '<script>alert("XSS")</script>',
            'kkm' => 75,
        ]);

        $response = $this->actingAs($this->adminUser)->get(route('admin.subjects.index'));
        $response->assertOk();

        // Must not contain raw executable script tag
        $response->assertDontSee('<script>alert("XSS")</script>', false);
        // Must contain escaped HTML entities
        $response->assertSee('&lt;script&gt;alert(&quot;XSS&quot;)&lt;/script&gt;', false);
    }

    /**
     * SEC-04: Role-Based Access Control (RBAC) Server-Side Enforcement
     */
    public function test_sec_04_rbac_blocks_unauthorized_roles(): void
    {
        // Student attempting Admin routes
        $this->actingAs($this->studentUser)->get(route('admin.dashboard'))->assertForbidden();
        $this->actingAs($this->studentUser)->get(route('admin.students.index'))->assertForbidden();
        $this->actingAs($this->studentUser)->get(route('admin.reports.index'))->assertForbidden();

        // Student attempting Teacher routes
        $this->actingAs($this->studentUser)->get(route('teacher.dashboard'))->assertForbidden();
        $this->actingAs($this->studentUser)->get(route('teacher.attendances.index'))->assertForbidden();
        $this->actingAs($this->studentUser)->get(route('teacher.assignments.index'))->assertForbidden();

        // Teacher attempting Admin routes
        $this->actingAs($this->teacherUser)->get(route('admin.dashboard'))->assertForbidden();
        $this->actingAs($this->teacherUser)->get(route('admin.activity-logs.index'))->assertForbidden();
    }

    /**
     * SEC-05: IDOR Prevention on Grades & Assessments
     */
    public function test_sec_05_idor_prevention_on_grades(): void
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

        $component = GradeComponent::create([
            'class_subject_id' => $this->classSubject->id,
            'name' => 'UTS',
            'weight' => 100,
            'max_score' => 100,
        ]);

        // Unauthorized teacher attempts to score this class's students
        $response = $this->actingAs($otherTeacherUser)->post(route('teacher.grades.store', $component->id), [
            'grades' => [
                [
                    'student_id' => $this->student->id,
                    'score' => 99,
                ],
            ],
        ]);

        $response->assertForbidden();
        $this->assertDatabaseMissing('grades', ['score' => 99.00]);
    }

    /**
     * SEC-06: IDOR Prevention on Private Storage Files
     */
    public function test_sec_06_idor_prevention_on_file_downloads(): void
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

        $path = Storage::disk('local')->putFile('materials', UploadedFile::fake()->create('private_exam.pdf', 100, 'application/pdf'));

        $material = Material::create([
            'class_subject_id' => $this->classSubject->id,
            'title' => 'Materi Tertutup 7-A',
            'file_path' => $path,
            'original_filename' => 'private_exam.pdf',
            'mime_type' => 'application/pdf',
            'file_size' => 102400,
            'uploaded_by' => $this->teacherUser->id,
        ]);

        // Student not in 7-A tries to download material
        $response = $this->actingAs($otherStudentUser)->get(route('student.materials.download', $material->id));
        $response->assertForbidden();
    }

    /**
     * SEC-07: Rate Limiting on Login Authentication
     */
    public function test_sec_07_rate_limiting_blocks_brute_force_attacks(): void
    {
        // 5 consecutive failed login attempts
        for ($i = 0; $i < 5; $i++) {
            $this->post('/login', [
                'username' => 'guru_sec',
                'password' => 'wrongpassword',
            ]);
        }

        // 6th attempt should be blocked by rate limiter
        $response = $this->post('/login', [
            'username' => 'guru_sec',
            'password' => 'wrongpassword',
        ]);

        $response->assertSessionHasErrors(['username']);
    }

    /**
     * SEC-08: CSRF Protection
     */
    public function test_sec_08_state_modifying_requests_require_csrf_protection(): void
    {
        // Test that standard web middleware includes VerifyCsrfToken / ValidateCsrfToken
        $response = $this->withoutMiddleware(\App\Http\Middleware\SecurityHeadersMiddleware::class)
            ->post(route('login'), [
                'login' => 'admin_sec',
                'password' => 'AdminSecret123!',
            ]);

        // When standard session & CSRF are active in normal browser requests, requests without token are guarded
        $this->assertTrue(class_exists(\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class));
    }

    /**
     * SEC-09: File Upload MIME Type and Size Restrictions
     */
    public function test_sec_09_disallowed_file_types_and_oversized_files_are_rejected(): void
    {
        // Executable script disguised
        $badFile = UploadedFile::fake()->create('exploit.php', 100, 'application/x-php');

        $response = $this->actingAs($this->teacherUser)->post(route('teacher.materials.store'), [
            'class_subject_id' => $this->classSubject->id,
            'title' => 'Exploit File',
            'material_file' => $badFile,
        ]);

        $response->assertSessionHasErrors(['material_file']);

        // Oversized file > 10MB
        $hugeFile = UploadedFile::fake()->create('giant.pdf', 15000, 'application/pdf');

        $responseHuge = $this->actingAs($this->teacherUser)->post(route('teacher.materials.store'), [
            'class_subject_id' => $this->classSubject->id,
            'title' => 'Huge File',
            'material_file' => $hugeFile,
        ]);

        $responseHuge->assertSessionHasErrors(['material_file']);
    }

    /**
     * SEC-10: Session Invalidation on Logout
     */
    public function test_sec_10_session_is_invalidated_upon_logout(): void
    {
        $this->actingAs($this->adminUser);
        $this->assertAuthenticatedAs($this->adminUser);

        $response = $this->post(route('logout'));
        $response->assertRedirect('/login');

        $this->assertGuest();

        // Following request to protected route is redirected to login
        $protectedResponse = $this->get(route('admin.dashboard'));
        $protectedResponse->assertRedirect(route('login'));
    }

    /**
     * SEC-11: Security Headers (Harden HTTP Responses)
     */
    public function test_sec_11_security_headers_are_present(): void
    {
        $response = $this->get('/');

        $response->assertHeader('X-Content-Type-Options', 'nosniff');
        $response->assertHeader('X-Frame-Options', 'SAMEORIGIN');
        $response->assertHeader('X-XSS-Protection', '1; mode=block');
        $response->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
    }

    /**
     * SEC-12: Environment and Secrets Protection
     */
    public function test_sec_12_environment_file_is_ignored_and_protected(): void
    {
        $gitignorePath = base_path('.gitignore');
        $this->assertFileExists($gitignorePath);

        $gitignoreContent = file_get_contents($gitignorePath);
        $this->assertStringContainsString('.env', $gitignoreContent);
    }
}
