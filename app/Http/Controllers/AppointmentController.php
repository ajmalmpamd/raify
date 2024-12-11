<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Appointment;
use Carbon\Carbon;

class AppointmentController extends Controller
{
    public function available_slots($date)
    {
        $start_time = Carbon::createFromTime(10, 0);
        $end_time = Carbon::createFromTime(17, 0);
        $break_time = Carbon::createFromTime(13, 0);
        $slots = [];

        while ($start_time < $end_time) {
            if ($start_time->eq($break_time)) {
                $start_time->addHour();
                continue;
            }
            $slots[] = $start_time->format('H:i');
            $start_time->addMinutes(30);
        }

        // Fetch booked slots with details
        $bookedSlots = Appointment::where('date', $date)
            ->get(['time', 'name', 'phone'])
            ->map(function ($appointment) {
                return [
                    'time' => Carbon::parse($appointment->time)->format('H:i'),
                    'name' => $appointment->name,
                    'phone' => $appointment->phone,
                ];
            })->toArray();

        // Extract booked times in 'H:i' format
        $bookedTimes = array_column($bookedSlots, 'time');

        // Calculate available slots
        $availableSlots = array_diff($slots, $bookedTimes);

        return response()->json([
            'available' => $availableSlots,
            'booked' => $bookedSlots, // Full booking details for booked slots
        ]);
    }

    public function book_appointment(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:15',
            'date' => 'required|date',
            'time' => 'required|string|date_format:H:i',
        ]);

        $appointment = Appointment::create($validated);

        return response()->json(['message' => 'Appointment booked successfully', 'appointment' => $appointment]);
    }
}
