<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class DoctorSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'doctor_id',
        'clinic_id',
        'city',
        'address',
        'location_name',
        'schedule_type',
        'specific_date',
        'start_date',
        'end_date',
        'monday',
        'tuesday',
        'wednesday',
        'thursday',
        'friday',
        'saturday',
        'sunday',
        'time_slots',
        'appointment_duration',
        'status',
        'is_available_for_booking',
        'notes',
    ];

    protected $casts = [
        'specific_date' => 'date',
        'start_date' => 'date',
        'end_date' => 'date',
        'time_slots' => 'array',
        'monday' => 'boolean',
        'tuesday' => 'boolean',
        'wednesday' => 'boolean',
        'thursday' => 'boolean',
        'friday' => 'boolean',
        'saturday' => 'boolean',
        'sunday' => 'boolean',
        'is_available_for_booking' => 'boolean',
        'appointment_duration' => 'integer',
    ];

    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }

    public function clinic()
    {
        return $this->belongsTo(Clinic::class);
    }

    public function isAvailableOnDate(Carbon $date)
    {
        if ($this->status !== 'active' || !$this->is_available_for_booking) {
            return false;
        }

        if ($date->lt($this->start_date)) {
            return false;
        }

        if ($this->end_date && $date->gt($this->end_date)) {
            return false;
        }

        if ($this->schedule_type === 'specific_date') {
            return $date->isSameDay($this->specific_date);
        }

        $dayOfWeek = strtolower($date->format('l'));
        return $this->{$dayOfWeek} ?? false;
    }

    public function getAvailableSlotsForDate(Carbon $date)
    {
        if (!$this->isAvailableOnDate($date)) {
            return [];
        }

        $slots = [];

        foreach ($this->time_slots as $timeSlot) {
            $start = Carbon::parse($date->format('Y-m-d') . ' ' . $timeSlot['start']);
            $end = Carbon::parse($date->format('Y-m-d') . ' ' . $timeSlot['end']);

            while ($start->lt($end)) {
                $slotEnd = $start->copy()->addMinutes($this->appointment_duration);

                if ($slotEnd->lte($end)) {
                    $slots[] = [
                        'start' => $start->format('H:i'),
                        'end' => $slotEnd->format('H:i'),
                        'start_datetime' => $start->toDateTimeString(),
                        'end_datetime' => $slotEnd->toDateTimeString(),
                    ];
                }

                $start = $slotEnd;
            }
        }

        return $slots;
    }

    public function getAvailableSlotsExcludingAppointments(Carbon $date)
    {
        $allSlots = $this->getAvailableSlotsForDate($date);

        if (empty($allSlots)) {
            return [];
        }

        $appointments = Appointment::where('doctor_id', $this->doctor_id)
            ->whereDate('date_time', $date)
            ->whereIn('status', ['scheduled', 'pending'])
            ->get();

        $availableSlots = array_filter($allSlots, function($slot) use ($appointments) {
            $slotStart = Carbon::parse($slot['start_datetime']);
            $slotEnd = Carbon::parse($slot['end_datetime']);

            foreach ($appointments as $appointment) {
                $appointmentStart = Carbon::parse($appointment->date_time);
                $appointmentEnd = $appointmentStart->copy()->addMinutes($appointment->duration);

                if ($slotStart->lt($appointmentEnd) && $slotEnd->gt($appointmentStart)) {
                    return false;
                }
            }

            return true;
        });

        return array_values($availableSlots);
    }

    public function getTotalSlotsForDate(Carbon $date)
    {
        return count($this->getAvailableSlotsForDate($date));
    }

    public function scopeInCity($query, $city)
    {
        return $query->where('city', $city);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeAvailableForBooking($query)
    {
        return $query->where('is_available_for_booking', true);
    }

    public function scopeAvailableBetween($query, Carbon $startDate, Carbon $endDate)
    {
        return $query->where('status', 'active')
            ->where('start_date', '<=', $endDate)
            ->where(function($q) use ($startDate) {
                $q->whereNull('end_date')
                    ->orWhere('end_date', '>=', $startDate);
            });
    }

    public function scopeForDoctor($query, $doctorId)
    {
        return $query->where('doctor_id', $doctorId);
    }

    public function getActiveDays()
    {
        $days = [];
        $weekDays = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];

        foreach ($weekDays as $day) {
            if ($this->{$day}) {
                $days[] = $day;
            }
        }

        return $days;
    }

    public function getTotalHoursPerDay()
    {
        $totalMinutes = 0;

        foreach ($this->time_slots as $slot) {
            $start = Carbon::parse($slot['start']);
            $end = Carbon::parse($slot['end']);
            $totalMinutes += $start->diffInMinutes($end);
        }

        return round($totalMinutes / 60, 2);
    }

    public function getTotalSlotsPerDay()
    {
        $totalMinutes = 0;

        foreach ($this->time_slots as $slot) {
            $start = Carbon::parse($slot['start']);
            $end = Carbon::parse($slot['end']);
            $totalMinutes += $start->diffInMinutes($end);
        }

        return floor($totalMinutes / $this->appointment_duration);
    }
}
