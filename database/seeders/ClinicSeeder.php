<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Clinic;

class ClinicSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $clinics = [
            [
                'name' => 'Clínica Central',
                'address' => 'Av. Principal 123, Centro Médico',
                'phone' => '555-0100',
                'email' => 'info@clinicacentral.com',
                'medical_director' => 'Dr. Carlos Mendoza',
                'foundation_year' => 1985,
                'specialties' => json_encode([
                    'Medicina General',
                    'Cardiología',
                    'Pediatría',
                    'Ginecología'
                ]),
                'schedule' => json_encode([
                    'monday' => '07:00-18:00',
                    'tuesday' => '07:00-18:00',
                    'wednesday' => '07:00-18:00',
                    'thursday' => '07:00-18:00',
                    'friday' => '07:00-18:00',
                    'saturday' => '08:00-12:00',
                    'sunday' => 'closed'
                ]),
                'emergency_services' => true,
                'status' => 'active',
                'description' => 'Clínica especializada en medicina general y especialidades médicas con más de 35 años de experiencia.'
            ],
            [
                'name' => 'Hospital San Rafael',
                'address' => 'Calle Salud 456, Sector Norte',
                'phone' => '555-0200',
                'email' => 'contacto@sanrafael.com',
                'medical_director' => 'Dra. María González',
                'foundation_year' => 1992,
                'specialties' => json_encode([
                    'Cirugía General',
                    'Traumatología',
                    'Neurología',
                    'Oncología',
                    'Medicina Interna'
                ]),
                'schedule' => json_encode([
                    'monday' => '24 hours',
                    'tuesday' => '24 hours',
                    'wednesday' => '24 hours',
                    'thursday' => '24 hours',
                    'friday' => '24 hours',
                    'saturday' => '24 hours',
                    'sunday' => '24 hours'
                ]),
                'emergency_services' => true,
                'status' => 'active',
                'description' => 'Hospital de alta complejidad con servicios de emergencia 24/7 y especialidades quirúrgicas.'
            ],
            [
                'name' => 'Centro Médico Familiar',
                'address' => 'Plaza Comercial 789, Zona Residencial',
                'phone' => '555-0300',
                'email' => 'atencion@centrofamiliar.com',
                'medical_director' => 'Dr. Roberto Silva',
                'foundation_year' => 2005,
                'specialties' => json_encode([
                    'Medicina Familiar',
                    'Pediatría',
                    'Dermatología',
                    'Oftalmología'
                ]),
                'schedule' => json_encode([
                    'monday' => '08:00-17:00',
                    'tuesday' => '08:00-17:00',
                    'wednesday' => '08:00-17:00',
                    'thursday' => '08:00-17:00',
                    'friday' => '08:00-17:00',
                    'saturday' => '09:00-13:00',
                    'sunday' => 'closed'
                ]),
                'emergency_services' => false,
                'status' => 'active',
                'description' => 'Centro médico enfocado en la atención familiar integral con ambiente cálido y profesional.'
            ],
            [
                'name' => 'Clínica Especializada del Corazón',
                'address' => 'Av. Cardiólogos 321, Torre Médica',
                'phone' => '555-0400',
                'email' => 'info@clinicacorazon.com',
                'medical_director' => 'Dr. Fernando Herrera',
                'foundation_year' => 2010,
                'specialties' => json_encode([
                    'Cardiología',
                    'Cirugía Cardiovascular',
                    'Electrofisiología',
                    'Hemodinamia'
                ]),
                'schedule' => json_encode([
                    'monday' => '07:00-19:00',
                    'tuesday' => '07:00-19:00',
                    'wednesday' => '07:00-19:00',
                    'thursday' => '07:00-19:00',
                    'friday' => '07:00-19:00',
                    'saturday' => '08:00-14:00',
                    'sunday' => 'emergency only'
                ]),
                'emergency_services' => true,
                'status' => 'active',
                'description' => 'Clínica especializada en enfermedades cardiovasculares con tecnología de vanguardia.'
            ],
            [
                'name' => 'Instituto Materno Infantil',
                'address' => 'Calle Maternidad 654, Sector Sur',
                'phone' => '555-0500',
                'email' => 'contacto@maternoinfantil.com',
                'medical_director' => 'Dra. Ana Patricia López',
                'foundation_year' => 1998,
                'specialties' => json_encode([
                    'Ginecología',
                    'Obstetricia',
                    'Pediatría',
                    'Neonatología',
                    'Medicina Materno-Fetal'
                ]),
                'schedule' => json_encode([
                    'monday' => '06:00-20:00',
                    'tuesday' => '06:00-20:00',
                    'wednesday' => '06:00-20:00',
                    'thursday' => '06:00-20:00',
                    'friday' => '06:00-20:00',
                    'saturday' => '07:00-15:00',
                    'sunday' => '08:00-14:00'
                ]),
                'emergency_services' => true,
                'status' => 'active',
                'description' => 'Instituto especializado en salud materno-infantil con sala de partos y UCI neonatal.'
            ]
        ];

        foreach ($clinics as $clinicData) {
            Clinic::create($clinicData);
        }

        $this->command->info('✅ Clínicas creadas exitosamente!');
        $this->command->info('📊 Total: ' . count($clinics) . ' clínicas');
    }
} 