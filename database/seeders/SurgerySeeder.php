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

        $anesthesiaTypes = [
            'general',
            'local',
            'spinal',
            'epidural',
            'sedación consciente'
        ];

        $surgicalEquipment = [
            ['Laparoscopio', 'Monitor HD', 'Insuflador CO2'],
            ['Microscopio quirúrgico', 'Electrocauterio'],
            ['Artroscopio', 'Shaver', 'Bomba de irrigación'],
            ['Set básico de cirugía', 'Electrocauterio'],
            ['Monitor cardíaco', 'Desfibrilador', 'Marcapasos temporal'],
            ['Neuronavegador', 'Microscopio', 'Monitor neurofisiológico'],
            ['Set de traumatología', 'Taladro óseo', 'Placas y tornillos']
        ];

        $surgeries = [];

        for ($i = 1; $i <= 30; $i++) {
            // Fechas: algunas pasadas, algunas futuras
            $dateTime = Carbon::now()->addDays(rand(-15, 30))->setTime(rand(6, 16), [0, 30][rand(0, 1)]);
            
            // Determinar estado basado en la fecha
            $status = 'scheduled';
            if ($dateTime->isPast()) {
                $status = ['completed', 'cancelled'][rand(0, 1)];
                if (rand(1, 100) <= 85) $status = 'completed'; // 85% completadas
            } elseif ($dateTime->isToday()) {
                $status = ['scheduled', 'in_progress', 'completed'][rand(0, 2)];
            }

            $mainSurgeonId = rand(1, 10);
            $patientId = rand(1, 10);
            $surgeryType = $surgeryTypes[array_rand($surgeryTypes)];
            
            // Verificar si ya existe una cirugía similar
            $existingSurgery = Surgery::where('patient_id', $patientId)
                ->where('main_surgeon_id', $mainSurgeonId)
                ->where('surgery_type', $surgeryType)
                ->where('date_time', $dateTime)
                ->first();
            
            if ($existingSurgery) {
                continue; // Saltar si ya existe
            }

            // Cirujanos asistentes (30% de probabilidad de tener asistentes)
            $assistantSurgeons = [];
            if (rand(1, 100) <= 30) {
                $numAssistants = rand(1, 2);
                for ($j = 0; $j < $numAssistants; $j++) {
                    $assistantId = rand(1, 10);
                    if ($assistantId !== $mainSurgeonId && !in_array($assistantId, $assistantSurgeons)) {
                        $assistantSurgeons[] = $assistantId;
                    }
                }
            }

            $surgery = [
                'patient_id' => $patientId,
                'main_surgeon_id' => $mainSurgeonId,
                'assistant_surgeons' => json_encode($assistantSurgeons),
                'date_time' => $dateTime,
                'estimated_duration' => rand(45, 240), // 45 minutos a 4 horas
                'surgery_type' => $surgeryType,
                'operating_room' => $operatingRooms[array_rand($operatingRooms)],
                'anesthesia_type' => $anesthesiaTypes[array_rand($anesthesiaTypes)],
                'required_equipment' => json_encode($surgicalEquipment[array_rand($surgicalEquipment)]),
                'preop_notes' => rand(1, 100) <= 70 ? 'Paciente evaluado, apto para cirugía' : null,
                'status' => $status,
                'created_at' => now(),
                'updated_at' => now()
            ];

            $surgeries[] = $surgery;
        }

        // Agregar algunas cirugías específicas para hoy y mañana
        $today = Carbon::today();
        $todaySurgeries = [
            [
                'patient_id' => 3,
                'main_surgeon_id' => 3,
                'assistant_surgeons' => json_encode([4]),
                'date_time' => $today->copy()->setTime(8, 0),
                'estimated_duration' => 90,
                'surgery_type' => 'Colecistectomía laparoscópica',
                'operating_room' => 'Quirófano 1',
                'anesthesia_type' => 'general',
                'required_equipment' => json_encode(['Laparoscopio', 'Monitor HD', 'Insuflador CO2']),
                'preop_notes' => 'Colelitiasis sintomática, paciente en ayunas',
                'status' => 'completed',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'patient_id' => 7,
                'main_surgeon_id' => 7,
                'assistant_surgeons' => json_encode([]),
                'date_time' => $today->copy()->setTime(14, 0),
                'estimated_duration' => 60,
                'surgery_type' => 'Artroscopia de rodilla',
                'operating_room' => 'Quirófano 2',
                'anesthesia_type' => 'spinal',
                'required_equipment' => json_encode(['Artroscopio', 'Shaver', 'Bomba de irrigación']),
                'preop_notes' => 'Lesión de menisco interno, resonancia confirma desgarro',
                'status' => 'in_progress',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'patient_id' => 5,
                'main_surgeon_id' => 5,
                'assistant_surgeons' => json_encode([2]),
                'date_time' => $today->copy()->addDay()->setTime(9, 0),
                'estimated_duration' => 120,
                'surgery_type' => 'Implante de marcapasos',
                'operating_room' => 'Quirófano Central',
                'anesthesia_type' => 'local',
                'required_equipment' => json_encode(['Monitor cardíaco', 'Desfibrilador', 'Marcapasos temporal']),
                'preop_notes' => 'Bloqueo AV completo, requiere marcapasos definitivo - URGENTE',
                'status' => 'scheduled',
                'created_at' => now(),
                'updated_at' => now()
            ]
        ];

        // Verificar cirugías específicas que no existan
        foreach ($todaySurgeries as $surgery) {
            $existingSurgery = Surgery::where('patient_id', $surgery['patient_id'])
                ->where('main_surgeon_id', $surgery['main_surgeon_id'])
                ->where('surgery_type', $surgery['surgery_type'])
                ->where('date_time', $surgery['date_time'])
                ->first();
            
            if (!$existingSurgery) {
                $surgeries[] = $surgery;
            } else {
                $this->command->info("⚠️  Cirugía {$surgery['surgery_type']} para paciente {$surgery['patient_id']} ya existe, saltando...");
            }
        }

        // Insertar cirugías una por una para evitar duplicados
        $created = 0;
        foreach ($surgeries as $surgeryData) {
            try {
                // Verificar nuevamente antes de crear
                $existingSurgery = Surgery::where('patient_id', $surgeryData['patient_id'])
                    ->where('main_surgeon_id', $surgeryData['main_surgeon_id'])
                    ->where('surgery_type', $surgeryData['surgery_type'])
                    ->where('date_time', $surgeryData['date_time'])
                    ->first();
                
                if (!$existingSurgery) {
                    Surgery::create($surgeryData);
                    $created++;
                }
            } catch (\Exception $e) {
                $this->command->warn("⚠️  Error creando cirugía: " . $e->getMessage());
            }
        }

        $this->command->info("✅ {$created} cirugías creadas exitosamente!");
        $this->command->info('🏥 Se crearon cirugías programadas incluyendo algunas para hoy y mañana.');
    }
} 