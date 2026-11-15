<?php

namespace Database\Factories;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $role = Role::firstOrCreate(
            ['name' => 'student'],
            ['display_name' => 'Siswa']
        );

        return [
            'role_id' => $role->id,
            'name' => fake()->name(),
            'username' => fake()->unique()->userName(),
            'email' => fake()->unique()->safeEmail(),
            'password' => static::$password ??= Hash::make('password'),
            'is_active' => true,
            'email_verified_at' => now(),
            'remember_token' => Str::random(10),
        ];
    }

    public function admin(): static
    {
        return $this->state(fn () => [
            'role_id' => Role::firstOrCreate(['name' => 'admin'], ['display_name' => 'Administrator'])->id,
        ]);
    }

    public function teacher(): static
    {
        return $this->state(fn () => [
            'role_id' => Role::firstOrCreate(['name' => 'teacher'], ['display_name' => 'Guru'])->id,
        ]);
    }

    public function student(): static
    {
        return $this->state(fn () => [
            'role_id' => Role::firstOrCreate(['name' => 'student'], ['display_name' => 'Siswa'])->id,
        ]);
    }

    public function inactive(): static
    {
        return $this->state(fn () => [
            'is_active' => false,
        ]);
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
