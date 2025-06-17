<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Clinic>
 */
class ClinicFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->company . ' Medical Center',
            'address' => $this->faker->address,
            'phone' => $this->faker->phoneNumber,
            'email' => $this->faker->companyEmail,
            'medical_director' => $this->faker->name,
            'foundation_year' => $this->faker->numberBetween(1950, 2020),
            'specialties' => [
                $this->faker->randomElement(['Cardiology', 'Neurology', 'Orthopedics']),
                $this->faker->randomElement(['Pediatrics', 'Gynecology', 'Dermatology']),
                $this->faker->randomElement(['Internal Medicine', 'Emergency Medicine', 'Radiology'])
            ],
            'schedule' => [
                'monday' => ['start' => '08:00', 'end' => '18:00'],
                'tuesday' => ['start' => '08:00', 'end' => '18:00'],
                'wednesday' => ['start' => '08:00', 'end' => '18:00'],
                'thursday' => ['start' => '08:00', 'end' => '18:00'],
                'friday' => ['start' => '08:00', 'end' => '18:00'],
                'saturday' => ['start' => '08:00', 'end' => '14:00'],
            ],
            'emergency_services' => $this->faker->boolean(70),
            'status' => $this->faker->randomElement(['active', 'inactive', 'maintenance']),
            'description' => $this->faker->paragraph,
        ];
    }

    /**
     * Indicate that the clinic is active.
     */
    public function active()
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'active',
        ]);
    }

    /**
     * Indicate that the clinic has emergency services.
     */
    public function withEmergency()
    {
        return $this->state(fn (array $attributes) => [
            'emergency_services' => true,
        ]);
    }
}
