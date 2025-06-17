<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\VitalSign;
use Carbon\Carbon;

class VitalSignSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $vitalSigns = [];
        
        // Generar signos vitales para cada paciente
        for ($patientId = 1; $patientId <= 10; $patientId++) {
            // Crear múltiples registros por paciente (simulando visitas)
            $numRecords = rand(3, 8);
            
            for ($i = 0; $i < $numRecords; $i++) {
                $recordedAt = Carbon::now()->subDays(rand(1, 90))->addHours(rand(8, 17));
                
                // Generar valores realistas basados en edad y condición
                $ageGroup = $this->getAgeGroup($patientId);
                $hasConditions = $this->hasChronicConditions($patientId);
                
                $vital = [
                    'patient_id' => $patientId,
                    'blood_pressure_systolic' => $this->generateSystolicBP($ageGroup, $hasConditions),
                    'blood_pressure_diastolic' => $this->generateDiastolicBP($ageGroup, $hasConditions),
                    'heart_rate' => $this->generateHeartRate($ageGroup, $hasConditions),
                    'temperature' => $this->generateTemperature(),
                    'respiratory_rate' => $this->generateRespiratoryRate($ageGroup),
                    'oxygen_saturation' => $this->generateOxygenSaturation($hasConditions),
                    'weight' => $this->generateWeight($patientId, $ageGroup),
                    'height' => $this->generateHeight($patientId, $ageGroup),
                    'bmi' => null, // Se calculará automáticamente
                    'pain_level' => rand(0, 10),
                    'notes' => $this->generateNotes(),
                    'recorded_at' => $recordedAt,
                    'recorded_by' => 'Enfermera ' . ['García', 'Rodríguez', 'López', 'Martínez', 'Silva'][rand(0, 4)],
                    'created_at' => $recordedAt,
                    'updated_at' => $recordedAt
                ];
                
                // Calcular BMI
                if ($vital['weight'] && $vital['height']) {
                    $heightInMeters = $vital['height'] / 100;
                    $vital['bmi'] = round($vital['weight'] / ($heightInMeters * $heightInMeters), 1);
                }
                
                $vitalSigns[] = $vital;
            }
        }
        
        // Agregar signos vitales recientes para algunos pacientes
        $recentVitals = [
            [
                'patient_id' => 1,
                'blood_pressure_systolic' => 145,
                'blood_pressure_diastolic' => 95,
                'heart_rate' => 82,
                'temperature' => 36.5,
                'respiratory_rate' => 16,
                'oxygen_saturation' => 98,
                'weight' => 78.5,
                'height' => 175,
                'bmi' => 25.6,
                'pain_level' => 2,
                'notes' => 'Paciente refiere leve cefalea',
                'recorded_at' => Carbon::now()->subHours(2),
                'recorded_by' => 'Enfermera García',
                'created_at' => Carbon::now()->subHours(2),
                'updated_at' => Carbon::now()->subHours(2)
            ],
            [
                'patient_id' => 2,
                'blood_pressure_systolic' => 110,
                'blood_pressure_diastolic' => 70,
                'heart_rate' => 95,
                'temperature' => 36.8,
                'respiratory_rate' => 22,
                'oxygen_saturation' => 99,
                'weight' => 45.2,
                'height' => 162,
                'bmi' => 17.2,
                'pain_level' => 0,
                'notes' => 'Paciente pediátrica activa, signos normales',
                'recorded_at' => Carbon::now()->subHours(1),
                'recorded_by' => 'Enfermera Rodríguez',
                'created_at' => Carbon::now()->subHours(1),
                'updated_at' => Carbon::now()->subHours(1)
            ],
            [
                'patient_id' => 3,
                'blood_pressure_systolic' => 160,
                'blood_pressure_diastolic' => 100,
                'heart_rate' => 88,
                'temperature' => 36.2,
                'respiratory_rate' => 18,
                'oxygen_saturation' => 96,
                'weight' => 85.3,
                'height' => 172,
                'bmi' => 28.8,
                'pain_level' => 4,
                'notes' => 'Hipertensión no controlada, ajustar medicación',
                'recorded_at' => Carbon::now()->subMinutes(30),
                'recorded_by' => 'Enfermera López',
                'created_at' => Carbon::now()->subMinutes(30),
                'updated_at' => Carbon::now()->subMinutes(30)
            ]
        ];
        
        $vitalSigns = array_merge($vitalSigns, $recentVitals);
        
        foreach ($vitalSigns as $vitalData) {
            VitalSign::create($vitalData);
        }
        
        $this->command->info('✅ Signos vitales creados exitosamente!');
        $this->command->info('💓 Se crearon ' . count($vitalSigns) . ' registros de signos vitales.');
    }
    
    private function getAgeGroup($patientId)
    {
        // Basado en las fechas de nacimiento de los pacientes del PatientSeeder
        $ages = [
            1 => 39, // Juan Carlos - 1985
            2 => 32, // Ana María - 1992
            3 => 46, // Carlos Eduardo - 1978
            4 => 36, // Laura Patricia - 1988
            5 => 59, // Roberto - 1965
            6 => 14, // Sofía - 2010
            7 => 29, // Andrés Felipe - 1995
            8 => 54, // Patricia - 1970
            9 => 42, // Diego Alejandro - 1982
            10 => 9  // Valentina - 2015
        ];
        
        $age = $ages[$patientId] ?? 35;
        
        if ($age < 18) return 'pediatric';
        if ($age < 65) return 'adult';
        return 'elderly';
    }
    
    private function hasChronicConditions($patientId)
    {
        // Basado en las condiciones médicas del PatientSeeder
        $conditions = [
            1 => true,  // Hipertensión
            2 => true,  // Asma
            3 => true,  // Diabetes, Dislipidemia
            4 => true,  // Rinitis alérgica
            5 => true,  // Hipertensión, Osteoartritis
            6 => false, // Ninguna
            7 => false, // Ninguna
            8 => true,  // Hipotiroidismo, Osteopenia
            9 => true,  // Lumbalgia crónica
            10 => true  // Dermatitis atópica
        ];
        
        return $conditions[$patientId] ?? false;
    }
    
    private function generateSystolicBP($ageGroup, $hasConditions)
    {
        $base = 120;
        
        if ($ageGroup === 'pediatric') $base = 100;
        if ($ageGroup === 'elderly') $base = 130;
        if ($hasConditions) $base += 20;
        
        return $base + rand(-15, 25);
    }
    
    private function generateDiastolicBP($ageGroup, $hasConditions)
    {
        $base = 80;
        
        if ($ageGroup === 'pediatric') $base = 65;
        if ($ageGroup === 'elderly') $base = 85;
        if ($hasConditions) $base += 10;
        
        return $base + rand(-10, 15);
    }
    
    private function generateHeartRate($ageGroup, $hasConditions)
    {
        $base = 72;
        
        if ($ageGroup === 'pediatric') $base = 100;
        if ($ageGroup === 'elderly') $base = 68;
        if ($hasConditions) $base += rand(-5, 10);
        
        return $base + rand(-15, 20);
    }
    
    private function generateTemperature()
    {
        return round(36.0 + (rand(0, 15) / 10), 1);
    }
    
    private function generateRespiratoryRate($ageGroup)
    {
        $base = 16;
        
        if ($ageGroup === 'pediatric') $base = 24;
        if ($ageGroup === 'elderly') $base = 18;
        
        return $base + rand(-4, 6);
    }
    
    private function generateOxygenSaturation($hasConditions)
    {
        $base = 98;
        
        if ($hasConditions) $base -= 2;
        
        return max(90, $base + rand(-2, 2));
    }
    
    private function generateWeight($patientId, $ageGroup)
    {
        // Pesos base aproximados por paciente
        $baseWeights = [
            1 => 78,   // Juan Carlos - adulto
            2 => 58,   // Ana María - adulta joven
            3 => 85,   // Carlos Eduardo - adulto con diabetes
            4 => 62,   // Laura Patricia - adulta
            5 => 82,   // Roberto - adulto mayor
            6 => 40,   // Sofía - niña
            7 => 70,   // Andrés Felipe - joven
            8 => 68,   // Patricia - adulta
            9 => 75,   // Diego Alejandro - adulto
            10 => 28   // Valentina - niña
        ];
        
        $base = $baseWeights[$patientId] ?? 70;
        return $base + rand(-5, 5);
    }
    
    private function generateHeight($patientId, $ageGroup)
    {
        // Alturas base aproximadas por paciente
        $baseHeights = [
            1 => 175,  // Juan Carlos
            2 => 162,  // Ana María
            3 => 172,  // Carlos Eduardo
            4 => 165,  // Laura Patricia
            5 => 178,  // Roberto
            6 => 145,  // Sofía
            7 => 180,  // Andrés Felipe
            8 => 160,  // Patricia
            9 => 174,  // Diego Alejandro
            10 => 120  // Valentina
        ];
        
        return $baseHeights[$patientId] ?? 170;
    }
    
    private function generateNotes()
    {
        $notes = [
            'Paciente colaborador',
            'Signos vitales estables',
            'Sin síntomas agudos',
            'Paciente refiere sentirse bien',
            'Control de rutina',
            'Leve ansiedad durante la toma',
            'Paciente en reposo',
            'Post-ejercicio',
            'Pre-consulta médica',
            'Control post-medicación',
            '',
            '',
            ''
        ];
        
        return $notes[array_rand($notes)];
    }
} 