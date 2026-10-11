<?php

namespace Database\Factories;

use App\Models\PortalInvitation;
use App\Models\School;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<PortalInvitation>
 */
class PortalInvitationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'school_id' => School::factory(), 'created_by' => User::factory(),
            'type' => 'guardian', 'email' => fake()->unique()->safeEmail(),
            'token_hash' => hash('sha256', Str::random(64)), 'expires_at' => now()->addDays(7),
        ];
    }
}
