<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Doctor>
 */
class DoctorFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'name' => $this->faker->name,
            'specialty' => $this->faker->randomElement(['Cardiology', 'Neurology', 'Orthopedics', 'Pediatrics', 'Internal Medicine']),
            'license_number' => 'CMP-' . $this->faker->unique()->randomNumber(6),
            'email' => $this->faker->unique()->safeEmail,
            'phone' => $this->faker->phoneNumber,
            'emergency_phone' => $this->faker->phoneNumber,
            'address' => $this->faker->address,
            'experience_years' => $this->faker->numberBetween(1, 30),
            'education' => [
                $this->faker->company . ' University - Medical Degree',
                $this->faker->company . ' Hospital - Residency'
            ],
            'certifications' => [
                'Board Certified in ' . $this->faker->randomElement(['Cardiology', 'Internal Medicine']),
                'Advanced Life Support Certification'
            ],
            'languages' => $this->faker->randomElements(['Spanish', 'English', 'French', 'Portuguese'], 2),
            'status' => $this->faker->randomElement(['active', 'inactive', 'vacation', 'leave']),
            'bio' => $this->faker->paragraph,
            'photo_url' => $this->faker->imageUrl(200, 200, 'people'),
            'rating' => $this->faker->randomFloat(2, 3.0, 5.0),
        ];
    }

    /**
     * Indicate that the doctor is active.
     */
    public function active()
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'active',
        ]);
    }
}
