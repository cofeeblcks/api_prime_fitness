<?php

namespace Database\Factories;

use App\Constants\StatusesConstants;
use App\Enums\RoleEnum;
use App\Enums\SexEnum;
use App\Models\IdentificationType;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

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
        // Generar un rol aleatorio (excluyendo ADMIN que es el 1)
        $roleId = $this->faker->randomElement([
            RoleEnum::RECEPTIONIST->value,
            RoleEnum::TRAINING->value,
            RoleEnum::MEMBER->value,
        ]);

        static $identificationTypeIds;

        if (!$identificationTypeIds) $identificationTypeIds = IdentificationType::pluck('id')->toArray();

        return [
            'first_name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName(),
            'email' => fake()->unique()->safeEmail(),
            'phone' => $this->faker->phoneNumber(),
            'birthdate' => $this->faker->dateTimeBetween('-80 years', '-18 years')->format('Y-m-d'),
            'sex' => $this->faker->randomElement(SexEnum::cases())->value,
            'identification' => $this->faker->numerify('##########'),
            'photo' => null,
            'height' => $this->faker->randomFloat(2, 1.50, 2.00),
            'role_id' => $roleId,
            'identification_type_id' => $this->faker->randomElement($identificationTypeIds),
            'status_id' => $this->faker->randomElement([
                StatusesConstants::ACTIVE,
                StatusesConstants::INACTIVE,
            ]),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'created_at' => now(),
            'updated_at' => now(),
        ];
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
