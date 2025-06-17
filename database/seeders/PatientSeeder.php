<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Patient;
use App\Models\MedicalHistory;
use App\Models\EmergencyContact;

class PatientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $patients = [
            [
                'patient' => [
                    'name' => 'Juan Carlos Pérez García',
                    'dni' => '12345678',
                    'birth_date' => '1985-03-15',
                    'gender' => 'male',
                    'blood_type' => 'O+',
                    'address' => 'Calle 123 #45-67, Barrio Centro',
                    'phone' => '555-2001',
                    'email' => 'juan.perez@email.com',
                    'status' => 'active',
                    'preferred_clinic_id' => 1
                ],
                'emergency_contact' => [
                    'name' => 'María García',
                    'relationship' => 'spouse',
                    'phone' => '555-2002'
                ],
                'medical_history' => [
                    'allergies' => ['Penicilina'],
                    'conditions' => ['Hipertensión arterial'],
                    'medications' => ['Losartán 50mg diario'],
                    'surgeries' => ['Apendicectomía (2010)']
                ]
            ],
            [
                'patient' => [
                    'name' => 'Ana María González López',
                    'dni' => '23456789',
                    'birth_date' => '1992-07-22',
                    'gender' => 'female',
                    'blood_type' => 'A+',
                    'address' => 'Carrera 45 #78-90, Barrio Norte',
                    'phone' => '555-2003',
                    'email' => 'ana.gonzalez@email.com',
                    'status' => 'active',
                    'preferred_clinic_id' => 1
                ],
                'emergency_contact' => [
                    'name' => 'Rosa López',
                    'relationship' => 'mother',
                    'phone' => '555-2004'
                ],
                'medical_history' => [
                    'allergies' => ['Aspirina', 'Mariscos'],
                    'conditions' => ['Asma bronquial'],
                    'medications' => ['Salbutamol inhalador PRN'],
                    'surgeries' => []
                ]
            ],
            [
                'patient' => [
                    'name' => 'Carlos Eduardo Rodríguez Silva',
                    'dni' => '34567890',
                    'birth_date' => '1978-11-08',
                    'gender' => 'male',
                    'blood_type' => 'B+',
                    'address' => 'Av. Libertador 234, Conjunto Residencial',
                    'phone' => '555-2005',
                    'email' => 'carlos.rodriguez@email.com',
                    'status' => 'active',
                    'preferred_clinic_id' => 2
                ],
                'emergency_contact' => [
                    'name' => 'Pedro Rodríguez',
                    'relationship' => 'brother',
                    'phone' => '555-2006'
                ],
                'medical_history' => [
                    'allergies' => [],
                    'conditions' => ['Diabetes tipo 2', 'Dislipidemia'],
                    'medications' => ['Metformina 850mg BID', 'Atorvastatina 20mg'],
                    'surgeries' => ['Colecistectomía laparoscópica (2015)']
                ]
            ],
            [
                'patient' => [
                    'name' => 'Laura Patricia Martínez Vargas',
                    'dni' => '45678901',
                    'birth_date' => '1988-04-12',
                    'gender' => 'female',
                    'blood_type' => 'AB+',
                    'address' => 'Calle 67 #89-01, Barrio Los Pinos',
                    'phone' => '555-2007',
                    'email' => 'laura.martinez@email.com',
                    'status' => 'active',
                    'preferred_clinic_id' => 3
                ],
                'emergency_contact' => [
                    'name' => 'Miguel Vargas',
                    'relationship' => 'spouse',
                    'phone' => '555-2008'
                ],
                'medical_history' => [
                    'allergies' => ['Polvo', 'Polen'],
                    'conditions' => ['Rinitis alérgica'],
                    'medications' => ['Loratadina 10mg diario en temporada'],
                    'surgeries' => ['Cesárea (2020)']
                ]
            ],
            [
                'patient' => [
                    'name' => 'Roberto Silva Moreno',
                    'dni' => '56789012',
                    'birth_date' => '1965-09-25',
                    'gender' => 'male',
                    'blood_type' => 'O-',
                    'address' => 'Carrera 12 #34-56, Centro Histórico',
                    'phone' => '555-2009',
                    'email' => 'roberto.silva@email.com',
                    'status' => 'active',
                    'preferred_clinic_id' => 2
                ],
                'emergency_contact' => [
                    'name' => 'Elena Moreno',
                    'relationship' => 'spouse',
                    'phone' => '555-2010'
                ],
                'medical_history' => [
                    'allergies' => ['Contraste yodado'],
                    'conditions' => ['Hipertensión arterial', 'Osteoartritis'],
                    'medications' => ['Enalapril 10mg BID', 'Ibuprofeno PRN'],
                    'surgeries' => ['Hernia inguinal (2018)', 'Artroscopia rodilla (2019)']
                ]
            ],
            [
                'patient' => [
                    'name' => 'Sofía Hernández Castro',
                    'dni' => '98765432',
                    'birth_date' => '2010-12-03',
                    'gender' => 'female',
                    'blood_type' => 'A-',
                    'address' => 'Av. Principal 789, Urbanización Nueva',
                    'phone' => '555-2011',
                    'email' => 'sofia.hernandez@email.com',
                    'status' => 'active',
                    'preferred_clinic_id' => 4
                ],
                'emergency_contact' => [
                    'name' => 'Carmen Castro',
                    'relationship' => 'mother',
                    'phone' => '555-2012'
                ],
                'medical_history' => [
                    'allergies' => [],
                    'conditions' => [],
                    'medications' => ['Vitamina D 400UI diario'],
                    'surgeries' => []
                ]
            ],
            [
                'patient' => [
                    'name' => 'Andrés Felipe López Ruiz',
                    'dni' => '87654321',
                    'birth_date' => '1995-06-18',
                    'gender' => 'male',
                    'blood_type' => 'B-',
                    'address' => 'Calle 90 #12-34, Zona Rosa',
                    'phone' => '555-2013',
                    'email' => 'andres.lopez@email.com',
                    'status' => 'active',
                    'preferred_clinic_id' => 1
                ],
                'emergency_contact' => [
                    'name' => 'Luis López',
                    'relationship' => 'father',
                    'phone' => '555-2014'
                ],
                'medical_history' => [
                    'allergies' => ['Látex'],
                    'conditions' => [],
                    'medications' => [],
                    'surgeries' => ['Fractura radio derecho (2018) - reducción quirúrgica']
                ]
            ],
            [
                'patient' => [
                    'name' => 'Patricia Morales Díaz',
                    'dni' => '76543210',
                    'birth_date' => '1970-01-30',
                    'gender' => 'female',
                    'blood_type' => 'AB-',
                    'address' => 'Carrera 56 #78-90, Barrio La Esperanza',
                    'phone' => '555-2015',
                    'email' => 'patricia.morales@email.com',
                    'status' => 'active',
                    'preferred_clinic_id' => 3
                ],
                'emergency_contact' => [
                    'name' => 'Andrea Morales',
                    'relationship' => 'daughter',
                    'phone' => '555-2016'
                ],
                'medical_history' => [
                    'allergies' => ['Sulfonamidas'],
                    'conditions' => ['Hipotiroidismo', 'Osteopenia'],
                    'medications' => ['Levotiroxina 50mcg', 'Calcio + Vitamina D'],
                    'surgeries' => ['Histerectomía (2015)', 'Biopsia mama (2020) - benigna']
                ]
            ],
            [
                'patient' => [
                    'name' => 'Diego Alejandro Ramírez Torres',
                    'dni' => '65432109',
                    'birth_date' => '1982-08-14',
                    'gender' => 'male',
                    'blood_type' => 'O+',
                    'address' => 'Av. Boyacá 345, Conjunto Los Cedros',
                    'phone' => '555-2017',
                    'email' => 'diego.ramirez@email.com',
                    'status' => 'active',
                    'preferred_clinic_id' => 2
                ],
                'emergency_contact' => [
                    'name' => 'Claudia Torres',
                    'relationship' => 'spouse',
                    'phone' => '555-2018'
                ],
                'medical_history' => [
                    'allergies' => [],
                    'conditions' => ['Lumbalgia crónica'],
                    'medications' => ['Relajante muscular PRN'],
                    'surgeries' => []
                ]
            ],
            [
                'patient' => [
                    'name' => 'Valentina Castro Jiménez',
                    'dni' => '54321098',
                    'birth_date' => '2015-02-20',
                    'gender' => 'female',
                    'blood_type' => 'A+',
                    'address' => 'Calle 23 #45-67, Barrio San José',
                    'phone' => '555-2019',
                    'email' => 'valentina.castro@email.com',
                    'status' => 'active',
                    'preferred_clinic_id' => 4
                ],
                'emergency_contact' => [
                    'name' => 'Gloria Jiménez',
                    'relationship' => 'mother',
                    'phone' => '555-2020'
                ],
                'medical_history' => [
                    'allergies' => ['Chocolate', 'Colorantes artificiales'],
                    'conditions' => ['Dermatitis atópica'],
                    'medications' => ['Crema hidratante diaria', 'Antihistamínico PRN'],
                    'surgeries' => []
                ]
            ]
        ];

        foreach ($patients as $patientData) {
            // Verificar si el paciente ya existe
            $existingPatient = Patient::where('dni', $patientData['patient']['dni'])->first();
            
            if ($existingPatient) {
                $this->command->info("⚠️  Paciente con DNI {$patientData['patient']['dni']} ya existe, saltando...");
                continue;
            }
            
            // Crear paciente
            $patient = Patient::create($patientData['patient']);
            
            // Crear contacto de emergencia
            $emergencyData = $patientData['emergency_contact'];
            $emergencyData['patient_id'] = $patient->id;
            EmergencyContact::create($emergencyData);
            
            // Crear historial médico
            $historyData = $patientData['medical_history'];
            $historyData['patient_id'] = $patient->id;
            MedicalHistory::create($historyData);
            
            $this->command->info("✅ Paciente creado: {$patient->name} (DNI: {$patient->dni})");
        }

        $this->command->info('✅ Proceso de creación de pacientes completado!');
    }
} 