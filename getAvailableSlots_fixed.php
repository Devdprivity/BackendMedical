<?php
/**
 * Get available time slots for a doctor on a specific date
 * VERSIÓN CORREGIDA CON MANEJO DE ERRORES
 */
public function getAvailableSlots(Request $request): JsonResponse
{
    try {
        $request->validate([
            'doctor_id' => 'required|exists:users,id',
            'date' => 'required|date|after_or_equal:today'
        ]);

        $doctorId = $request->doctor_id;
        $date = $request->date;

        $doctor = \App\Models\User::find($doctorId);
        if (!$doctor) {
            return response()->json([
                'success' => false,
                'message' => 'Doctor no encontrado'
            ], 404);
        }

        // Check if doctor works on this day
        $dayOfWeek = strtolower(\Carbon\Carbon::parse($date)->format('l'));
        
        // Safe JSON decode
        $workDays = [];
        try {
            $workDays = json_decode($doctor->work_days ?? '[]', true) ?? [];
        } catch (\Exception $e) {
            $workDays = [];
        }

        // If no work days are set, default to a standard work week
        if (empty($workDays) || !is_array($workDays)) {
            $workDays = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday'];
        }
        
        if (!in_array($dayOfWeek, $workDays)) {
            return response()->json([
                'success' => true,
                'data' => []
            ]);
        }

        // Generate time slots with safe parsing
        try {
            $startTime = \Carbon\Carbon::parse($doctor->schedule_start ?? '08:00');
            $endTime = \Carbon\Carbon::parse($doctor->schedule_end ?? '17:00');
        } catch (\Exception $e) {
            $startTime = \Carbon\Carbon::parse('08:00');
            $endTime = \Carbon\Carbon::parse('17:00');
        }
        
        $consultationDuration = intval($doctor->consultation_duration ?? 30);
        
        // Safe break time parsing
        $breakStart = null;
        $breakEnd = null;
        
        if ($doctor->break_start) {
            try {
                $breakStart = \Carbon\Carbon::parse($doctor->break_start);
            } catch (\Exception $e) {
                $breakStart = null;
            }
        }
        
        if ($doctor->break_end) {
            try {
                $breakEnd = \Carbon\Carbon::parse($doctor->break_end);
            } catch (\Exception $e) {
                $breakEnd = null;
            }
        }
        
        $slots = [];

        while ($startTime < $endTime) {
            $slotTime = $startTime->format('H:i');
            $slotEndTime = $startTime->copy()->addMinutes($consultationDuration);
            
            // Skip slots that overlap with break time
            if ($breakStart && $breakEnd) {
                // Check if slot overlaps with break time
                if ($startTime < $breakEnd && $slotEndTime > $breakStart) {
                    $startTime->addMinutes($consultationDuration);
                    continue;
                }
            }
            
            // Check availability for this slot
            try {
                $availabilityRequest = new Request([
                    'doctor_id' => $doctorId,
                    'date' => $date,
                    'time' => $slotTime,
                    'duration' => $consultationDuration
                ]);
                
                $availabilityResponse = $this->checkAvailability($availabilityRequest);
                $availabilityData = json_decode($availabilityResponse->getContent(), true);
                
                if ($availabilityData && isset($availabilityData['available']) && $availabilityData['available']) {
                    $slots[] = [
                        'time' => $slotTime,
                        'display_time' => $startTime->format('g:i A'),
                        'available' => true
                    ];
                }
            } catch (\Exception $e) {
                // Skip this slot if availability check fails
                continue;
            }

            $startTime->addMinutes($consultationDuration);
        }

        return response()->json([
            'success' => true,
            'data' => $slots
        ]);
        
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error al cargar horarios disponibles',
            'data' => []
        ], 500);
    }
} 