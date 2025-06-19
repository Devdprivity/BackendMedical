<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Appointment;
use App\Models\VideoCall;
use App\Models\User;
use App\Models\Patient;
use Carbon\Carbon;

class VideoCallForAllDoctorsSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        // Get all doctors (users with doctor role)
        $doctors = User::where('role', 'doctor')->get();
        $patients = Patient::take(3)->get();
        
        if ($doctors->count() === 0) {
            $this->command->info('No se encontraron doctores en el sistema.');
            return;
        }
        
        if ($patients->count() === 0) {
            $this->command->info('No se encontraron pacientes. Por favor ejecuta PatientSeeder primero.');
            return;
        }

        $this->command->info('Creando citas de videollamada para todos los doctores...');

        foreach ($doctors as $doctor) {
            $this->command->info("Creando citas para: {$doctor->name} ({$doctor->email})");
            
            // Create appointments for each doctor
            $appointmentsData = [
                // Today's appointment
                [
                    'patient_id' => $patients[0]->id,
                    'doctor_id' => $doctor->id,
                    'clinic_id' => $doctor->clinic_id ?? 1,
                    'appointment_date' => Carbon::today()->format('Y-m-d'),
                    'appointment_time' => '10:30',
                    'date_time' => Carbon::today()->setTime(10, 30)->format('Y-m-d H:i:s'),
                    'duration' => 30,
                    'type' => 'video_consultation',
                    'reason' => 'Videoconsulta de seguimiento',
                    'notes' => 'Consulta virtual programada para ' . $doctor->name,
                    'status' => 'scheduled',
                    'video_call_status' => 'pending'
                ],
                // Another today's appointment
                [
                    'patient_id' => $patients[1]->id,
                    'doctor_id' => $doctor->id,
                    'clinic_id' => $doctor->clinic_id ?? 1,
                    'appointment_date' => Carbon::today()->format('Y-m-d'),
                    'appointment_time' => '15:00',
                    'date_time' => Carbon::today()->setTime(15, 0)->format('Y-m-d H:i:s'),
                    'duration' => 45,
                    'type' => 'video_consultation',
                    'reason' => 'Consulta general por videollamada',
                    'notes' => 'Primera consulta virtual con ' . $doctor->name,
                    'status' => 'scheduled',
                    'video_call_status' => 'pending'
                ],
                // Tomorrow's appointment
                [
                    'patient_id' => $patients[2]->id,
                    'doctor_id' => $doctor->id,
                    'clinic_id' => $doctor->clinic_id ?? 1,
                    'appointment_date' => Carbon::tomorrow()->format('Y-m-d'),
                    'appointment_time' => '09:30',
                    'date_time' => Carbon::tomorrow()->setTime(9, 30)->format('Y-m-d H:i:s'),
                    'duration' => 30,
                    'type' => 'video_consultation',
                    'reason' => 'Control médico virtual',
                    'notes' => 'Revisión de resultados por videollamada',
                    'status' => 'scheduled',
                    'video_call_status' => 'pending'
                ]
            ];

            foreach ($appointmentsData as $appointmentData) {
                $videoCallStatus = $appointmentData['video_call_status'];
                unset($appointmentData['video_call_status']);

                // Check if appointment already exists to avoid duplicates
                $existingAppointment = Appointment::where('doctor_id', $doctor->id)
                    ->where('appointment_date', $appointmentData['appointment_date'])
                    ->where('appointment_time', $appointmentData['appointment_time'])
                    ->first();

                if ($existingAppointment) {
                    $this->command->info("  - Cita ya existe: {$appointmentData['appointment_date']} {$appointmentData['appointment_time']}");
                    continue;
                }

                // Create appointment
                $appointment = Appointment::create($appointmentData);
                
                // Create video call
                $roomName = VideoCall::generateRoomName($appointment->id);
                $roomUrl = VideoCall::generateJitsiUrl($roomName);
                
                $videoCallData = [
                    'appointment_id' => $appointment->id,
                    'room_name' => $roomName,
                    'room_url' => $roomUrl,
                    'status' => $videoCallStatus,
                    'recording_enabled' => false
                ];

                VideoCall::create($videoCallData);
                
                $this->command->info("  ✅ Cita creada: {$appointment->appointment_date} {$appointment->appointment_time} - {$appointment->reason}");
            }
        }

        $this->command->info('');
        $this->command->info('🎉 ¡Citas de videollamada creadas para todos los doctores!');
        $this->command->info('');
        $this->command->info('📅 Cada doctor ahora tiene:');
        $this->command->info('   - 2 videoconsultas para hoy (10:30 y 15:00)');
        $this->command->info('   - 1 videoconsulta para mañana (09:30)');
        $this->command->info('');
        $this->command->info('🔍 Ve a la página de Citas Médicas y busca citas con tipo "video_consultation"');
    }
} 