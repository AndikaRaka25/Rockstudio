<?php

namespace App\Livewire;

use App\Models\Booking;
use App\Models\Studio;
use App\Services\BookingService;
use App\Services\MidtransService;
use App\Services\ScheduleService;
use Carbon\Carbon;
use Livewire\Component;

class ScheduleGrid extends Component
{
    public int $studioId;
    public string $weekStartDate;
    public array $schedule = [];

    // Booking form state
    public bool $showBookingModal = false;
    public ?int $selectedSession = null;
    public ?string $selectedDate = null;
    public string $bandName = '';
    public string $bookerName = '';
    public string $bookerPhone = '';
    public string $notes = '';

    // Payment state
    public bool $showPaymentStep = false;
    public ?int $activeBookingId = null;
    public ?string $snapToken = null;
    public int $remainingSeconds = 0;

    // Messages
    public string $successMessage = '';
    public string $errorMessage = '';

    public function mount(int $studioId, ?string $weekStart = null): void
    {
        $this->studioId = $studioId;
        $this->weekStartDate = $weekStart ?? Carbon::now()->startOfWeek(Carbon::MONDAY)->toDateString();

        // Pre-fill user data
        if (auth()->check()) {
            $this->bookerName = auth()->user()->name;
            $this->bookerPhone = auth()->user()->phone ?? '';
        }

        $this->loadSchedule();
    }

    public function loadSchedule(): void
    {
        $service = app(ScheduleService::class);
        $this->schedule = $service->getWeeklySchedule(
            $this->studioId,
            Carbon::parse($this->weekStartDate)
        );
    }

    // === Week Navigation ===

    public function previousWeek(): void
    {
        $this->weekStartDate = Carbon::parse($this->weekStartDate)->subWeek()->toDateString();
        $this->loadSchedule();
    }

    public function nextWeek(): void
    {
        $this->weekStartDate = Carbon::parse($this->weekStartDate)->addWeek()->toDateString();
        $this->loadSchedule();
    }

    public function goToDate(string $date): void
    {
        $this->weekStartDate = Carbon::parse($date)->startOfWeek(Carbon::MONDAY)->toDateString();
        $this->loadSchedule();
    }

    public function thisWeek(): void
    {
        $this->weekStartDate = Carbon::now()->startOfWeek(Carbon::MONDAY)->toDateString();
        $this->loadSchedule();
    }

    // === Booking Flow ===

    public function selectSession(string $date, int $sessionNumber): void
    {
        $this->resetMessages();
        $this->selectedDate = $date;
        $this->selectedSession = $sessionNumber;
        $this->showBookingModal = true;
        $this->showPaymentStep = false;
        $this->snapToken = null;
        $this->activeBookingId = null;
    }

    public function closeModal(): void
    {
        $this->showBookingModal = false;
        $this->showPaymentStep = false;
        $this->selectedDate = null;
        $this->selectedSession = null;
        $this->bandName = '';
        $this->notes = '';
        $this->snapToken = null;
        $this->activeBookingId = null;
        $this->resetMessages();
    }

    /**
     * Step 1: Lock the session (FCFS + Pessimistic Locking)
     */
    public function lockAndBook(): void
    {
        $this->resetMessages();

        $this->validate([
            'bookerName' => 'required|string|max:255',
            'bookerPhone' => 'required|string|max:20',
        ]);

        try {
            $bookingService = app(BookingService::class);
            $booking = $bookingService->lockSession(
                $this->studioId,
                $this->selectedDate,
                $this->selectedSession,
                auth()->id(),
                [
                    'band_name' => $this->bandName ?: null,
                    'booker_name' => $this->bookerName,
                    'booker_phone' => $this->bookerPhone,
                    'notes' => $this->notes ?: null,
                ]
            );

            $this->activeBookingId = $booking->id;
            $this->remainingSeconds = 300; // 5 minutes

            // Generate Dummy Token (Bypassing Midtrans for local testing to avoid SSL error)
            // $midtransService = app(MidtransService::class);
            // $this->snapToken = $midtransService->createSnapToken($booking);
            $this->snapToken = 'DUMMY_TOKEN_FOR_TESTING';
            $booking->update(['midtrans_order_id' => app(BookingService::class)->generateOrderId()]);

            $this->showPaymentStep = true;
            $this->successMessage = __('booking.lock_acquired');

            // Refresh schedule to show pending (kuning)
            $this->loadSchedule();

        } catch (\Exception $e) {
            $this->errorMessage = $e->getMessage();
        }
    }

    /**
     * Simulate successful payment (Dummy Flow for testing)
     */
    public function simulatePayment(string $method): void
    {
        if ($this->activeBookingId) {
            $booking = Booking::find($this->activeBookingId);
            if ($booking && $booking->isPending()) {
                app(BookingService::class)->confirmBooking($booking, $method, $booking->midtrans_order_id);
                $this->successMessage = __('booking.booking_confirmed');
            }
        }
        $this->closeModal();
        $this->loadSchedule();
    }

    /**
     * Called from JS after successful Midtrans payment.
     */
    public function paymentSuccess(string $orderId): void
    {
        // Payment confirmation is handled by webhook.
        // This is just for UI feedback.
        $this->successMessage = __('booking.booking_confirmed');
        $this->closeModal();
        $this->loadSchedule();
    }

    /**
     * Called when payment is pending (e.g., waiting for bank transfer).
     */
    public function paymentPending(): void
    {
        $this->successMessage = __('booking.pending');
        $this->closeModal();
        $this->loadSchedule();
    }

    /**
     * Called when lock timer expires.
     */
    public function lockExpired(): void
    {
        if ($this->activeBookingId) {
            $booking = Booking::find($this->activeBookingId);
            if ($booking && $booking->isPending()) {
                $booking->update(['status' => 'expired']);
            }
        }

        $this->errorMessage = __('booking.lock_expired');
        $this->closeModal();
        $this->loadSchedule();
    }

    public function switchStudio(int $studioId): void
    {
        $this->studioId = $studioId;
        $this->loadSchedule();
    }

    private function resetMessages(): void
    {
        $this->successMessage = '';
        $this->errorMessage = '';
    }

    public function render()
    {
        return view('livewire.schedule-grid', [
            'studio' => Studio::find($this->studioId),
            'studios' => Studio::where('is_active', true)->get(),
        ]);
    }
}
