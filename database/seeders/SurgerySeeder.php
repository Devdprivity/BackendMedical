<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Surgery;
use Carbon\Carbon;

class SurgerySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $surgeryTypes = [
            'Apendicectomía',
            'Colecistectomía laparoscópica',
            'Hernia inguinal',
            'Cesárea',
            'Histerectomía',
            'Artroscopia de rodilla',
            'Cirugía de cataratas',
            'Amigdalectomía',
            'Mastectomía',
            'Prostatectomía',
            'Bypass gástrico',
            'Cirugía de vesícula',
            'Reparación de fractura',
            'Cirugía de meniscos',
            'Tiroidectomía',
            'Cirugía de colon',
            'Nefrectomía',
            'Cirugía cardíaca',
            'Neurocirugía',
            'Cirugía plástica reconstructiva',
            'Endoscopia terapéutica',
            'Biopsia quirúrgica',
            'Cirugía de columna',
            'Implante de marcapasos',
            'Cirugía de mano'
        ];

        $operatingRooms = [
            'Quirófano 1',
            'Quirófano 2',
            'Quirófano 3',
            'Quirófano 4',
            'Quirófano Central',
            'Sala de Cirugía Menor',
            'Quirófano de Emergencias'
        ];

        $surgeries = [];

        for ($i = 1; $i <= 30; $i++) {
            // Fechas: algunas pasadas, algunas futuras
            $scheduledDate = Carbon::now()->addDays(rand(-15, 30));
            $scheduledTime = ['06:00:00', '07:00:00', '08:00:00', '09:00:00', '10:00:00', '11:00:00', '14:00:00', '15:00:00'][rand(0, 7)];
            
            // Determinar estado basado en la fecha
            $status = 'scheduled';
            $actualStartTime = null;
            $actualEndTime = null;
            $complications = '';
            $outcome = '';
            
            if ($scheduledDate->isPast()) {
                $status = ['completed', 'cancelled', 'postponed'][rand(0, 2)];
                if (rand(1, 100) <= 85) $status = 'completed'; // 85% completadas
                
                if ($status === 'completed') {
                    $startDelay = rand(0, 30); // Retraso de 0-30 minutos
                    $actualStartTime = Carbon::parse($scheduledTime)->addMinutes($startDelay)->format('H:i:s');
                    
                    $duration = rand(45, 180); // 45 minutos a 3 horas
                    $actualEndTime = Carbon::parse($actualStartTime)->addMinutes($duration)->format('H:i:s');
                    
                    // Complicaciones (15% de probabilidad)
                    if (rand(1, 100) <= 15) {
                        $complications = [
                            'Sangrado menor controlado',
                            'Hipotensión transitoria',
                            'Dificultad en la intubación',
                            'Adherencias inesperadas',
                            'Sangrado moderado',
                            'Reacción alérgica menor'
                        ][rand(0, 5)];
                    } else {
                        $complications = 'Ninguna';
                    }
                    
                    $outcome = rand(1, 100) <= 95 ? 'Exitosa' : 'Complicada pero estable';
                }
            } elseif ($scheduledDate->isToday()) {
                $status = ['scheduled', 'in_progress', 'completed'][rand(0, 2)];
                if ($status === 'in_progress') {
                    $actualStartTime = Carbon::parse($scheduledTime)->addMinutes(rand(0, 15))->format('H:i:s');
                }
            }

            $surgery = [
                'patient_id' => rand(1, 10),
                'doctor_id' => rand(1, 10), // Cirujanos
                'surgery_type' => $surgeryTypes[array_rand($surgeryTypes)],
                'scheduled_date' => $scheduledDate->format('Y-m-d'),
                'scheduled_time' => $scheduledTime,
                'duration_minutes' => rand(45, 180),
                'operating_room' => $operatingRooms[array_rand($operatingRooms)],
                'status' => $status,
                'urgency' => rand(1, 100) <= 80 ? 'elective' : 'emergency',
                'anesthesia_type' => ['general', 'local', 'spinal', 'epidural'][rand(0, 3)],
                'pre_operative_notes' => 'Paciente evaluado, apto para cirugía',
                'post_operative_notes' => $status === 'completed' ? 'Cirugía sin complicaciones, paciente estable' : '',
                'actual_start_time' => $actualStartTime,
                'actual_end_time' => $actualEndTime,
                'complications' => $complications,
                'outcome' => $outcome,
                'notes' => rand(1, 100) <= 30 ? 'Cirugía programada' : '',
                'created_at' => $scheduledDate->copy()->subDays(rand(1, 7)),
                'updated_at' => now()
            ];

            $surgeries[] = $surgery;
        }

        // Agregar algunas cirugías específicas para hoy
        $today = Carbon::today();
        $todaySurgeries = [
            [
                'patient_id' => 3,
                'doctor_id' => 3, // Dr. Luis Martínez - Cirujano
                'surgery_type' => 'Colecistectomía laparoscópica',
                'scheduled_date' => $today->format('Y-m-d'),
                'scheduled_time' => '08:00:00',
                'duration_minutes' => 90,
                'operating_room' => 'Quirófano 1',
                'status' => 'completed',
                'urgency' => 'elective',
                'anesthesia_type' => 'general',
                'pre_operative_notes' => 'Colelitiasis sintomática, paciente en ayunas',
                'post_operative_notes' => 'Procedimiento laparoscópico exitoso, vesícula extraída sin complicaciones',
                'actual_start_time' => '08:15:00',
                'actual_end_time' => '09:45:00',
                'complications' => 'Ninguna',
                'outcome' => 'Exitosa',
                'notes' => 'Recuperación satisfactoria',
                'created_at' => $today->copy()->subDays(5),
                'updated_at' => now()
            ],
            [
                'patient_id' => 7,
                'doctor_id' => 7, // Dr. Fernando Castro - Traumatólogo
                'surgery_type' => 'Artroscopia de rodilla',
                'scheduled_date' => $today->format('Y-m-d'),
                'scheduled_time' => '14:00:00',
                'duration_minutes' => 60,
                'operating_room' => 'Quirófano 2',
                'status' => 'in_progress',
                'urgency' => 'elective',
                'anesthesia_type' => 'spinal',
                'pre_operative_notes' => 'Lesión de menisco interno, resonancia confirma desgarro',
                'post_operative_notes' => '',
                'actual_start_time' => '14:10:00',
                'actual_end_time' => null,
                'complications' => '',
                'outcome' => '',
                'notes' => 'Cirugía en curso',
                'created_at' => $today->copy()->subDays(3),
                'updated_at' => now()
            ],
            [
                'patient_id' => 5,
                'doctor_id' => 5, // Dr. Roberto Silva - Neurólogo (consulta pre-quirúrgica)
                'surgery_type' => 'Implante de marcapasos',
                'scheduled_date' => $today->copy()->addDays(1)->format('Y-m-d'),
                'scheduled_time' => '09:00:00',
                'duration_minutes' => 120,
                'operating_room' => 'Quirófano Central',
                'status' => 'scheduled',
                'urgency' => 'urgent',
                'anesthesia_type' => 'local',
                'pre_operative_notes' => 'Bloqueo AV completo, requiere marcapasos definitivo',
                'post_operative_notes' => '',
                'actual_start_time' => null,
                'actual_end_time' => null,
                'complications' => '',
                'outcome' => '',
                'notes' => 'Urgente - bradycardia sintomática',
                'created_at' => $today->copy()->subDay(),
                'updated_at' => now()
            ]
        ];

        $surgeries = array_merge($surgeries, $todaySurgeries);

        foreach ($surgeries as $surgeryData) {
            Surgery::create($surgeryData);
        }

        $this->command->info('✅ Cirugías creadas exitosamente!');
        $this->command->info('🏥 Se crearon ' . count($surgeries) . ' cirugías incluyendo algunas para hoy.');
    }
} 