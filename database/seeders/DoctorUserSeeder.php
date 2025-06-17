<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Doctor;
use Illuminate\Support\Facades\Hash;

class DoctorUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $doctors = [
            [
                'user' => [
                    'name' => 'Dr. Carlos Rodríguez',
                    'email' => 'carlos.rodriguez@clinica.com',
                    'password' => Hash::make('doctor123'),
                    'role' => 'doctor',
                    'status' => 'active',
                    'email_verified_at' => now(),
                ],
                'doctor' => [
                    'specialty' => 'Cardiología',
                    'license_number' => 'MD-CAR-001',
                    'clinic_id' => 1,
                    'phone' => '555-1001',
                    'education' => 'MD Universidad Nacional, Especialización en Cardiología',
                    'experience_years' => 15,
                    'consultation_fee' => 120.00,
                    'schedule' => json_encode([
                        'monday' => '08:00-16:00',
                        'tuesday' => '08:00-16:00',
                        'wednesday' => '08:00-16:00',
                        'thursday' => '08:00-16:00',
                        'friday' => '08:00-14:00',
                        'saturday' => 'off',
                        'sunday' => 'off'
                    ]),
                    'bio' => 'Especialista en cardiología con 15 años de experiencia en diagnóstico y tratamiento de enfermedades cardiovasculares.'
                ]
            ],
            [
                'user' => [
                    'name' => 'Dra. María González',
                    'email' => 'maria.gonzalez@clinica.com',
                    'password' => Hash::make('doctor123'),
                    'role' => 'doctor',
                    'status' => 'active',
                    'email_verified_at' => now(),
                ],
                'doctor' => [
                    'specialty' => 'Pediatría',
                    'license_number' => 'MD-PED-002',
                    'clinic_id' => 4,
                    'phone' => '555-1002',
                    'education' => 'MD Universidad Javeriana, Especialización en Pediatría',
                    'experience_years' => 12,
                    'consultation_fee' => 100.00,
                    'schedule' => json_encode([
                        'monday' => '07:00-15:00',
                        'tuesday' => '07:00-15:00',
                        'wednesday' => '07:00-15:00',
                        'thursday' => '07:00-15:00',
                        'friday' => '07:00-15:00',
                        'saturday' => '08:00-12:00',
                        'sunday' => 'off'
                    ]),
                    'bio' => 'Pediatra con amplia experiencia en cuidado integral de niños y adolescentes, especializada en desarrollo infantil.'
                ]
            ],
            [
                'user' => [
                    'name' => 'Dr. Luis Martínez',
                    'email' => 'luis.martinez@clinica.com',
                    'password' => Hash::make('doctor123'),
                    'role' => 'doctor',
                    'status' => 'active',
                    'email_verified_at' => now(),
                ],
                'doctor' => [
                    'specialty' => 'Cirugía General',
                    'license_number' => 'MD-CIR-003',
                    'clinic_id' => 2,
                    'phone' => '555-1003',
                    'education' => 'MD Universidad del Rosario, Especialización en Cirugía General',
                    'experience_years' => 20,
                    'consultation_fee' => 150.00,
                    'schedule' => json_encode([
                        'monday' => '06:00-14:00',
                        'tuesday' => '06:00-14:00',
                        'wednesday' => '06:00-14:00',
                        'thursday' => '06:00-14:00',
                        'friday' => '06:00-12:00',
                        'saturday' => 'on-call',
                        'sunday' => 'on-call'
                    ]),
                    'bio' => 'Cirujano general con 20 años de experiencia en procedimientos quirúrgicos complejos y cirugía laparoscópica.'
                ]
            ],
            [
                'user' => [
                    'name' => 'Dra. Ana Pérez',
                    'email' => 'ana.perez@clinica.com',
                    'password' => Hash::make('doctor123'),
                    'role' => 'doctor',
                    'status' => 'active',
                    'email_verified_at' => now(),
                ],
                'doctor' => [
                    'specialty' => 'Ginecología',
                    'license_number' => 'MD-GIN-004',
                    'clinic_id' => 1,
                    'phone' => '555-1004',
                    'education' => 'MD Universidad de La Sabana, Especialización en Ginecología',
                    'experience_years' => 10,
                    'consultation_fee' => 110.00,
                    'schedule' => json_encode([
                        'monday' => '08:00-17:00',
                        'tuesday' => '08:00-17:00',
                        'wednesday' => '08:00-17:00',
                        'thursday' => '08:00-17:00',
                        'friday' => '08:00-16:00',
                        'saturday' => 'off',
                        'sunday' => 'off'
                    ]),
                    'bio' => 'Ginecóloga especializada en salud reproductiva femenina, control prenatal y cirugía ginecológica mínimamente invasiva.'
                ]
            ],
            [
                'user' => [
                    'name' => 'Dr. Roberto Silva',
                    'email' => 'roberto.silva@clinica.com',
                    'password' => Hash::make('doctor123'),
                    'role' => 'doctor',
                    'status' => 'active',
                    'email_verified_at' => now(),
                ],
                'doctor' => [
                    'specialty' => 'Neurología',
                    'license_number' => 'MD-NEU-005',
                    'clinic_id' => 2,
                    'phone' => '555-1005',
                    'education' => 'MD Universidad Pontificia Bolivariana, Especialización en Neurología',
                    'experience_years' => 18,
                    'consultation_fee' => 140.00,
                    'schedule' => json_encode([
                        'monday' => '09:00-17:00',
                        'tuesday' => '09:00-17:00',
                        'wednesday' => '09:00-17:00',
                        'thursday' => '09:00-17:00',
                        'friday' => '09:00-15:00',
                        'saturday' => 'off',
                        'sunday' => 'off'
                    ]),
                    'bio' => 'Neurólogo con experiencia en diagnóstico y tratamiento de trastornos del sistema nervioso, especialista en epilepsia.'
                ]
            ],
            [
                'user' => [
                    'name' => 'Dra. Patricia Morales',
                    'email' => 'patricia.morales@clinica.com',
                    'password' => Hash::make('doctor123'),
                    'role' => 'doctor',
                    'status' => 'active',
                    'email_verified_at' => now(),
                ],
                'doctor' => [
                    'specialty' => 'Dermatología',
                    'license_number' => 'MD-DER-006',
                    'clinic_id' => 3,
                    'phone' => '555-1006',
                    'education' => 'MD Universidad El Bosque, Especialización en Dermatología',
                    'experience_years' => 8,
                    'consultation_fee' => 95.00,
                    'schedule' => json_encode([
                        'monday' => '08:00-16:00',
                        'tuesday' => '08:00-16:00',
                        'wednesday' => '08:00-16:00',
                        'thursday' => '08:00-16:00',
                        'friday' => '08:00-16:00',
                        'saturday' => '09:00-13:00',
                        'sunday' => 'off'
                    ]),
                    'bio' => 'Dermatóloga especializada en enfermedades de la piel, dermatología estética y dermatología pediátrica.'
                ]
            ],
            [
                'user' => [
                    'name' => 'Dr. Fernando Castro',
                    'email' => 'fernando.castro@clinica.com',
                    'password' => Hash::make('doctor123'),
                    'role' => 'doctor',
                    'status' => 'active',
                    'email_verified_at' => now(),
                ],
                'doctor' => [
                    'specialty' => 'Traumatología',
                    'license_number' => 'MD-TRA-007',
                    'clinic_id' => 2,
                    'phone' => '555-1007',
                    'education' => 'MD Universidad Militar, Especialización en Traumatología',
                    'experience_years' => 22,
                    'consultation_fee' => 130.00,
                    'schedule' => json_encode([
                        'monday' => '07:00-15:00',
                        'tuesday' => '07:00-15:00',
                        'wednesday' => '07:00-15:00',
                        'thursday' => '07:00-15:00',
                        'friday' => '07:00-13:00',
                        'saturday' => 'emergency',
                        'sunday' => 'emergency'
                    ]),
                    'bio' => 'Traumatólogo con amplia experiencia en cirugía ortopédica, traumatología deportiva y reconstrucción ósea.'
                ]
            ],
            [
                'user' => [
                    'name' => 'Dra. Isabel Ramírez',
                    'email' => 'isabel.ramirez@clinica.com',
                    'password' => Hash::make('doctor123'),
                    'role' => 'doctor',
                    'status' => 'active',
                    'email_verified_at' => now(),
                ],
                'doctor' => [
                    'specialty' => 'Oftalmología',
                    'license_number' => 'MD-OFT-008',
                    'clinic_id' => 3,
                    'phone' => '555-1008',
                    'education' => 'MD Universidad CES, Especialización en Oftalmología',
                    'experience_years' => 14,
                    'consultation_fee' => 115.00,
                    'schedule' => json_encode([
                        'monday' => '08:00-17:00',
                        'tuesday' => '08:00-17:00',
                        'wednesday' => '08:00-17:00',
                        'thursday' => '08:00-17:00',
                        'friday' => '08:00-16:00',
                        'saturday' => 'off',
                        'sunday' => 'off'
                    ]),
                    'bio' => 'Oftalmóloga especializada en cirugía de cataratas, retina y enfermedades oculares complejas.'
                ]
            ],
            [
                'user' => [
                    'name' => 'Dr. Andrés López',
                    'email' => 'andres.lopez@clinica.com',
                    'password' => Hash::make('doctor123'),
                    'role' => 'doctor',
                    'status' => 'active',
                    'email_verified_at' => now(),
                ],
                'doctor' => [
                    'specialty' => 'Medicina Interna',
                    'license_number' => 'MD-INT-009',
                    'clinic_id' => 1,
                    'phone' => '555-1009',
                    'education' => 'MD Universidad de Antioquia, Especialización en Medicina Interna',
                    'experience_years' => 16,
                    'consultation_fee' => 105.00,
                    'schedule' => json_encode([
                        'monday' => '07:00-16:00',
                        'tuesday' => '07:00-16:00',
                        'wednesday' => '07:00-16:00',
                        'thursday' => '07:00-16:00',
                        'friday' => '07:00-15:00',
                        'saturday' => 'off',
                        'sunday' => 'off'
                    ]),
                    'bio' => 'Internista con experiencia en diagnóstico y tratamiento de enfermedades complejas en adultos.'
                ]
            ],
            [
                'user' => [
                    'name' => 'Dra. Carmen Vargas',
                    'email' => 'carmen.vargas@clinica.com',
                    'password' => Hash::make('doctor123'),
                    'role' => 'doctor',
                    'status' => 'active',
                    'email_verified_at' => now(),
                ],
                'doctor' => [
                    'specialty' => 'Psiquiatría',
                    'license_number' => 'MD-PSI-010',
                    'clinic_id' => 3,
                    'phone' => '555-1010',
                    'education' => 'MD Universidad de los Andes, Especialización en Psiquiatría',
                    'experience_years' => 11,
                    'consultation_fee' => 125.00,
                    'schedule' => json_encode([
                        'monday' => '09:00-18:00',
                        'tuesday' => '09:00-18:00',
                        'wednesday' => '09:00-18:00',
                        'thursday' => '09:00-18:00',
                        'friday' => '09:00-17:00',
                        'saturday' => 'off',
                        'sunday' => 'off'
                    ]),
                    'bio' => 'Psiquiatra especializada en trastornos del estado de ánimo, ansiedad y salud mental integral.'
                ]
            ]
        ];

        foreach ($doctors as $doctorData) {
            // Crear usuario
            $user = User::create($doctorData['user']);
            
            // Crear perfil de doctor asociado al usuario
            $doctorData['doctor']['user_id'] = $user->id;
            Doctor::create($doctorData['doctor']);
        }

        $this->command->info('✅ Doctores y usuarios creados exitosamente!');
    }
} 