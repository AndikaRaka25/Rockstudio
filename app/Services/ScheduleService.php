<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Studio;
use Carbon\Carbon;

class ScheduleService
{
    /**
     * Build the weekly schedule grid data for a studio.
     * Returns array of days × sessions with booking info.
     */
    public function getWeeklySchedule(int $studioId, Carbon $weekStart): array
    {
        $studio = Studio::findOrFail($studioId);
        $weekEnd = $weekStart->copy()->addDays(6);

        // Fetch all active bookings for this week
        $bookings = Booking::forSchedule($studioId, $weekStart->toDateString(), $weekEnd->toDateString())
            ->get()
            ->groupBy(function ($booking) {
                return $booking->date->toDateString() . '-' . $booking->session_number;
            });

        $sessions = $studio->getSessionTemplates();
        $schedule = [];

        for ($day = 0; $day < 7; $day++) {
            $date = $weekStart->copy()->addDays($day);
            $dateStr = $date->toDateString();
            $dayData = [
                'date' => $dateStr,
                'day_name' => $date->translatedFormat('l'),
                'day_short' => $date->translatedFormat('D'),
                'formatted_date' => $date->translatedFormat('d M Y'),
                'is_today' => $date->isToday(),
                'is_past' => $date->isPast() && !$date->isToday(),
                'sessions' => [],
            ];

            foreach ($sessions as $session) {
                $key = $dateStr . '-' . $session['session_number'];
                $booking = $bookings->get($key)?->first();

                $cellData = [
                    'session_number' => $session['session_number'],
                    'start_time' => $session['start_time'],
                    'end_time' => $session['end_time'],
                    'status' => 'available', // default: Hijau
                    'booking' => null,
                    'display_name' => '',
                ];

                if ($booking) {
                    $cellData['status'] = $booking->status; // pending (kuning), confirmed (merah)
                    $cellData['booking'] = $booking;
                    $cellData['display_name'] = $booking->band_name ?: $booking->booker_name;

                    // Check if this is current user's booking → status 'own' (biru)
                    if (auth()->check() && $booking->user_id === auth()->id()) {
                        $cellData['status'] = $booking->status === 'confirmed' ? 'own' : $booking->status;
                    }
                }

                // Past sessions are unavailable
                if ($date->isPast() && !$date->isToday()) {
                    if ($cellData['status'] === 'available') {
                        $cellData['status'] = 'past';
                    }
                }

                // Today: past sessions (time already passed)
                if ($date->isToday()) {
                    $sessionEndHour = (int)substr($session['end_time'], 0, 2);
                    if ($sessionEndHour <= (int)now()->format('H') && $cellData['status'] === 'available') {
                        $cellData['status'] = 'past';
                    }
                }

                $dayData['sessions'][] = $cellData;
            }

            $schedule[] = $dayData;
        }

        return [
            'studio' => $studio,
            'week_start' => $weekStart->toDateString(),
            'week_end' => $weekEnd->toDateString(),
            'week_label' => $weekStart->translatedFormat('d M') . ' - ' . $weekEnd->translatedFormat('d M Y'),
            'days' => $schedule,
        ];
    }
}
