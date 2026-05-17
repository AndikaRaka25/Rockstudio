<?php

namespace App\Http\Controllers\Renter;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Booking;

class DashboardController extends Controller
{
    public function index()
    {
        $events = Event::active()->latest()->take(5)->get();
        $myBookings = Booking::where('user_id', auth()->id())
            ->where('status', 'confirmed')
            ->where('date', '>=', now()->toDateString())
            ->orderBy('date')
            ->orderBy('session_number')
            ->take(5)
            ->get();

        return view('renter.dashboard', compact('events', 'myBookings'));
    }
}
