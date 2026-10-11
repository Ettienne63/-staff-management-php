<?php

namespace Database\Factories;

use App\Models\ChildRegistration;
use App\Models\School;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ChildRegistration>
 */
class ChildRegistrationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'school_id' => School::factory(), 'guardian_user_id' => User::factory(),
            'first_name' => fake()->firstName(), 'last_name' => fake()->lastName(),
            'date_of_birth' => fake()->dateTimeBetween('-18 years', '-6 years')->format('Y-m-d'),
            'grade' => fake()->numberBetween(1, 12), 'relationship' => 'parent', 'status' => 'pending',
        ];
    }
}
