<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\MedicalExam;
use App\Models\ExamResult;
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

        $instructions = [
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
            '',
            '',
            ''
        ];

        $exams = [];
        $examResults = [];

        for ($i = 1; $i <= 50; $i++) {
            $orderedDate = Carbon::now()->subDays(rand(1, 30));
            $scheduledDate = $orderedDate->copy()->addDays(rand(1, 7));
            
            // Determinar estado basado en las fechas
            $status = 'ordered';
            $performedDate = null;
            
            if ($scheduledDate->isPast()) {
                $status = ['completed', 'pending', 'cancelled'][rand(0, 2)];
                if (rand(1, 100) <= 85) $status = 'completed'; // 85% completados
                
                if ($status === 'completed') {
                    $performedDate = $scheduledDate->copy()->addDays(rand(0, 2));
                }
            } elseif ($scheduledDate->isToday()) {
                $status = ['scheduled', 'in_progress', 'completed'][rand(0, 2)];
            } else {
                $status = 'scheduled';
            }

            $urgencyLevels = ['routine', 'urgent', 'stat'];
            $urgency = $urgencyLevels[rand(0, 2)];
            if (rand(1, 100) <= 70) $urgency = 'routine'; // 70% rutina

            $exam = [
                'patient_id' => rand(1, 10),
                'doctor_id' => rand(1, 10),
                'exam_type' => $examTypes[array_rand($examTypes)],
                'ordered_date' => $orderedDate->format('Y-m-d'),
                'scheduled_date' => $scheduledDate->format('Y-m-d'),
                'performed_date' => $performedDate ? $performedDate->format('Y-m-d') : null,
                'status' => $status,
                'urgency' => $urgency,
                'instructions' => $instructions[array_rand($instructions)],
                'notes' => rand(1, 100) <= 30 ? 'Examen de control' : '',
                'created_at' => $orderedDate,
                'updated_at' => now()
            ];

            $exams[] = $exam;

            // Crear resultados para exámenes completados
            if ($status === 'completed' && $performedDate) {
                $examId = $i; // Asumiendo que los IDs serán secuenciales
                
                $result = $this->generateExamResult($exam['exam_type'], $examId, $performedDate);
                if ($result) {
                    $examResults[] = $result;
                }
            }
        }

        // Insertar exámenes
        foreach ($exams as $examData) {
            MedicalExam::create($examData);
        }

        // Insertar resultados
        foreach ($examResults as $resultData) {
            ExamResult::create($resultData);
        }

        $this->command->info('✅ Exámenes médicos y resultados creados exitosamente!');
        $this->command->info('🧪 Se crearon ' . count($exams) . ' exámenes con ' . count($examResults) . ' resultados.');
    }

    private function generateExamResult($examType, $examId, $performedDate)
    {
        $technicianNames = [
            'Laboratorista García',
            'Tec. Martínez',
            'Lab. Rodríguez',
            'Tec. López',
            'Dr. Silva (Radiólogo)',
            'Dra. Morales (Patóloga)',
            'Tec. Vargas',
            'Lab. Castro'
        ];

        $normalFindings = [
            'Valores dentro de parámetros normales',
            'Estudio normal',
            'Sin alteraciones significativas',
            'Resultados satisfactorios',
            'Dentro de límites normales',
            'Examen normal para la edad'
        ];

        $abnormalFindings = [
            'Valores ligeramente elevados',
            'Se observan alteraciones leves',
            'Requiere seguimiento médico',
            'Hallazgos compatibles con proceso inflamatorio',
            'Alteraciones que requieren evaluación clínica',
            'Valores fuera del rango normal'
        ];

        switch ($examType) {
            case 'Hemograma completo':
                $isNormal = rand(1, 100) <= 75;
                return [
                    'medical_exam_id' => $examId,
                    'results' => $isNormal ? 
                        'Hemoglobina: 14.2 g/dL, Hematocrito: 42%, Leucocitos: 7,500/μL, Plaquetas: 280,000/μL' :
                        'Hemoglobina: 10.8 g/dL (bajo), Hematocrito: 32%, Leucocitos: 12,000/μL (elevado), Plaquetas: 180,000/μL',
                    'interpretation' => $isNormal ? $normalFindings[array_rand($normalFindings)] : $abnormalFindings[array_rand($abnormalFindings)],
                    'performed_date' => $performedDate,
                    'technician_name' => $technicianNames[array_rand($technicianNames)],
                    'created_at' => $performedDate,
                    'updated_at' => now()
                ];

            case 'Química sanguínea':
                $isNormal = rand(1, 100) <= 70;
                return [
                    'medical_exam_id' => $examId,
                    'results' => $isNormal ? 
                        'Glucosa: 95 mg/dL, Creatinina: 0.9 mg/dL, BUN: 15 mg/dL, Ácido úrico: 4.2 mg/dL' :
                        'Glucosa: 165 mg/dL (elevada), Creatinina: 1.4 mg/dL (elevada), BUN: 25 mg/dL, Ácido úrico: 7.8 mg/dL',
                    'interpretation' => $isNormal ? $normalFindings[array_rand($normalFindings)] : 'Hiperglucemia y función renal alterada, requiere evaluación médica',
                    'performed_date' => $performedDate,
                    'technician_name' => $technicianNames[array_rand($technicianNames)],
                    'created_at' => $performedDate,
                    'updated_at' => now()
                ];

            case 'Radiografía de tórax':
                $isNormal = rand(1, 100) <= 80;
                return [
                    'medical_exam_id' => $examId,
                    'results' => $isNormal ? 
                        'Campos pulmonares libres, silueta cardíaca normal, sin infiltrados' :
                        'Infiltrado en lóbulo inferior derecho, silueta cardíaca aumentada',
                    'interpretation' => $isNormal ? 'Radiografía de tórax normal' : 'Hallazgos sugestivos de proceso infeccioso pulmonar',
                    'performed_date' => $performedDate,
                    'technician_name' => $technicianNames[array_rand($technicianNames)],
                    'created_at' => $performedDate,
                    'updated_at' => now()
                ];

            case 'Electrocardiograma':
                $isNormal = rand(1, 100) <= 85;
                return [
                    'medical_exam_id' => $examId,
                    'results' => $isNormal ? 
                        'Ritmo sinusal, FC: 72 lpm, intervalos normales, sin alteraciones del ST-T' :
                        'Ritmo sinusal, FC: 95 lpm, bloqueo incompleto de rama derecha, alteraciones inespecíficas del ST',
                    'interpretation' => $isNormal ? 'ECG normal' : 'Alteraciones menores del sistema de conducción',
                    'performed_date' => $performedDate,
                    'technician_name' => $technicianNames[array_rand($technicianNames)],
                    'created_at' => $performedDate,
                    'updated_at' => now()
                ];

            case 'Examen general de orina':
                $isNormal = rand(1, 100) <= 80;
                return [
                    'medical_exam_id' => $examId,
                    'results' => $isNormal ? 
                        'Color amarillo claro, densidad 1.020, proteínas negativas, glucosa negativa, leucocitos 2-3 por campo' :
                        'Color amarillo oscuro, densidad 1.028, proteínas +, glucosa ++, leucocitos 15-20 por campo',
                    'interpretation' => $isNormal ? $normalFindings[array_rand($normalFindings)] : 'Proteinuria y glucosuria, posible infección urinaria',
                    'performed_date' => $performedDate,
                    'technician_name' => $technicianNames[array_rand($technicianNames)],
                    'created_at' => $performedDate,
                    'updated_at' => now()
                ];

            default:
                // Para otros tipos de exámenes, generar resultado genérico
                $isNormal = rand(1, 100) <= 75;
                return [
                    'medical_exam_id' => $examId,
                    'results' => $isNormal ? 'Estudio realizado sin complicaciones' : 'Se observan hallazgos que requieren correlación clínica',
                    'interpretation' => $isNormal ? $normalFindings[array_rand($normalFindings)] : $abnormalFindings[array_rand($abnormalFindings)],
                    'performed_date' => $performedDate,
                    'technician_name' => $technicianNames[array_rand($technicianNames)],
                    'created_at' => $performedDate,
                    'updated_at' => now()
                ];
        }
    }
} 