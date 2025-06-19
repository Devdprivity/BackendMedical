<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Appointment;
use App\Models\VideoCall;
use App\Models\User;
use App\Models\Patient;
use Carbon\Carbon;

class VideoCallSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        // Get a doctor and some patients
        $doctor = User::where('role', 'doctor')->first();
        $patients = Patient::take(3)->get();
        
        if (!$doctor || $patients->count() === 0) {
            $this->command->info('No doctors or patients found. Please run DoctorSeeder and PatientSeeder first.');
            return;
        }

        $this->command->info('Creating sample appointments with video calls...');

        // Create appointments for different scenarios
        $appointmentsData = [
            // Today's appointments
            [
                'patient_id' => $patients[0]->id,
                'doctor_id' => $doctor->id,
                'clinic_id' => 1,
                'appointment_date' => Carbon::today()->format('Y-m-d'),
                'appointment_time' => '10:00',
                'date_time' => Carbon::today()->setTime(10, 0)->format('Y-m-d H:i:s'),
                'duration' => 30,
                'type' => 'video_consultation',
                'reason' => 'Consulta de seguimiento - Videollamada',
                'notes' => 'Consulta virtual programada',
                'status' => 'scheduled',
                'video_call_status' => 'pending'
            ],
            [
                'patient_id' => $patients[1]->id,
                'doctor_id' => $doctor->id,
                'clinic_id' => 1,
                'appointment_date' => Carbon::today()->format('Y-m-d'),
                'appointment_time' => '14:30',
                'date_time' => Carbon::today()->setTime(14, 30)->format('Y-m-d H:i:s'),
                'duration' => 45,
                'type' => 'video_consultation',
                'reason' => 'Consulta general - Telemedicina',
                'notes' => 'Primera consulta virtual',
                'status' => 'scheduled',
                'video_call_status' => 'pending'
            ],
            // Tomorrow's appointments
            [
                'patient_id' => $patients[2]->id,
                'doctor_id' => $doctor->id,
                'clinic_id' => 1,
                'appointment_date' => Carbon::tomorrow()->format('Y-m-d'),
                'appointment_time' => '09:00',
                'date_time' => Carbon::tomorrow()->setTime(9, 0)->format('Y-m-d H:i:s'),
                'duration' => 30,
                'type' => 'video_consultation',
                'reason' => 'Control médico virtual',
                'notes' => 'Revisión de resultados por videollamada',
                'status' => 'scheduled',
                'video_call_status' => 'pending'
            ],
            // Completed video consultation
            [
                'patient_id' => $patients[0]->id,
                'doctor_id' => $doctor->id,
                'clinic_id' => 1,
                'appointment_date' => Carbon::yesterday()->format('Y-m-d'),
                'appointment_time' => '11:00',
                'date_time' => Carbon::yesterday()->setTime(11, 0)->format('Y-m-d H:i:s'),
                'duration' => 30,
                'type' => 'video_consultation',
                'reason' => 'Consulta completada por videollamada',
                'notes' => 'Consulta virtual exitosa',
                'status' => 'completed',
                'video_call_status' => 'completed'
            ],
            // Regular appointment (no video call)
            [
                'patient_id' => $patients[1]->id,
                'doctor_id' => $doctor->id,
                'clinic_id' => 1,
                'appointment_date' => Carbon::today()->format('Y-m-d'),
                'appointment_time' => '16:00',
                'date_time' => Carbon::today()->setTime(16, 0)->format('Y-m-d H:i:s'),
                'duration' => 30,
                'type' => 'consultation',
                'reason' => 'Consulta presencial',
                'notes' => 'Consulta en consultorio',
                'status' => 'scheduled',
                'video_call_status' => null
            ]
        ];

        foreach ($appointmentsData as $appointmentData) {
            $videoCallStatus = $appointmentData['video_call_status'];
            unset($appointmentData['video_call_status']);

            // Create appointment
            $appointment = Appointment::create($appointmentData);
            
            // Create video call if it's a video consultation
            if ($videoCallStatus !== null) {
                $roomName = VideoCall::generateRoomName($appointment->id);
                $roomUrl = VideoCall::generateJitsiUrl($roomName);
                
                $videoCallData = [
                    'appointment_id' => $appointment->id,
                    'room_name' => $roomName,
                    'room_url' => $roomUrl,
                    'status' => $videoCallStatus,
                    'recording_enabled' => false
                ];

                // Add timing data for completed calls
                if ($videoCallStatus === 'completed') {
                    $startTime = Carbon::parse($appointment->date_time);
                    $endTime = $startTime->copy()->addMinutes(25); // 25 minute call
                    
                    $videoCallData['started_at'] = $startTime;
                    $videoCallData['ended_at'] = $endTime;
                    $videoCallData['duration_minutes'] = 25;
                    $videoCallData['participants'] = [
                        [
                            'user_id' => $doctor->id,
                            'name' => $doctor->name,
                            'role' => 'doctor',
                            'type' => 'host',
                            'joined_at' => $startTime->toISOString()
                        ],
                        [
                            'user_id' => $appointment->patient_id,
                            'name' => $appointment->patient->name,
                            'role' => 'patient',
                            'type' => 'participant',
                            'joined_at' => $startTime->addMinutes(2)->toISOString()
                        ]
                    ];
                    $videoCallData['notes'] = 'Consulta virtual completada exitosamente. El paciente se mostró colaborativo y se resolvieron todas sus dudas.';
                }

                VideoCall::create($videoCallData);
                
                $this->command->info("Created appointment with video call: {$appointment->reason} - Status: {$videoCallStatus}");
            } else {
                $this->command->info("Created regular appointment: {$appointment->reason}");
            }
        }

        $this->command->info('Video call seeder completed successfully!');
        $this->command->info('');
        $this->command->info('Sample data created:');
        $this->command->info('- 2 scheduled video consultations for today');
        $this->command->info('- 1 scheduled video consultation for tomorrow');
        $this->command->info('- 1 completed video consultation from yesterday');
        $this->command->info('- 1 regular appointment (no video call)');
        $this->command->info('');
        $this->command->info('You can now test the video call system in the appointments page!');
    }
} 