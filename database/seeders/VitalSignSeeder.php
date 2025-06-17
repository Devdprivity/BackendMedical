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
                $measuredAt = Carbon::now()->subDays(rand(1, 90))->addHours(rand(8, 17));
                
                // Generar valores realistas basados en edad y condición
                $ageGroup = $this->getAgeGroup($patientId);
                $hasConditions = $this->hasChronicConditions($patientId);
                
                // Generar presión arterial como string (ej: "120/80")
                $systolic = $this->generateSystolicBP($ageGroup, $hasConditions);
                $diastolic = $this->generateDiastolicBP($ageGroup, $hasConditions);
                $bloodPressure = $systolic . '/' . $diastolic;
                
                // Verificar si ya existe un registro similar
                $existingVital = VitalSign::where('patient_id', $patientId)
                    ->where('measured_at', $measuredAt)
                    ->first();
                
                if ($existingVital) {
                    continue; // Saltar si ya existe
                }
                
                $vital = [
                    'patient_id' => $patientId,
                    'weight' => $this->generateWeight($patientId, $ageGroup),
                    'height' => $this->generateHeight($patientId, $ageGroup),
                    'blood_pressure' => $bloodPressure,
                    'heart_rate' => $this->generateHeartRate($ageGroup, $hasConditions),
                    'temperature' => $this->generateTemperature(),
                    'measured_at' => $measuredAt,
                    'created_at' => now(),
                    'updated_at' => now()
                ];
                
                $vitalSigns[] = $vital;
            }
        }
        
        // Agregar signos vitales recientes para algunos pacientes
        $today = Carbon::today();
        $recentVitals = [
            [
                'patient_id' => 1,
                'weight' => 78.5,
                'height' => 175.0,
                'blood_pressure' => '145/95',
                'heart_rate' => 82,
                'temperature' => 36.5,
                'measured_at' => $today->copy()->subHours(2),
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'patient_id' => 2,
                'weight' => 45.2,
                'height' => 162.0,
                'blood_pressure' => '110/70',
                'heart_rate' => 95,
                'temperature' => 36.8,
                'measured_at' => $today->copy()->subHours(1),
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'patient_id' => 3,
                'weight' => 85.3,
                'height' => 172.0,
                'blood_pressure' => '160/100',
                'heart_rate' => 88,
                'temperature' => 36.2,
                'measured_at' => $today->copy()->subMinutes(30),
                'created_at' => now(),
                'updated_at' => now()
            ]
        ];
        
        // Verificar signos vitales recientes que no existan
        foreach ($recentVitals as $vital) {
            $existingVital = VitalSign::where('patient_id', $vital['patient_id'])
                ->where('measured_at', $vital['measured_at'])
                ->first();
            
            if (!$existingVital) {
                $vitalSigns[] = $vital;
            } else {
                $this->command->info("⚠️  Signos vitales para paciente {$vital['patient_id']} en {$vital['measured_at']} ya existen, saltando...");
            }
        }
        
        // Insertar signos vitales uno por uno para evitar duplicados
        $created = 0;
        foreach ($vitalSigns as $vitalData) {
            try {
                // Verificar nuevamente antes de crear
                $existingVital = VitalSign::where('patient_id', $vitalData['patient_id'])
                    ->where('measured_at', $vitalData['measured_at'])
                    ->first();
                
                if (!$existingVital) {
                    VitalSign::create($vitalData);
                    $created++;
                }
            } catch (\Exception $e) {
                $this->command->warn("⚠️  Error creando signos vitales: " . $e->getMessage());
            }
        }
        
        $this->command->info("✅ {$created} registros de signos vitales creados exitosamente!");
        $this->command->info('💓 Se crearon signos vitales para múltiples visitas de pacientes.');
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
        // Temperatura normal con pequeñas variaciones
        return round(36.5 + (rand(-10, 15) / 10), 1);
    }
    
    private function generateWeight($patientId, $ageGroup)
    {
        // Pesos base realistas por paciente
        $baseWeights = [
            1 => 78,   // Juan Carlos - adulto
            2 => 45,   // Ana María - adulta delgada
            3 => 85,   // Carlos Eduardo - adulto con sobrepeso
            4 => 62,   // Laura Patricia - adulta
            5 => 72,   // Roberto - adulto mayor
            6 => 50,   // Sofía - adolescente
            7 => 70,   // Andrés Felipe - adulto joven
            8 => 68,   // Patricia - adulta
            9 => 80,   // Diego Alejandro - adulto
            10 => 32   // Valentina - niña
        ];
        
        $baseWeight = $baseWeights[$patientId] ?? 70;
        return $baseWeight + rand(-3, 3) + (rand(0, 10) / 10); // Variación pequeña
    }
    
    private function generateHeight($patientId, $ageGroup)
    {
        // Alturas base por paciente (en cm)
        $baseHeights = [
            1 => 175,  // Juan Carlos
            2 => 162,  // Ana María
            3 => 172,  // Carlos Eduardo
            4 => 165,  // Laura Patricia
            5 => 168,  // Roberto
            6 => 155,  // Sofía - adolescente
            7 => 178,  // Andrés Felipe
            8 => 160,  // Patricia
            9 => 180,  // Diego Alejandro
            10 => 135  // Valentina - niña
        ];
        
        $baseHeight = $baseHeights[$patientId] ?? 170;
        
        // Para niños, la altura puede variar más (crecimiento)
        if ($ageGroup === 'pediatric') {
            return $baseHeight + rand(-2, 5);
        }
        
        return $baseHeight; // Adultos mantienen altura constante
    }
} 