<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
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
        $firstName = fake()->firstName();
        $lastName = fake()->lastName();
        $username = strtolower($firstName . '.' . $lastName . '.' . fake()->unique()->numberBetween(100, 999));

        return [
            'username' => $username,
            'email' => fake()->unique()->safeEmail(),
            'password' => static::$password ??= Hash::make('password'),
            'full_name' => $firstName . ' ' . $lastName,
            'phone' => fake()->phoneNumber(),
            'user_type' => 'staff',
            'parent_user_id' => null,
            'status' => 'active',
            'created_by' => null,
            'last_login' => null,
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the user is a super admin.
     */
    public function superAdmin(): static
    {
        return $this->state(fn (array $attributes) => [
            'user_type' => 'super_admin',
            'parent_user_id' => null,
            'status' => 'active',
        ]);
    }

    /**
     * Indicate that the user is a hotel owner.
     */
    public function hotelOwner(): static
    {
        return $this->state(fn (array $attributes) => [
            'user_type' => 'hotel_owner',
            'parent_user_id' => null,
            'status' => 'active',
        ]);
    }

    /**
     * Indicate that the user is staff.
     */
    public function staff(?int $parentUserId = null): static
    {
        return $this->state(fn (array $attributes) => [
            'user_type' => 'staff',
            'parent_user_id' => $parentUserId,
            'status' => 'active',
        ]);
    }

    /**
     * Indicate that the user is suspended.
     */
    public function suspended(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'suspended',
        ]);
    }

    /**
     * Indicate that the user is deleted.
     */
    public function deleted(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'deleted',
        ]);
    }

    /**
     * Set the parent user (hotel owner) for staff.
     */
    public function withParent(User $parent): static
    {
        return $this->state(fn (array $attributes) => [
            'parent_user_id' => $parent->id,
            'created_by' => $parent->id,
        ]);
    }

    /**
     * Set who created this user.
     */
    public function createdBy(User $creator): static
    {
        return $this->state(fn (array $attributes) => [
            'created_by' => $creator->id,
        ]);
    }
}
