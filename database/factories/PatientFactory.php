<?php

namespace Database\Factories;

use App\Models\Clinic;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Patient>
 */
class PatientFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name,
            'dni' => $this->faker->unique()->numerify('########'),
            'birth_date' => $this->faker->dateTimeBetween('-80 years', '-18 years')->format('Y-m-d'),
            'gender' => $this->faker->randomElement(['male', 'female', 'other']),
            'blood_type' => $this->faker->randomElement(['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-']),
            'address' => $this->faker->address,
            'phone' => $this->faker->phoneNumber,
            'email' => $this->faker->optional()->safeEmail,
            'status' => $this->faker->randomElement(['active', 'inactive']),
            'preferred_clinic_id' => null, // Will be set by the test if needed
        ];
    }

    /**
     * Indicate that the patient is active.
     */
    public function active()
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'active',
        ]);
    }

    /**
     * Indicate that the patient has a preferred clinic.
     */
    public function withClinic()
    {
        return $this->state(fn (array $attributes) => [
            'preferred_clinic_id' => Clinic::factory(),
        ]);
    }
}
