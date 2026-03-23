<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DoctorSchedule;
use App\Models\Doctor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class DoctorScheduleController extends Controller
{
    /**
     * Display a listing of schedules
     * Filtra por doctor_id, city, date_range
     */
    public function index(Request $request)
    {
        $query = DoctorSchedule::with(['doctor', 'clinic']);

        // Filtrar por doctor
        if ($request->has('doctor_id')) {
            $query->where('doctor_id', $request->doctor_id);
        }

        // Filtrar por ciudad
        if ($request->has('city')) {
            $query->where('city', $request->city);
        }

        // Filtrar por estado
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filtrar por rango de fechas
        if ($request->has('start_date') && $request->has('end_date')) {
            $startDate = Carbon::parse($request->start_date);
            $endDate = Carbon::parse($request->end_date);
            $query->availableBetween($startDate, $endDate);
        }

        // Solo activos por defecto
        if (!$request->has('include_inactive')) {
            $query->active();
        }

        $schedules = $query->orderBy('start_date')->paginate(20);

        return response()->json($schedules);
    }

    /**
     * Store a newly created schedule
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'doctor_id' => 'required|exists:doctors,id',
            'clinic_id' => 'required|exists:clinics,id',
            'city' => 'required|string|max:100',
            'address' => 'nullable|string',
            'location_name' => 'nullable|string|max:200',
            'schedule_type' => 'required|in:daily,weekly,monthly,specific_date',
            'specific_date' => 'required_if:schedule_type,specific_date|nullable|date',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'monday' => 'boolean',
            'tuesday' => 'boolean',
            'wednesday' => 'boolean',
            'thursday' => 'boolean',
            'friday' => 'boolean',
            'saturday' => 'boolean',
            'sunday' => 'boolean',
            'time_slots' => 'required|array',
            'time_slots.*.start' => 'required|date_format:H:i',
            'time_slots.*.end' => 'required|date_format:H:i|after:time_slots.*.start',
            'appointment_duration' => 'required|integer|min:15|max:240',
            'status' => 'nullable|in:active,inactive,temporary',
            'is_available_for_booking' => 'nullable|boolean',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $schedule = DoctorSchedule::create($request->all());

        return response()->json([
            'message' => 'Horario creado exitosamente',
            'schedule' => $schedule->load(['doctor', 'clinic'])
        ], 201);
    }

    /**
     * Display the specified schedule
     */
    public function show($id)
    {
        $schedule = DoctorSchedule::with(['doctor', 'clinic'])->findOrFail($id);

        return response()->json($schedule);
    }

    /**
     * Update the specified schedule
     */
    public function update(Request $request, $id)
    {
        $schedule = DoctorSchedule::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'doctor_id' => 'sometimes|exists:doctors,id',
            'clinic_id' => 'sometimes|exists:clinics,id',
            'city' => 'sometimes|string|max:100',
            'address' => 'nullable|string',
            'location_name' => 'nullable|string|max:200',
            'schedule_type' => 'sometimes|in:daily,weekly,monthly,specific_date',
            'specific_date' => 'nullable|date',
            'start_date' => 'sometimes|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'monday' => 'boolean',
            'tuesday' => 'boolean',
            'wednesday' => 'boolean',
            'thursday' => 'boolean',
            'friday' => 'boolean',
            'saturday' => 'boolean',
            'sunday' => 'boolean',
            'time_slots' => 'sometimes|array',
            'time_slots.*.start' => 'required_with:time_slots|date_format:H:i',
            'time_slots.*.end' => 'required_with:time_slots|date_format:H:i',
            'appointment_duration' => 'sometimes|integer|min:15|max:240',
            'status' => 'sometimes|in:active,inactive,temporary',
            'is_available_for_booking' => 'nullable|boolean',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $schedule->update($request->all());

        return response()->json([
            'message' => 'Horario actualizado exitosamente',
            'schedule' => $schedule->fresh()->load(['doctor', 'clinic'])
        ]);
    }

    /**
     * Remove the specified schedule
     */
    public function destroy($id)
    {
        $schedule = DoctorSchedule::findOrFail($id);
        $schedule->delete();

        return response()->json([
            'message' => 'Horario eliminado exitosamente'
        ]);
    }

    /**
     * Get available slots for a specific date
     * GET /api/doctor-schedules/{id}/available-slots?date=2025-03-25
     */
    public function availableSlots($id, Request $request)
    {
        $validator = Validator::make($request->all(), [
            'date' => 'required|date|after_or_equal:today',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $schedule = DoctorSchedule::findOrFail($id);
        $date = Carbon::parse($request->date);

        $slots = $schedule->getAvailableSlotsExcludingAppointments($date);

        return response()->json([
            'date' => $date->format('Y-m-d'),
            'doctor' => $schedule->doctor->name,
            'clinic' => $schedule->clinic->name,
            'city' => $schedule->city,
            'total_slots' => count($slots),
            'slots' => $slots
        ]);
    }

    /**
     * Get schedules for a specific doctor
     * GET /api/doctors/{doctorId}/schedules
     */
    public function byDoctor($doctorId, Request $request)
    {
        $doctor = Doctor::findOrFail($doctorId);

        $query = DoctorSchedule::where('doctor_id', $doctorId)
            ->with(['clinic']);

        // Filtrar por ciudad si se proporciona
        if ($request->has('city')) {
            $query->where('city', $request->city);
        }

        // Solo activos por defecto
        if (!$request->has('include_inactive')) {
            $query->active();
        }

        $schedules = $query->orderBy('start_date')->get();

        return response()->json([
            'doctor' => $doctor,
            'schedules' => $schedules,
            'cities' => $doctor->getCitiesWithSchedules()
        ]);
    }

    /**
     * Get all doctors available in a specific city
     * GET /api/schedules/by-city/{city}
     */
    public function byCity($city, Request $request)
    {
        $query = DoctorSchedule::where('city', $city)
            ->active()
            ->availableForBooking()
            ->with(['doctor', 'clinic']);

        // Filtrar por rango de fechas
        if ($request->has('start_date') && $request->has('end_date')) {
            $startDate = Carbon::parse($request->start_date);
            $endDate = Carbon::parse($request->end_date);
            $query->availableBetween($startDate, $endDate);
        }

        $schedules = $query->get();

        // Agrupar por doctor
        $doctors = $schedules->groupBy('doctor_id')->map(function ($doctorSchedules) {
            $doctor = $doctorSchedules->first()->doctor;
            return [
                'id' => $doctor->id,
                'name' => $doctor->name,
                'specialty' => $doctor->specialty,
                'photo_url' => $doctor->photo_url,
                'rating' => $doctor->rating,
                'schedules' => $doctorSchedules->values()
            ];
        })->values();

        return response()->json([
            'city' => $city,
            'total_doctors' => $doctors->count(),
            'doctors' => $doctors
        ]);
    }

    /**
     * Get doctor availability calendar for a month
     * GET /api/doctor-schedules/{id}/calendar?month=2025-03
     */
    public function calendar($id, Request $request)
    {
        $validator = Validator::make($request->all(), [
            'month' => 'required|date_format:Y-m',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $schedule = DoctorSchedule::with(['doctor', 'clinic'])->findOrFail($id);

        $startOfMonth = Carbon::parse($request->month . '-01')->startOfMonth();
        $endOfMonth = $startOfMonth->copy()->endOfMonth();

        $availability = [];
        $currentDate = $startOfMonth->copy();

        while ($currentDate->lte($endOfMonth)) {
            if ($schedule->isAvailableOnDate($currentDate)) {
                $totalSlots = $schedule->getTotalSlotsForDate($currentDate);
                $availableSlots = count($schedule->getAvailableSlotsExcludingAppointments($currentDate));

                $availability[] = [
                    'date' => $currentDate->format('Y-m-d'),
                    'day_name' => $currentDate->format('l'),
                    'total_slots' => $totalSlots,
                    'available_slots' => $availableSlots,
                    'is_full' => $availableSlots === 0,
                ];
            }

            $currentDate->addDay();
        }

        return response()->json([
            'schedule' => $schedule,
            'month' => $request->month,
            'availability' => $availability
        ]);
    }
}
