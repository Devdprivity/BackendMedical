<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Doctor;
use App\Models\Clinic;
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
                    'name' => 'Dr. Carlos Rodríguez',
                    'specialty' => 'Cardiología',
                    'license_number' => 'MD-CAR-001',
                    'email' => 'carlos.rodriguez@clinica.com',
                    'phone' => '555-1001',
                    'emergency_phone' => '555-9001',
                    'address' => 'Calle 123 #45-67, Bogotá, Colombia',
                    'education' => [
                        'MD Universidad Nacional - Medicina General',
                        'Especialización en Cardiología - Hospital San Juan de Dios'
                    ],
                    'certifications' => [
                        'Certificado en Cardiología Intervencionista',
                        'Certificado en Ecocardiografía'
                    ],
                    'languages' => ['Español', 'Inglés'],
                    'experience_years' => 15,
                    'status' => 'active',
                    'bio' => 'Especialista en cardiología con 15 años de experiencia en diagnóstico y tratamiento de enfermedades cardiovasculares.',
                    'rating' => 4.8
                ],
                'clinic_id' => 1,
                'schedule' => [
                    'monday' => '08:00-16:00',
                    'tuesday' => '08:00-16:00',
                    'wednesday' => '08:00-16:00',
                    'thursday' => '08:00-16:00',
                    'friday' => '08:00-14:00',
                    'saturday' => 'off',
                    'sunday' => 'off'
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
                    'name' => 'Dra. María González',
                    'specialty' => 'Pediatría',
                    'license_number' => 'MD-PED-002',
                    'email' => 'maria.gonzalez@clinica.com',
                    'phone' => '555-1002',
                    'emergency_phone' => '555-9002',
                    'address' => 'Carrera 45 #23-89, Medellín, Colombia',
                    'education' => [
                        'MD Universidad Javeriana - Medicina General',
                        'Especialización en Pediatría - Hospital Infantil'
                    ],
                    'certifications' => [
                        'Certificado en Pediatría General',
                        'Certificado en Desarrollo Infantil'
                    ],
                    'languages' => ['Español', 'Inglés'],
                    'experience_years' => 12,
                    'status' => 'active',
                    'bio' => 'Pediatra con amplia experiencia en cuidado integral de niños y adolescentes, especializada en desarrollo infantil.',
                    'rating' => 4.9
                ],
                'clinic_id' => 4,
                'schedule' => [
                    'monday' => '07:00-15:00',
                    'tuesday' => '07:00-15:00',
                    'wednesday' => '07:00-15:00',
                    'thursday' => '07:00-15:00',
                    'friday' => '07:00-15:00',
                    'saturday' => '08:00-12:00',
                    'sunday' => 'off'
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
                    'name' => 'Dr. Luis Martínez',
                    'specialty' => 'Cirugía General',
                    'license_number' => 'MD-CIR-003',
                    'email' => 'luis.martinez@clinica.com',
                    'phone' => '555-1003',
                    'emergency_phone' => '555-9003',
                    'address' => 'Avenida 68 #12-34, Bogotá, Colombia',
                    'education' => [
                        'MD Universidad del Rosario - Medicina General',
                        'Especialización en Cirugía General - Hospital Militar'
                    ],
                    'certifications' => [
                        'Certificado en Cirugía Laparoscópica',
                        'Certificado en Cirugía de Emergencia'
                    ],
                    'languages' => ['Español', 'Inglés', 'Francés'],
                    'experience_years' => 20,
                    'status' => 'active',
                    'bio' => 'Cirujano general con 20 años de experiencia en procedimientos quirúrgicos complejos y cirugía laparoscópica.',
                    'rating' => 4.7
                ],
                'clinic_id' => 2,
                'schedule' => [
                    'monday' => '06:00-14:00',
                    'tuesday' => '06:00-14:00',
                    'wednesday' => '06:00-14:00',
                    'thursday' => '06:00-14:00',
                    'friday' => '06:00-12:00',
                    'saturday' => 'on-call',
                    'sunday' => 'on-call'
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
                    'name' => 'Dra. Ana Pérez',
                    'specialty' => 'Ginecología',
                    'license_number' => 'MD-GIN-004',
                    'email' => 'ana.perez@clinica.com',
                    'phone' => '555-1004',
                    'emergency_phone' => '555-9004',
                    'address' => 'Calle 72 #11-45, Bogotá, Colombia',
                    'education' => [
                        'MD Universidad de La Sabana - Medicina General',
                        'Especialización en Ginecología - Hospital de la Mujer'
                    ],
                    'certifications' => [
                        'Certificado en Ginecología Oncológica',
                        'Certificado en Cirugía Ginecológica Mínimamente Invasiva'
                    ],
                    'languages' => ['Español', 'Inglés'],
                    'experience_years' => 10,
                    'status' => 'active',
                    'bio' => 'Ginecóloga especializada en salud reproductiva femenina, control prenatal y cirugía ginecológica mínimamente invasiva.',
                    'rating' => 4.6
                ],
                'clinic_id' => 1,
                'schedule' => [
                    'monday' => '08:00-17:00',
                    'tuesday' => '08:00-17:00',
                    'wednesday' => '08:00-17:00',
                    'thursday' => '08:00-17:00',
                    'friday' => '08:00-16:00',
                    'saturday' => 'off',
                    'sunday' => 'off'
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
                    'name' => 'Dr. Roberto Silva',
                    'specialty' => 'Neurología',
                    'license_number' => 'MD-NEU-005',
                    'email' => 'roberto.silva@clinica.com',
                    'phone' => '555-1005',
                    'emergency_phone' => '555-9005',
                    'address' => 'Carrera 15 #85-23, Bogotá, Colombia',
                    'education' => [
                        'MD Universidad Pontificia Bolivariana - Medicina General',
                        'Especialización en Neurología - Instituto de Neurociencias'
                    ],
                    'certifications' => [
                        'Certificado en Epileptología',
                        'Certificado en Neurofisiología Clínica'
                    ],
                    'languages' => ['Español', 'Inglés', 'Alemán'],
                    'experience_years' => 18,
                    'status' => 'active',
                    'bio' => 'Neurólogo con experiencia en diagnóstico y tratamiento de trastornos del sistema nervioso, especialista en epilepsia.',
                    'rating' => 4.8
                ],
                'clinic_id' => 2,
                'schedule' => [
                    'monday' => '09:00-17:00',
                    'tuesday' => '09:00-17:00',
                    'wednesday' => '09:00-17:00',
                    'thursday' => '09:00-17:00',
                    'friday' => '09:00-15:00',
                    'saturday' => 'off',
                    'sunday' => 'off'
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
                    'name' => 'Dra. Patricia Morales',
                    'specialty' => 'Dermatología',
                    'license_number' => 'MD-DER-006',
                    'email' => 'patricia.morales@clinica.com',
                    'phone' => '555-1006',
                    'emergency_phone' => '555-9006',
                    'address' => 'Calle 93 #14-28, Bogotá, Colombia',
                    'education' => [
                        'MD Universidad El Bosque - Medicina General',
                        'Especialización en Dermatología - Hospital Militar'
                    ],
                    'certifications' => [
                        'Certificado en Dermatología Estética',
                        'Certificado en Dermatología Pediátrica'
                    ],
                    'languages' => ['Español', 'Inglés'],
                    'experience_years' => 8,
                    'status' => 'active',
                    'bio' => 'Dermatóloga especializada en enfermedades de la piel, dermatología estética y dermatología pediátrica.',
                    'rating' => 4.5
                ],
                'clinic_id' => 3,
                'schedule' => [
                    'monday' => '08:00-16:00',
                    'tuesday' => '08:00-16:00',
                    'wednesday' => '08:00-16:00',
                    'thursday' => '08:00-16:00',
                    'friday' => '08:00-16:00',
                    'saturday' => '09:00-13:00',
                    'sunday' => 'off'
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
                    'name' => 'Dr. Fernando Castro',
                    'specialty' => 'Traumatología',
                    'license_number' => 'MD-TRA-007',
                    'email' => 'fernando.castro@clinica.com',
                    'phone' => '555-1007',
                    'emergency_phone' => '555-9007',
                    'address' => 'Avenida 19 #104-35, Bogotá, Colombia',
                    'education' => [
                        'MD Universidad Militar - Medicina General',
                        'Especialización en Traumatología - Hospital Militar'
                    ],
                    'certifications' => [
                        'Certificado en Cirugía Ortopédica',
                        'Certificado en Traumatología Deportiva'
                    ],
                    'languages' => ['Español', 'Inglés'],
                    'experience_years' => 22,
                    'status' => 'active',
                    'bio' => 'Traumatólogo con amplia experiencia en cirugía ortopédica, traumatología deportiva y reconstrucción ósea.',
                    'rating' => 4.9
                ],
                'clinic_id' => 2,
                'schedule' => [
                    'monday' => '07:00-15:00',
                    'tuesday' => '07:00-15:00',
                    'wednesday' => '07:00-15:00',
                    'thursday' => '07:00-15:00',
                    'friday' => '07:00-13:00',
                    'saturday' => 'emergency',
                    'sunday' => 'emergency'
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
                    'name' => 'Dra. Isabel Ramírez',
                    'specialty' => 'Oftalmología',
                    'license_number' => 'MD-OFT-008',
                    'email' => 'isabel.ramirez@clinica.com',
                    'phone' => '555-1008',
                    'emergency_phone' => '555-9008',
                    'address' => 'Calle 127 #7-45, Bogotá, Colombia',
                    'education' => [
                        'MD Universidad CES - Medicina General',
                        'Especialización en Oftalmología - Instituto de Oftalmología'
                    ],
                    'certifications' => [
                        'Certificado en Cirugía de Cataratas',
                        'Certificado en Cirugía de Retina'
                    ],
                    'languages' => ['Español', 'Inglés'],
                    'experience_years' => 14,
                    'status' => 'active',
                    'bio' => 'Oftalmóloga especializada en cirugía de cataratas, retina y enfermedades oculares complejas.',
                    'rating' => 4.7
                ],
                'clinic_id' => 3,
                'schedule' => [
                    'monday' => '08:00-17:00',
                    'tuesday' => '08:00-17:00',
                    'wednesday' => '08:00-17:00',
                    'thursday' => '08:00-17:00',
                    'friday' => '08:00-16:00',
                    'saturday' => 'off',
                    'sunday' => 'off'
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
                    'name' => 'Dr. Andrés López',
                    'specialty' => 'Medicina Interna',
                    'license_number' => 'MD-INT-009',
                    'email' => 'andres.lopez@clinica.com',
                    'phone' => '555-1009',
                    'emergency_phone' => '555-9009',
                    'address' => 'Carrera 7 #32-16, Bogotá, Colombia',
                    'education' => [
                        'MD Universidad de Antioquia - Medicina General',
                        'Especialización en Medicina Interna - Hospital San Vicente'
                    ],
                    'certifications' => [
                        'Certificado en Medicina Interna',
                        'Certificado en Cuidados Intensivos'
                    ],
                    'languages' => ['Español', 'Inglés'],
                    'experience_years' => 16,
                    'status' => 'active',
                    'bio' => 'Internista con experiencia en diagnóstico y tratamiento de enfermedades complejas en adultos.',
                    'rating' => 4.6
                ],
                'clinic_id' => 1,
                'schedule' => [
                    'monday' => '07:00-16:00',
                    'tuesday' => '07:00-16:00',
                    'wednesday' => '07:00-16:00',
                    'thursday' => '07:00-16:00',
                    'friday' => '07:00-15:00',
                    'saturday' => 'off',
                    'sunday' => 'off'
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
                    'name' => 'Dra. Carmen Vargas',
                    'specialty' => 'Psiquiatría',
                    'license_number' => 'MD-PSI-010',
                    'email' => 'carmen.vargas@clinica.com',
                    'phone' => '555-1010',
                    'emergency_phone' => '555-9010',
                    'address' => 'Calle 100 #9-85, Bogotá, Colombia',
                    'education' => [
                        'MD Universidad de los Andes - Medicina General',
                        'Especialización en Psiquiatría - Hospital San Juan de Dios'
                    ],
                    'certifications' => [
                        'Certificado en Psiquiatría General',
                        'Certificado en Terapia Cognitivo-Conductual'
                    ],
                    'languages' => ['Español', 'Inglés'],
                    'experience_years' => 11,
                    'status' => 'active',
                    'bio' => 'Psiquiatra especializada en trastornos del estado de ánimo, ansiedad y salud mental integral.',
                    'rating' => 4.8
                ],
                'clinic_id' => 3,
                'schedule' => [
                    'monday' => '09:00-18:00',
                    'tuesday' => '09:00-18:00',
                    'wednesday' => '09:00-18:00',
                    'thursday' => '09:00-18:00',
                    'friday' => '09:00-17:00',
                    'saturday' => 'off',
                    'sunday' => 'off'
                ]
            ]
        ];

        foreach ($doctors as $doctorData) {
            // Verificar si el usuario ya existe
            $existingUser = User::where('email', $doctorData['user']['email'])->first();
            
            if ($existingUser) {
                $this->command->info("⚠️  Usuario {$doctorData['user']['email']} ya existe, saltando...");
                continue;
            }
            
            // Crear usuario
            $user = User::create($doctorData['user']);
            
            // Crear perfil de doctor asociado al usuario
            $doctorData['doctor']['user_id'] = $user->id;
            $doctor = Doctor::create($doctorData['doctor']);
            
            // Crear la relación doctor-clínica en la tabla pivot
            $clinic = Clinic::find($doctorData['clinic_id']);
            if ($clinic) {
                $doctor->clinics()->attach($clinic->id, [
                    'status' => 'active',
                    'schedule' => json_encode($doctorData['schedule'])
                ]);
                $this->command->info("✅ Doctor creado: {$doctorData['user']['name']} - Asignado a {$clinic->name}");
            } else {
                $this->command->info("✅ Doctor creado: {$doctorData['user']['name']} - Sin clínica asignada");
            }
        }

        $this->command->info('✅ Proceso de creación de doctores completado!');
    }
} 