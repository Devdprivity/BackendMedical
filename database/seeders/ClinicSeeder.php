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
                'website' => 'https://clinicacentral.com',
                'status' => 'active',
                'specialties' => json_encode(['Medicina General', 'Cardiología', 'Pediatría', 'Ginecología']),
                'opening_hours' => json_encode([
                    'monday' => '07:00-18:00',
                    'tuesday' => '07:00-18:00',
                    'wednesday' => '07:00-18:00',
                    'thursday' => '07:00-18:00',
                    'friday' => '07:00-18:00',
                    'saturday' => '08:00-12:00',
                    'sunday' => 'closed'
                ]),
                'emergency_contact' => '555-0199',
                'license_number' => 'CL-2024-001'
            ],
            [
                'name' => 'Hospital San Rafael',
                'address' => 'Calle 45 #12-34, Zona Norte',
                'phone' => '555-0200',
                'email' => 'contacto@sanrafael.com',
                'website' => 'https://hospitalsanrafael.com',
                'status' => 'active',
                'specialties' => json_encode(['Cirugía', 'Traumatología', 'Neurología', 'Oncología', 'UCI']),
                'opening_hours' => json_encode([
                    'monday' => '24/7',
                    'tuesday' => '24/7',
                    'wednesday' => '24/7',
                    'thursday' => '24/7',
                    'friday' => '24/7',
                    'saturday' => '24/7',
                    'sunday' => '24/7'
                ]),
                'emergency_contact' => '555-0299',
                'license_number' => 'CL-2024-002'
            ],
            [
                'name' => 'Centro Médico Esperanza',
                'address' => 'Carrera 78 #90-12, Zona Sur',
                'phone' => '555-0300',
                'email' => 'info@esperanza.com',
                'website' => 'https://centroesperanza.com',
                'status' => 'active',
                'specialties' => json_encode(['Dermatología', 'Oftalmología', 'Psicología', 'Fisioterapia']),
                'opening_hours' => json_encode([
                    'monday' => '08:00-17:00',
                    'tuesday' => '08:00-17:00',
                    'wednesday' => '08:00-17:00',
                    'thursday' => '08:00-17:00',
                    'friday' => '08:00-17:00',
                    'saturday' => '09:00-13:00',
                    'sunday' => 'closed'
                ]),
                'emergency_contact' => '555-0399',
                'license_number' => 'CL-2024-003'
            ],
            [
                'name' => 'Clínica Pediátrica Los Ángeles',
                'address' => 'Av. Los Niños 567, Zona Residencial',
                'phone' => '555-0400',
                'email' => 'pediatria@losangeles.com',
                'website' => 'https://pediatrialosangeles.com',
                'status' => 'active',
                'specialties' => json_encode(['Pediatría', 'Neonatología', 'Psicología Infantil', 'Vacunación']),
                'opening_hours' => json_encode([
                    'monday' => '07:00-16:00',
                    'tuesday' => '07:00-16:00',
                    'wednesday' => '07:00-16:00',
                    'thursday' => '07:00-16:00',
                    'friday' => '07:00-16:00',
                    'saturday' => '08:00-12:00',
                    'sunday' => 'closed'
                ]),
                'emergency_contact' => '555-0499',
                'license_number' => 'CL-2024-004'
            ],
            [
                'name' => 'Instituto Cardiovascular',
                'address' => 'Calle del Corazón 234, Centro Especializado',
                'phone' => '555-0500',
                'email' => 'cardiologia@instituto.com',
                'website' => 'https://cardiovascular.com',
                'status' => 'active',
                'specialties' => json_encode(['Cardiología', 'Cirugía Cardiovascular', 'Electrofisiología', 'Hemodinamia']),
                'opening_hours' => json_encode([
                    'monday' => '06:00-18:00',
                    'tuesday' => '06:00-18:00',
                    'wednesday' => '06:00-18:00',
                    'thursday' => '06:00-18:00',
                    'friday' => '06:00-18:00',
                    'saturday' => '07:00-12:00',
                    'sunday' => 'emergency'
                ]),
                'emergency_contact' => '555-0599',
                'license_number' => 'CL-2024-005'
            ]
        ];

        foreach ($clinics as $clinicData) {
            Clinic::create($clinicData);
        }

        $this->command->info('✅ Clínicas creadas exitosamente!');
    }
} 