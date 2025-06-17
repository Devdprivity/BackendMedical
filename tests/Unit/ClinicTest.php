<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase as BaseTestCase;
use App\Models\Clinic;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\User;

class ClinicTest extends BaseTestCase
{
    use RefreshDatabase;

    /**
     * A basic unit test example.
     */
    public function test_example(): void
    {
        $this->assertTrue(true);
    }

    public function test_clinic_can_be_created_with_valid_data()
    {
        $clinicData = [
            'name' => 'Test Medical Center',
            'address' => '123 Test Street',
            'phone' => '+1234567890',
            'email' => 'test@medical.com',
            'medical_director' => 'Dr. John Smith',
            'foundation_year' => 2000,
            'specialties' => ['Cardiology', 'Neurology'],
            'schedule' => [
                'monday' => ['start' => '08:00', 'end' => '18:00'],
                'tuesday' => ['start' => '08:00', 'end' => '18:00'],
            ],
            'emergency_services' => true,
            'status' => 'active',
            'description' => 'A test medical center',
        ];

        $clinic = Clinic::create($clinicData);

        $this->assertInstanceOf(Clinic::class, $clinic);
        $this->assertEquals('Test Medical Center', $clinic->name);
        $this->assertEquals('active', $clinic->status);
        $this->assertTrue($clinic->emergency_services);
        $this->assertIsArray($clinic->specialties);
        $this->assertContains('Cardiology', $clinic->specialties);
    }

    public function test_clinic_is_active_method()
    {
        $activeClinic = Clinic::factory()->active()->create();
        $inactiveClinic = Clinic::factory()->create(['status' => 'inactive']);

        $this->assertTrue($activeClinic->isActive());
        $this->assertFalse($inactiveClinic->isActive());
    }

    public function test_clinic_has_doctors_relationship()
    {
        $clinic = Clinic::factory()->create();
        $user = User::factory()->create();
        $doctor = Doctor::factory()->create(['user_id' => $user->id]);
        
        $clinic->doctors()->attach($doctor->id, [
            'status' => 'active',
            'schedule' => json_encode(['monday' => ['start' => '09:00', 'end' => '17:00']])
        ]);

        $this->assertTrue($clinic->doctors->contains($doctor));
        $this->assertEquals('active', $clinic->doctors->first()->pivot->status);
    }

    public function test_clinic_has_patients_relationship()
    {
        $clinic = Clinic::factory()->create();
        $patient = Patient::factory()->create(['preferred_clinic_id' => $clinic->id]);

        $this->assertTrue($clinic->patients->contains($patient));
    }

    public function test_clinic_active_doctors_scope()
    {
        $clinic = Clinic::factory()->create();
        
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        
        $activeDoctor = Doctor::factory()->create(['user_id' => $user1->id]);
        $inactiveDoctor = Doctor::factory()->create(['user_id' => $user2->id]);
        
        $clinic->doctors()->attach($activeDoctor->id, ['status' => 'active']);
        $clinic->doctors()->attach($inactiveDoctor->id, ['status' => 'inactive']);

        $activeDoctors = $clinic->activeDoctors()->get();

        $this->assertEquals(1, $activeDoctors->count());
        $this->assertTrue($activeDoctors->contains($activeDoctor));
        $this->assertFalse($activeDoctors->contains($inactiveDoctor));
    }

    public function test_clinic_foundation_year_casting()
    {
        $clinic = Clinic::factory()->create(['foundation_year' => '2000']);

        $this->assertIsInt($clinic->foundation_year);
        $this->assertEquals(2000, $clinic->foundation_year);
    }

    public function test_clinic_emergency_services_casting()
    {
        $clinic = Clinic::factory()->create(['emergency_services' => 1]);

        $this->assertIsBool($clinic->emergency_services);
        $this->assertTrue($clinic->emergency_services);
    }

    public function test_clinic_specialties_casting()
    {
        $specialties = ['Cardiology', 'Neurology', 'Orthopedics'];
        $clinic = Clinic::factory()->create(['specialties' => $specialties]);

        $this->assertIsArray($clinic->specialties);
        $this->assertEquals($specialties, $clinic->specialties);
    }

    public function test_clinic_schedule_casting()
    {
        $schedule = [
            'monday' => ['start' => '08:00', 'end' => '18:00'],
            'tuesday' => ['start' => '08:00', 'end' => '18:00'],
        ];
        $clinic = Clinic::factory()->create(['schedule' => $schedule]);

        $this->assertIsArray($clinic->schedule);
        $this->assertEquals($schedule, $clinic->schedule);
    }
}
