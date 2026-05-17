<?php

namespace App\Console\Commands;

use App\Services\BookingService;
use Illuminate\Console\Command;

class ExpireLockedBookings extends Command
{
    protected $signature = 'bookings:expire-locks';
    protected $description = 'Expire pending bookings that have exceeded the 5-minute lock timeout';

    public function handle(BookingService $bookingService): int
    {
        $expired = $bookingService->expireLockedBookings();

        if ($expired > 0) {
            $this->info("Expired {$expired} locked booking(s).");
        }

        return Command::SUCCESS;
    }
}
