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
                    'first_name' => 'Juan Carlos',
                    'last_name' => 'Pérez García',
                    'email' => 'juan.perez@email.com',
                    'phone' => '555-2001',
                    'date_of_birth' => '1985-03-15',
                    'gender' => 'male',
                    'address' => 'Calle 123 #45-67, Barrio Centro',
                    'identification_type' => 'CC',
                    'identification_number' => '12345678',
                    'blood_type' => 'O+',
                    'marital_status' => 'married',
                    'occupation' => 'Ingeniero',
                    'insurance_provider' => 'EPS Salud Total',
                    'insurance_number' => 'ST-12345678',
                    'status' => 'active'
                ],
                'emergency_contact' => [
                    'name' => 'María García',
                    'relationship' => 'spouse',
                    'phone' => '555-2002',
                    'email' => 'maria.garcia@email.com'
                ],
                'medical_history' => [
                    'allergies' => 'Penicilina',
                    'chronic_conditions' => 'Hipertensión arterial',
                    'medications' => 'Losartán 50mg diario',
                    'family_history' => 'Padre: Diabetes tipo 2, Madre: Hipertensión',
                    'surgical_history' => 'Apendicectomía (2010)',
                    'notes' => 'Paciente colaborador, buen cumplimiento terapéutico'
                ]
            ],
            [
                'patient' => [
                    'first_name' => 'Ana María',
                    'last_name' => 'González López',
                    'email' => 'ana.gonzalez@email.com',
                    'phone' => '555-2003',
                    'date_of_birth' => '1992-07-22',
                    'gender' => 'female',
                    'address' => 'Carrera 45 #78-90, Barrio Norte',
                    'identification_type' => 'CC',
                    'identification_number' => '23456789',
                    'blood_type' => 'A+',
                    'marital_status' => 'single',
                    'occupation' => 'Profesora',
                    'insurance_provider' => 'EPS Compensar',
                    'insurance_number' => 'CP-23456789',
                    'status' => 'active'
                ],
                'emergency_contact' => [
                    'name' => 'Rosa López',
                    'relationship' => 'mother',
                    'phone' => '555-2004',
                    'email' => 'rosa.lopez@email.com'
                ],
                'medical_history' => [
                    'allergies' => 'Aspirina, Mariscos',
                    'chronic_conditions' => 'Asma bronquial',
                    'medications' => 'Salbutamol inhalador PRN',
                    'family_history' => 'Madre: Asma, Hermana: Alergias alimentarias',
                    'surgical_history' => 'Ninguna',
                    'notes' => 'Control periódico de asma, evitar alérgenos conocidos'
                ]
            ],
            [
                'patient' => [
                    'first_name' => 'Carlos Eduardo',
                    'last_name' => 'Rodríguez Silva',
                    'email' => 'carlos.rodriguez@email.com',
                    'phone' => '555-2005',
                    'date_of_birth' => '1978-11-08',
                    'gender' => 'male',
                    'address' => 'Av. Libertador 234, Conjunto Residencial',
                    'identification_type' => 'CC',
                    'identification_number' => '34567890',
                    'blood_type' => 'B+',
                    'marital_status' => 'divorced',
                    'occupation' => 'Contador',
                    'insurance_provider' => 'EPS Sanitas',
                    'insurance_number' => 'SN-34567890',
                    'status' => 'active'
                ],
                'emergency_contact' => [
                    'name' => 'Pedro Rodríguez',
                    'relationship' => 'brother',
                    'phone' => '555-2006',
                    'email' => 'pedro.rodriguez@email.com'
                ],
                'medical_history' => [
                    'allergies' => 'Ninguna conocida',
                    'chronic_conditions' => 'Diabetes tipo 2, Dislipidemia',
                    'medications' => 'Metformina 850mg BID, Atorvastatina 20mg',
                    'family_history' => 'Padre: Diabetes, Abuelo paterno: Infarto',
                    'surgical_history' => 'Colecistectomía laparoscópica (2015)',
                    'notes' => 'Control glicémico irregular, requiere educación diabetológica'
                ]
            ],
            [
                'patient' => [
                    'first_name' => 'Laura Patricia',
                    'last_name' => 'Martínez Vargas',
                    'email' => 'laura.martinez@email.com',
                    'phone' => '555-2007',
                    'date_of_birth' => '1988-04-12',
                    'gender' => 'female',
                    'address' => 'Calle 67 #89-01, Barrio Los Pinos',
                    'identification_type' => 'CC',
                    'identification_number' => '45678901',
                    'blood_type' => 'AB+',
                    'marital_status' => 'married',
                    'occupation' => 'Abogada',
                    'insurance_provider' => 'EPS Sura',
                    'insurance_number' => 'SR-45678901',
                    'status' => 'active'
                ],
                'emergency_contact' => [
                    'name' => 'Miguel Vargas',
                    'relationship' => 'spouse',
                    'phone' => '555-2008',
                    'email' => 'miguel.vargas@email.com'
                ],
                'medical_history' => [
                    'allergies' => 'Polvo, Polen',
                    'chronic_conditions' => 'Rinitis alérgica',
                    'medications' => 'Loratadina 10mg diario en temporada',
                    'family_history' => 'Madre: Alergias, Padre: Sano',
                    'surgical_history' => 'Cesárea (2020)',
                    'notes' => 'Embarazo previo sin complicaciones, planifica segundo embarazo'
                ]
            ],
            [
                'patient' => [
                    'first_name' => 'Roberto',
                    'last_name' => 'Silva Moreno',
                    'email' => 'roberto.silva@email.com',
                    'phone' => '555-2009',
                    'date_of_birth' => '1965-09-25',
                    'gender' => 'male',
                    'address' => 'Carrera 12 #34-56, Centro Histórico',
                    'identification_type' => 'CC',
                    'identification_number' => '56789012',
                    'blood_type' => 'O-',
                    'marital_status' => 'married',
                    'occupation' => 'Médico Veterinario',
                    'insurance_provider' => 'EPS Famisanar',
                    'insurance_number' => 'FM-56789012',
                    'status' => 'active'
                ],
                'emergency_contact' => [
                    'name' => 'Elena Moreno',
                    'relationship' => 'spouse',
                    'phone' => '555-2010',
                    'email' => 'elena.moreno@email.com'
                ],
                'medical_history' => [
                    'allergies' => 'Contraste yodado',
                    'chronic_conditions' => 'Hipertensión arterial, Osteoartritis',
                    'medications' => 'Enalapril 10mg BID, Ibuprofeno PRN',
                    'family_history' => 'Padre: HTA, Madre: Artritis reumatoide',
                    'surgical_history' => 'Hernia inguinal (2018), Artroscopia rodilla (2019)',
                    'notes' => 'Actividad física regular, control cardiovascular estable'
                ]
            ],
            [
                'patient' => [
                    'first_name' => 'Sofía',
                    'last_name' => 'Hernández Castro',
                    'email' => 'sofia.hernandez@email.com',
                    'phone' => '555-2011',
                    'date_of_birth' => '2010-12-03',
                    'gender' => 'female',
                    'address' => 'Av. Principal 789, Urbanización Nueva',
                    'identification_type' => 'TI',
                    'identification_number' => '98765432',
                    'blood_type' => 'A-',
                    'marital_status' => 'single',
                    'occupation' => 'Estudiante',
                    'insurance_provider' => 'EPS Salud Total',
                    'insurance_number' => 'ST-98765432',
                    'status' => 'active'
                ],
                'emergency_contact' => [
                    'name' => 'Carmen Castro',
                    'relationship' => 'mother',
                    'phone' => '555-2012',
                    'email' => 'carmen.castro@email.com'
                ],
                'medical_history' => [
                    'allergies' => 'Ninguna conocida',
                    'chronic_conditions' => 'Ninguna',
                    'medications' => 'Vitamina D 400UI diario',
                    'family_history' => 'Abuelos: Sanos',
                    'surgical_history' => 'Ninguna',
                    'notes' => 'Desarrollo normal para la edad, esquema de vacunación completo'
                ]
            ],
            [
                'patient' => [
                    'first_name' => 'Andrés Felipe',
                    'last_name' => 'López Ruiz',
                    'email' => 'andres.lopez@email.com',
                    'phone' => '555-2013',
                    'date_of_birth' => '1995-06-18',
                    'gender' => 'male',
                    'address' => 'Calle 90 #12-34, Zona Rosa',
                    'identification_type' => 'CC',
                    'identification_number' => '87654321',
                    'blood_type' => 'B-',
                    'marital_status' => 'single',
                    'occupation' => 'Diseñador Gráfico',
                    'insurance_provider' => 'EPS Compensar',
                    'insurance_number' => 'CP-87654321',
                    'status' => 'active'
                ],
                'emergency_contact' => [
                    'name' => 'Luis López',
                    'relationship' => 'father',
                    'phone' => '555-2014',
                    'email' => 'luis.lopez@email.com'
                ],
                'medical_history' => [
                    'allergies' => 'Látex',
                    'chronic_conditions' => 'Ninguna',
                    'medications' => 'Ninguna',
                    'family_history' => 'Padre: Hipertensión leve',
                    'surgical_history' => 'Fractura radio derecho (2018) - reducción quirúrgica',
                    'notes' => 'Deportista amateur, lesión por skateboarding'
                ]
            ],
            [
                'patient' => [
                    'first_name' => 'Patricia',
                    'last_name' => 'Morales Díaz',
                    'email' => 'patricia.morales@email.com',
                    'phone' => '555-2015',
                    'date_of_birth' => '1970-01-30',
                    'gender' => 'female',
                    'address' => 'Carrera 56 #78-90, Barrio La Esperanza',
                    'identification_type' => 'CC',
                    'identification_number' => '76543210',
                    'blood_type' => 'AB-',
                    'marital_status' => 'widowed',
                    'occupation' => 'Enfermera',
                    'insurance_provider' => 'EPS Sanitas',
                    'insurance_number' => 'SN-76543210',
                    'status' => 'active'
                ],
                'emergency_contact' => [
                    'name' => 'Andrea Morales',
                    'relationship' => 'daughter',
                    'phone' => '555-2016',
                    'email' => 'andrea.morales@email.com'
                ],
                'medical_history' => [
                    'allergies' => 'Sulfonamidas',
                    'chronic_conditions' => 'Hipotiroidismo, Osteopenia',
                    'medications' => 'Levotiroxina 50mcg, Calcio + Vitamina D',
                    'family_history' => 'Madre: Osteoporosis, Hermana: Cáncer de mama',
                    'surgical_history' => 'Histerectomía (2015), Biopsia mama (2020) - benigna',
                    'notes' => 'Control oncológico anual, densitometría cada 2 años'
                ]
            ],
            [
                'patient' => [
                    'first_name' => 'Diego Alejandro',
                    'last_name' => 'Ramírez Torres',
                    'email' => 'diego.ramirez@email.com',
                    'phone' => '555-2017',
                    'date_of_birth' => '1982-08-14',
                    'gender' => 'male',
                    'address' => 'Av. Boyacá 345, Conjunto Los Cedros',
                    'identification_type' => 'CC',
                    'identification_number' => '65432109',
                    'blood_type' => 'O+',
                    'marital_status' => 'married',
                    'occupation' => 'Fisioterapeuta',
                    'insurance_provider' => 'EPS Sura',
                    'insurance_number' => 'SR-65432109',
                    'status' => 'active'
                ],
                'emergency_contact' => [
                    'name' => 'Claudia Torres',
                    'relationship' => 'spouse',
                    'phone' => '555-2018',
                    'email' => 'claudia.torres@email.com'
                ],
                'medical_history' => [
                    'allergies' => 'Ninguna conocida',
                    'chronic_conditions' => 'Lumbalgia crónica',
                    'medications' => 'Relajante muscular PRN',
                    'family_history' => 'Padre: Problemas de columna',
                    'surgical_history' => 'Ninguna',
                    'notes' => 'Lesión laboral por sobrecarga, manejo conservador'
                ]
            ],
            [
                'patient' => [
                    'first_name' => 'Valentina',
                    'last_name' => 'Castro Jiménez',
                    'email' => 'valentina.castro@email.com',
                    'phone' => '555-2019',
                    'date_of_birth' => '2015-02-20',
                    'gender' => 'female',
                    'address' => 'Calle 23 #45-67, Barrio San José',
                    'identification_type' => 'TI',
                    'identification_number' => '54321098',
                    'blood_type' => 'A+',
                    'marital_status' => 'single',
                    'occupation' => 'Estudiante',
                    'insurance_provider' => 'EPS Famisanar',
                    'insurance_number' => 'FM-54321098',
                    'status' => 'active'
                ],
                'emergency_contact' => [
                    'name' => 'Gloria Jiménez',
                    'relationship' => 'mother',
                    'phone' => '555-2020',
                    'email' => 'gloria.jimenez@email.com'
                ],
                'medical_history' => [
                    'allergies' => 'Chocolate, Colorantes artificiales',
                    'chronic_conditions' => 'Dermatitis atópica',
                    'medications' => 'Crema hidratante diaria, Antihistamínico PRN',
                    'family_history' => 'Madre: Alergias, Tío materno: Asma',
                    'surgical_history' => 'Ninguna',
                    'notes' => 'Control dermatológico regular, evitar triggers conocidos'
                ]
            ]
        ];

        foreach ($patients as $patientData) {
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
        }

        $this->command->info('✅ Pacientes, contactos de emergencia e historiales médicos creados exitosamente!');
    }
} 