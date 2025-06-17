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
                    
                    // Determinar estado basado en la fecha
                    $status = 'scheduled';
                    if ($currentDate->isPast()) {
                        $status = ['completed', 'cancelled', 'no_show'][rand(0, 2)];
                        // Más probabilidad de completadas
                        if (rand(1, 100) <= 80) $status = 'completed';
                    } elseif ($currentDate->isToday()) {
                        $status = ['scheduled', 'in_progress', 'completed'][rand(0, 2)];
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
                        '',
                        '',
                        ''
                    ];

                    $appointments[] = [
                        'patient_id' => $patientId,
                        'doctor_id' => $doctorId,
                        'appointment_date' => $currentDate->format('Y-m-d'),
                        'appointment_time' => $time,
                        'reason' => $reasons[array_rand($reasons)],
                        'status' => $status,
                        'notes' => $notes[array_rand($notes)],
                        'duration_minutes' => [30, 45, 60][rand(0, 2)],
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
                'appointment_date' => $today->format('Y-m-d'),
                'appointment_time' => '09:00:00',
                'reason' => 'Control cardiológico',
                'status' => 'completed',
                'notes' => 'Paciente estable, continuar tratamiento',
                'duration_minutes' => 45,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'patient_id' => 2,
                'doctor_id' => 2,
                'appointment_date' => $today->format('Y-m-d'),
                'appointment_time' => '10:30:00',
                'reason' => 'Control pediátrico',
                'status' => 'in_progress',
                'notes' => 'Vacunación programada',
                'duration_minutes' => 30,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'patient_id' => 3,
                'doctor_id' => 3,
                'appointment_date' => $today->format('Y-m-d'),
                'appointment_time' => '14:00:00',
                'reason' => 'Consulta pre-quirúrgica',
                'status' => 'scheduled',
                'notes' => 'Evaluación para cirugía programada',
                'duration_minutes' => 60,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'patient_id' => 4,
                'doctor_id' => 4,
                'appointment_date' => $today->format('Y-m-d'),
                'appointment_time' => '15:30:00',
                'reason' => 'Control prenatal',
                'status' => 'scheduled',
                'notes' => 'Semana 32 de gestación',
                'duration_minutes' => 45,
                'created_at' => now(),
                'updated_at' => now()
            ]
        ];

        $appointments = array_merge($appointments, $todayAppointments);

        // Insertar en lotes para mejor rendimiento
        $chunks = array_chunk($appointments, 50);
        foreach ($chunks as $chunk) {
            Appointment::insert($chunk);
        }

        $this->command->info('✅ Citas creadas exitosamente!');
        $this->command->info('📅 Se crearon citas para las próximas 4 semanas incluyendo citas para hoy.');
    }
} 