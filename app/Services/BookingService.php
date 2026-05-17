<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Studio;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class BookingService
{
    /**
     * FCFS + Pessimistic Locking: Lock a session for a user.
     * Uses SELECT ... FOR UPDATE to prevent race conditions.
     *
     * @throws \Exception if session is already locked/booked
     */
    public function lockSession(int $studioId, string $date, int $sessionNumber, int $userId, array $formData): Booking
    {
        return DB::transaction(function () use ($studioId, $date, $sessionNumber, $userId, $formData) {
            // Pessimistic Lock: SELECT ... FOR UPDATE
            // This blocks any other concurrent transaction trying to lock the same session
            $existing = Booking::where('studio_id', $studioId)
                ->where('date', $date)
                ->where('session_number', $sessionNumber)
                ->whereIn('status', ['pending', 'confirmed'])
                ->lockForUpdate()
                ->first();

            if ($existing) {
                if ($existing->isPending() && $existing->isLockExpired()) {
                    // Lock expired, release it
                    $existing->update(['status' => 'expired']);
                } else {
                    throw new \Exception(__('booking.session_taken'));
                }
            }

            $studio = Studio::findOrFail($studioId);
            $sessions = $studio->getSessionTemplates();
            $session = $sessions[$sessionNumber - 1];

            return Booking::create([
                'studio_id' => $studioId,
                'user_id' => $userId,
                'date' => $date,
                'session_number' => $sessionNumber,
                'start_time' => $session['start_time'],
                'end_time' => $session['end_time'],
                'status' => 'pending',
                'band_name' => $formData['band_name'] ?? null,
                'booker_name' => $formData['booker_name'],
                'booker_phone' => $formData['booker_phone'],
                'notes' => $formData['notes'] ?? null,
                'amount' => $studio->price_per_session,
                'dp_amount' => $studio->dp_amount,
                'locked_at' => now(),
            ]);
        });
    }

    /**
     * Confirm a booking after successful payment.
     */
    public function confirmBooking(Booking $booking, string $paymentMethod, string $midtransOrderId): void
    {
        $booking->update([
            'status' => 'confirmed',
            'payment_method' => $paymentMethod,
            'midtrans_order_id' => $midtransOrderId,
            'confirmed_at' => now(),
        ]);

        // Create income transaction
        Transaction::create([
            'studio_id' => $booking->studio_id,
            'booking_id' => $booking->id,
            'type' => 'income',
            'category' => 'booking',
            'amount' => $booking->dp_amount,
            'description' => "Pembayaran DP booking {$booking->booker_name} - Sesi {$booking->session_number} ({$booking->date->format('d/m/Y')})",
            'date' => now(),
        ]);
    }

    /**
     * Cancel a confirmed booking (before H-1).
     * DP is NOT refunded (no-refund policy).
     */
    public function cancelBooking(Booking $booking): void
    {
        if (!$booking->canCancel()) {
            throw new \Exception(__('booking.cannot_cancel'));
        }

        $booking->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
        ]);
    }

    /**
     * Reschedule a booking to a new session (before H-1).
     * Creates a new booking linked to the original via original_booking_id.
     * No additional charge.
     */
    public function rescheduleBooking(Booking $booking, string $newDate, int $newSessionNumber): Booking
    {
        if (!$booking->canReschedule()) {
            throw new \Exception(__('booking.cannot_reschedule'));
        }

        return DB::transaction(function () use ($booking, $newDate, $newSessionNumber) {
            // Check new slot availability with pessimistic lock
            $existing = Booking::where('studio_id', $booking->studio_id)
                ->where('date', $newDate)
                ->where('session_number', $newSessionNumber)
                ->whereIn('status', ['pending', 'confirmed'])
                ->lockForUpdate()
                ->first();

            if ($existing) {
                throw new \Exception(__('booking.session_taken'));
            }

            // Mark old booking as rescheduled
            $booking->update(['status' => 'rescheduled']);

            // Create new booking
            $studio = Studio::findOrFail($booking->studio_id);
            $sessions = $studio->getSessionTemplates();
            $session = $sessions[$newSessionNumber - 1];

            return Booking::create([
                'studio_id' => $booking->studio_id,
                'user_id' => $booking->user_id,
                'date' => $newDate,
                'session_number' => $newSessionNumber,
                'start_time' => $session['start_time'],
                'end_time' => $session['end_time'],
                'status' => 'confirmed',
                'band_name' => $booking->band_name,
                'booker_name' => $booking->booker_name,
                'booker_phone' => $booking->booker_phone,
                'notes' => $booking->notes,
                'payment_method' => $booking->payment_method,
                'amount' => $booking->amount,
                'dp_amount' => $booking->dp_amount,
                'confirmed_at' => now(),
                'original_booking_id' => $booking->id,
            ]);
        });
    }

    /**
     * Expire all pending bookings that have exceeded 5-minute lock.
     * Called by scheduled command every minute.
     */
    public function expireLockedBookings(): int
    {
        return Booking::where('status', 'pending')
            ->where('locked_at', '<', now()->subMinutes(5))
            ->update(['status' => 'expired']);
    }

    /**
     * Auto-forfeit bookings that are past H-1 deadline and still confirmed.
     * This handles the "after H-1: jadwal + uang muka hangus" rule.
     * Note: This is informational - the booking remains confirmed but cannot be cancelled.
     */
    public function checkH1Deadlines(): void
    {
        // Bookings where the date has passed - mark as completed (not cancelled)
        // The no-cancel-after-H-1 rule is enforced in canCancel() method
    }

    /**
     * Generate a unique Midtrans order ID.
     */
    public function generateOrderId(): string
    {
        return 'RS-' . now()->format('Ymd') . '-' . strtoupper(Str::random(8));
    }
}
