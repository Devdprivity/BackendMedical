<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\MedicalExam;
use Carbon\Carbon;

class MedicalExamSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $examTypes = [
            'Hemograma completo',
            'Química sanguínea',
            'Perfil lipídico',
            'Glucosa en ayunas',
            'Hemoglobina glicosilada',
            'Examen general de orina',
            'Radiografía de tórax',
            'Electrocardiograma',
            'Ecografía abdominal',
            'Tomografía computarizada',
            'Resonancia magnética',
            'Mamografía',
            'Papanicolaou',
            'Densitometría ósea',
            'Ecocardiograma',
            'Prueba de esfuerzo',
            'Endoscopia digestiva',
            'Colonoscopia',
            'Biopsia',
            'Cultivo de orina',
            'TSH',
            'T3 y T4',
            'PSA',
            'Marcadores tumorales',
            'Serología para hepatitis',
            'VIH',
            'Radiografía de columna',
            'Ecografía pélvica',
            'Espirometría',
            'Holter de 24 horas'
        ];

        $laboratoryAreas = [
            'Laboratorio Clínico',
            'Hematología',
            'Bioquímica',
            'Microbiología',
            'Radiología',
            'Cardiología',
            'Ecografía',
            'Tomografía',
            'Resonancia Magnética',
            'Mamografía',
            'Endoscopia',
            'Patología',
            'Neumología'
        ];

        $preparations = [
            'Paciente debe venir en ayunas de 12 horas',
            'No tomar medicamentos antes del examen',
            'Traer exámenes previos para comparación',
            'Beber abundante agua antes del examen',
            'No consumir alcohol 24 horas antes',
            'Evitar ejercicio intenso el día anterior',
            'Venir con vejiga llena',
            'Suspender anticoagulantes según indicación médica',
            'Traer acompañante',
            'Usar ropa cómoda y sin metales',
            null,
            null,
            null
        ];

        $exams = [];

        for ($i = 1; $i <= 50; $i++) {
            $scheduledDate = Carbon::now()->addDays(rand(1, 30))->setTime(rand(8, 16), [0, 30][rand(0, 1)]);
            
            // Algunos exámenes en el pasado (completados)
            if (rand(1, 100) <= 40) {
                $scheduledDate = Carbon::now()->subDays(rand(1, 15))->setTime(rand(8, 16), [0, 30][rand(0, 1)]);
            }
            
            // Determinar estado basado en las fechas
            $status = 'scheduled';
            if ($scheduledDate->isPast()) {
                $status = ['completed', 'cancelled'][rand(0, 1)];
                if (rand(1, 100) <= 85) $status = 'completed'; // 85% completados
            } elseif ($scheduledDate->isToday()) {
                $status = ['scheduled', 'in_progress', 'completed'][rand(0, 2)];
            }

            $patientId = rand(1, 10);
            $doctorId = rand(1, 10);
            $examType = $examTypes[array_rand($examTypes)];
            
            // Verificar si ya existe un examen similar
            $existingExam = MedicalExam::where('patient_id', $patientId)
                ->where('requesting_doctor_id', $doctorId)
                ->where('exam_type', $examType)
                ->where('scheduled_date', $scheduledDate)
                ->first();
            
            if ($existingExam) {
                continue; // Saltar si ya existe
            }

            $exam = [
                'patient_id' => $patientId,
                'requesting_doctor_id' => $doctorId,
                'exam_type' => $examType,
                'scheduled_date' => $scheduledDate,
                'laboratory_area' => $laboratoryAreas[array_rand($laboratoryAreas)],
                'preparation_required' => $preparations[array_rand($preparations)],
                'notes' => rand(1, 100) <= 30 ? 'Examen de control' : null,
                'status' => $status,
                'created_at' => now(),
                'updated_at' => now()
            ];

            $exams[] = $exam;
        }

        // Agregar algunos exámenes específicos para hoy
        $today = Carbon::today();
        $todayExams = [
            [
                'patient_id' => 1,
                'requesting_doctor_id' => 1,
                'exam_type' => 'Hemograma completo',
                'scheduled_date' => $today->copy()->setTime(9, 0),
                'laboratory_area' => 'Laboratorio Clínico',
                'preparation_required' => 'Paciente debe venir en ayunas de 12 horas',
                'notes' => 'Control post-tratamiento',
                'status' => 'scheduled',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'patient_id' => 2,
                'requesting_doctor_id' => 2,
                'exam_type' => 'Radiografía de tórax',
                'scheduled_date' => $today->copy()->setTime(11, 30),
                'laboratory_area' => 'Radiología',
                'preparation_required' => 'Usar ropa cómoda y sin metales',
                'notes' => 'Evaluación pre-quirúrgica',
                'status' => 'in_progress',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'patient_id' => 3,
                'requesting_doctor_id' => 3,
                'exam_type' => 'Electrocardiograma',
                'scheduled_date' => $today->copy()->setTime(14, 0),
                'laboratory_area' => 'Cardiología',
                'preparation_required' => null,
                'notes' => 'Control cardiológico rutinario',
                'status' => 'completed',
                'created_at' => now(),
                'updated_at' => now()
            ]
        ];

        // Verificar exámenes específicos de hoy que no existan
        foreach ($todayExams as $exam) {
            $existingExam = MedicalExam::where('patient_id', $exam['patient_id'])
                ->where('requesting_doctor_id', $exam['requesting_doctor_id'])
                ->where('exam_type', $exam['exam_type'])
                ->where('scheduled_date', $exam['scheduled_date'])
                ->first();
            
            if (!$existingExam) {
                $exams[] = $exam;
            } else {
                $this->command->info("⚠️  Examen {$exam['exam_type']} para paciente {$exam['patient_id']} ya existe, saltando...");
            }
        }

        // Insertar exámenes uno por uno para evitar duplicados
        $created = 0;
        foreach ($exams as $examData) {
            try {
                // Verificar nuevamente antes de crear
                $existingExam = MedicalExam::where('patient_id', $examData['patient_id'])
                    ->where('requesting_doctor_id', $examData['requesting_doctor_id'])
                    ->where('exam_type', $examData['exam_type'])
                    ->where('scheduled_date', $examData['scheduled_date'])
                    ->first();
                
                if (!$existingExam) {
                    MedicalExam::create($examData);
                    $created++;
                }
            } catch (\Exception $e) {
                $this->command->warn("⚠️  Error creando examen: " . $e->getMessage());
            }
        }

        $this->command->info("✅ {$created} exámenes médicos creados exitosamente!");
        $this->command->info('🧪 Se crearon exámenes programados para las próximas semanas incluyendo algunos para hoy.');
    }
} 