<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Create doctor-patient relationships for existing data
        $this->createRelationshipsFromPatientCreators();
        $this->createRelationshipsFromAppointments();
        $this->createRelationshipsFromTreatments();
    }

    /**
     * Create relationships based on who created the patients
     */
    private function createRelationshipsFromPatientCreators(): void
    {
        $this->command->info('Creating relationships from patient creators...');

        // Get patients created by doctors
        $patientsWithDoctorCreators = DB::table('patients')
            ->join('users', 'patients.created_by', '=', 'users.id')
            ->join('doctors', 'users.id', '=', 'doctors.user_id')
            ->where('users.role', 'doctor')
            ->whereNotNull('patients.created_by')
            ->select('patients.id as patient_id', 'doctors.id as doctor_id', 'users.clinic_id', 'patients.created_at')
            ->get();

        foreach ($patientsWithDoctorCreators as $patient) {
            // Check if relationship already exists
            $exists = DB::table('doctor_patient_relationships')
                ->where('doctor_id', $patient->doctor_id)
                ->where('patient_id', $patient->patient_id)
                ->exists();

            if (!$exists) {
                DB::table('doctor_patient_relationships')->insert([
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
            }
        }

        $this->command->info(sprintf('Created %d relationships from patient creators', $patientsWithDoctorCreators->count()));
    }

    /**
     * Create relationships based on existing appointments
     */
    private function createRelationshipsFromAppointments(): void
    {
        $this->command->info('Creating relationships from appointments...');

        // Get unique doctor-patient pairs from appointments
        $appointmentPairs = DB::table('appointments')
            ->join('doctors', 'appointments.doctor_id', '=', 'doctors.id')
            ->join('patients', 'appointments.patient_id', '=', 'patients.id')
            ->select('doctors.id as doctor_id', 'patients.id as patient_id',
                    DB::raw('MIN(appointments.date_time) as first_appointment'),
                    'appointments.clinic_id')
            ->groupBy('doctors.id', 'patients.id', 'appointments.clinic_id')
            ->get();

        foreach ($appointmentPairs as $pair) {
            // Check if relationship already exists
            $exists = DB::table('doctor_patient_relationships')
                ->where('doctor_id', $pair->doctor_id)
                ->where('patient_id', $pair->patient_id)
                ->exists();

            if (!$exists) {
                DB::table('doctor_patient_relationships')->insert([
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
            }
        }

        $this->command->info(sprintf('Created %d relationships from appointments', $appointmentPairs->count()));
    }

    /**
     * Create relationships based on existing treatments
     */
    private function createRelationshipsFromTreatments(): void
    {
        $this->command->info('Creating relationships from treatments...');

        // Get unique doctor-patient pairs from treatments
        $treatmentPairs = DB::table('treatments')
            ->join('doctors', 'treatments.doctor_id', '=', 'doctors.id')
            ->join('patients', 'treatments.patient_id', '=', 'patients.id')
            ->select('doctors.id as doctor_id', 'patients.id as patient_id',
                    DB::raw('MIN(treatments.start_date) as first_treatment'),
                    'treatments.clinic_id')
            ->groupBy('doctors.id', 'patients.id', 'treatments.clinic_id')
            ->get();

        foreach ($treatmentPairs as $pair) {
            // Check if relationship already exists
            $exists = DB::table('doctor_patient_relationships')
                ->where('doctor_id', $pair->doctor_id)
                ->where('patient_id', $pair->patient_id)
                ->exists();

            if (!$exists) {
                DB::table('doctor_patient_relationships')->insert([
                    'doctor_id' => $pair->doctor_id,
                    'patient_id' => $pair->patient_id,
                    'clinic_id' => $pair->clinic_id,
                    'relationship_type' => 'consulting',
                    'started_at' => $pair->first_treatment,
                    'status' => 'active',
                    'notes' => 'Relación creada automáticamente - basada en tratamientos existentes',
                    'permissions' => json_encode(['view_history', 'prescribe', 'update_records']),
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
        }

        $this->command->info(sprintf('Created %d relationships from treatments', $treatmentPairs->count()));
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove auto-generated relationships
        DB::table('doctor_patient_relationships')
            ->where('notes', 'like', '%Relación creada automáticamente%')
            ->delete();
    }
};
