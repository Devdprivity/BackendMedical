<?php

echo "🚀 Creando relaciones médico-paciente...\n";

// Incluir Bootstrap de Laravel para usar los modelos
require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

try {
    // Usar los modelos de Laravel
    echo "✅ Laravel inicializado correctamente\n";

    // 1. Crear relaciones para pacientes creados por médicos
    echo "\n📝 Buscando pacientes creados por médicos...\n";

    $patientsWithDoctors = \DB::table('patients')
        ->join('users', 'patients.created_by', '=', 'users.id')
        ->join('doctors', 'users.id', '=', 'doctors.user_id')
        ->where('users.role', 'doctor')
        ->whereNotNull('patients.created_by')
        ->select('patients.id as patient_id', 'doctors.id as doctor_id', 'users.clinic_id', 'patients.created_at')
        ->get();

    echo "   Encontrados: " . $patientsWithDoctors->count() . " pacientes creados por médicos\n";

    $created1 = 0;
    foreach ($patientsWithDoctors as $patient) {
        $exists = \DB::table('doctor_patient_relationships')
            ->where('doctor_id', $patient->doctor_id)
            ->where('patient_id', $patient->patient_id)
            ->exists();

        if (!$exists) {
            \DB::table('doctor_patient_relationships')->insert([
                'doctor_id' => $patient->doctor_id,
                'patient_id' => $patient->patient_id,
                'clinic_id' => $patient->clinic_id,
                'relationship_type' => 'primary',
                'started_at' => $patient->created_at,
                'status' => 'active',
                'notes' => 'Relación creada automáticamente - doctor creó al paciente',
                'permissions' => json_encode(['view_history', 'prescribe', 'update_records', 'schedule_appointments', 'emergency_access']),
                'created_at' => now(),
                'updated_at' => now()
            ]);
            $created1++;
        }
    }

    echo "   ✅ Creadas $created1 relaciones desde creadores de pacientes\n";

    // 2. Crear relaciones basadas en citas existentes
    echo "\n📅 Buscando relaciones médico-paciente desde citas...\n";

    $appointmentPairs = \DB::table('appointments')
        ->join('doctors', 'appointments.doctor_id', '=', 'doctors.id')
        ->join('patients', 'appointments.patient_id', '=', 'patients.id')
        ->select('doctors.id as doctor_id', 'patients.id as patient_id',
                \DB::raw('MIN(appointments.date_time) as first_appointment'),
                'appointments.clinic_id')
        ->groupBy('doctors.id', 'patients.id', 'appointments.clinic_id')
        ->get();

    echo "   Encontrados: " . $appointmentPairs->count() . " pares doctor-paciente desde citas\n";

    $created2 = 0;
    foreach ($appointmentPairs as $pair) {
        $exists = \DB::table('doctor_patient_relationships')
            ->where('doctor_id', $pair->doctor_id)
            ->where('patient_id', $pair->patient_id)
            ->exists();

        if (!$exists) {
            \DB::table('doctor_patient_relationships')->insert([
                'doctor_id' => $pair->doctor_id,
                'patient_id' => $pair->patient_id,
                'clinic_id' => $pair->clinic_id,
                'relationship_type' => 'consulting',
                'started_at' => date('Y-m-d', strtotime($pair->first_appointment)),
                'status' => 'active',
                'notes' => 'Relación creada automáticamente - basada en citas existentes',
                'permissions' => json_encode(['view_history', 'prescribe', 'update_records']),
                'created_at' => now(),
                'updated_at' => now()
            ]);
            $created2++;
        }
    }

    echo "   ✅ Creadas $created2 relaciones desde citas\n";

    // 3. Verificar total de relaciones
    $totalRelations = \DB::table('doctor_patient_relationships')->count();
    echo "\n📊 Resumen:\n";
    echo "   - Relaciones desde creadores: $created1\n";
    echo "   - Relaciones desde citas: $created2\n";
    echo "   - Total relaciones en BD: $totalRelations\n";

    echo "\n🎉 ¡Proceso completado exitosamente!\n";
    echo "   Los médicos ahora pueden ver sus pacientes.\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "   Línea: " . $e->getLine() . "\n";
    echo "   Archivo: " . $e->getFile() . "\n";
}

?>
