<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Appointment;
use Carbon\Carbon;

class AppointmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $baseDate = Carbon::now()->startOfWeek();
        $appointments = [];

        // Generar citas para las próximas 4 semanas
        for ($week = 0; $week < 4; $week++) {
            for ($day = 0; $day < 7; $day++) {
                $currentDate = $baseDate->copy()->addWeeks($week)->addDays($day);
                
                // Solo días laborables para la mayoría de citas
                if ($currentDate->isWeekend()) {
                    continue;
                }

                // Horarios de citas por día
                $timeSlots = [
                    '08:00:00', '08:30:00', '09:00:00', '09:30:00', '10:00:00',
                    '10:30:00', '11:00:00', '11:30:00', '14:00:00', '14:30:00',
                    '15:00:00', '15:30:00', '16:00:00', '16:30:00'
                ];

                foreach ($timeSlots as $index => $time) {
                    // No llenar todos los horarios
                    if (rand(1, 100) > 70) continue;

                    $patientId = rand(1, 10); // Asumiendo 10 pacientes
                    $doctorId = rand(1, 10);  // Asumiendo 10 doctores
                    $clinicId = rand(1, 5);   // Asumiendo 5 clínicas
                    
                    // Crear datetime combinando fecha y hora
                    $dateTime = $currentDate->copy()->setTimeFromTimeString($time);
                    
                    // Verificar si ya existe una cita en este horario
                    $existingAppointment = Appointment::where('doctor_id', $doctorId)
                        ->where('date_time', $dateTime)
                        ->first();
                    
                    if ($existingAppointment) {
                        continue; // Saltar si ya existe
                    }
                    
                    // Determinar estado basado en la fecha
                    $status = 'scheduled';
                    if ($currentDate->isPast()) {
                        $status = ['completed', 'cancelled'][rand(0, 1)];
                        // Más probabilidad de completadas
                        if (rand(1, 100) <= 80) $status = 'completed';
                    } elseif ($currentDate->isToday()) {
                        $status = ['scheduled', 'completed'][rand(0, 1)];
                    }

                    $reasons = [
                        'Consulta general',
                        'Control médico',
                        'Seguimiento tratamiento',
                        'Dolor abdominal',
                        'Cefalea persistente',
                        'Control hipertensión',
                        'Revisión diabetes',
                        'Examen preventivo',
                        'Dolor articular',
                        'Control cardiológico',
                        'Consulta dermatológica',
                        'Evaluación oftalmológica',
                        'Control pediátrico',
                        'Consulta ginecológica',
                        'Dolor de garganta',
                        'Control prenatal',
                        'Evaluación neurológica',
                        'Consulta traumatología',
                        'Control psiquiátrico',
                        'Revisión exámenes'
                    ];

                    $types = [
                        'Consulta General',
                        'Control',
                        'Seguimiento',
                        'Emergencia',
                        'Especialista',
                        'Preventiva',
                        'Pre-quirúrgica',
                        'Post-quirúrgica'
                    ];

                    $notes = [
                        'Primera consulta del paciente',
                        'Paciente con antecedentes de hipertensión',
                        'Control de rutina',
                        'Paciente refiere mejoría',
                        'Solicita renovación de medicamentos',
                        'Seguimiento post-quirúrgico',
                        'Evaluación de síntomas',
                        'Control de signos vitales',
                        'Paciente colaborador',
                        'Requiere exámenes adicionales',
                        null,
                        null,
                        null
                    ];

                    $appointments[] = [
                        'patient_id' => $patientId,
                        'doctor_id' => $doctorId,
                        'clinic_id' => $clinicId,
                        'date_time' => $dateTime,
                        'type' => $types[array_rand($types)],
                        'reason' => $reasons[array_rand($reasons)],
                        'status' => $status,
                        'notes' => $notes[array_rand($notes)],
                        'duration' => [30, 45, 60][rand(0, 2)],
                        'created_at' => now(),
                        'updated_at' => now()
                    ];
                }
            }
        }

        // Agregar algunas citas específicas para hoy
        $today = Carbon::today();
        $todayAppointments = [
            [
                'patient_id' => 1,
                'doctor_id' => 1,
                'clinic_id' => 1,
                'date_time' => $today->copy()->setTime(9, 0),
                'type' => 'Control',
                'reason' => 'Control cardiológico',
                'status' => 'completed',
                'notes' => 'Paciente estable, continuar tratamiento',
                'duration' => 45,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'patient_id' => 2,
                'doctor_id' => 2,
                'clinic_id' => 1,
                'date_time' => $today->copy()->setTime(10, 30),
                'type' => 'Control',
                'reason' => 'Control pediátrico',
                'status' => 'scheduled',
                'notes' => 'Vacunación programada',
                'duration' => 30,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'patient_id' => 3,
                'doctor_id' => 3,
                'clinic_id' => 2,
                'date_time' => $today->copy()->setTime(14, 0),
                'type' => 'Pre-quirúrgica',
                'reason' => 'Consulta pre-quirúrgica',
                'status' => 'scheduled',
                'notes' => 'Evaluación para cirugía programada',
                'duration' => 60,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'patient_id' => 4,
                'doctor_id' => 4,
                'clinic_id' => 2,
                'date_time' => $today->copy()->setTime(15, 30),
                'type' => 'Control',
                'reason' => 'Control prenatal',
                'status' => 'scheduled',
                'notes' => 'Semana 32 de gestación',
                'duration' => 45,
                'created_at' => now(),
                'updated_at' => now()
            ]
        ];

        // Verificar citas específicas de hoy que no existan
        foreach ($todayAppointments as $appointment) {
            $existingAppointment = Appointment::where('doctor_id', $appointment['doctor_id'])
                ->where('date_time', $appointment['date_time'])
                ->first();
            
            if (!$existingAppointment) {
                $appointments[] = $appointment;
            } else {
                $this->command->info("⚠️  Cita para doctor {$appointment['doctor_id']} a las {$appointment['date_time']} ya existe, saltando...");
            }
        }

        // Insertar citas una por una para evitar duplicados
        $created = 0;
        foreach ($appointments as $appointmentData) {
            try {
                // Verificar nuevamente antes de crear
                $existingAppointment = Appointment::where('doctor_id', $appointmentData['doctor_id'])
                    ->where('date_time', $appointmentData['date_time'])
                    ->first();
                
                if (!$existingAppointment) {
                    Appointment::create($appointmentData);
                    $created++;
                }
            } catch (\Exception $e) {
                $this->command->warn("⚠️  Error creando cita: " . $e->getMessage());
            }
        }

        $this->command->info("✅ {$created} citas creadas exitosamente!");
        $this->command->info('📅 Se crearon citas para las próximas 4 semanas incluyendo citas para hoy.');
    }
} 