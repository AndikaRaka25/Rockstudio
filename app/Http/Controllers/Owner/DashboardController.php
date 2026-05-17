<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Transaction;
use App\Models\Studio;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();
        $studios = Studio::all();

        $todayBookings = Booking::where('status', 'confirmed')
            ->where('date', $today)
            ->count();

        $monthIncome = Transaction::income()
            ->whereMonth('date', $today->month)
            ->whereYear('date', $today->year)
            ->sum('amount');

        $totalSessions = 8 * 7 * $studios->count(); // 8 sessions × 7 days × studios
        $weekStart = $today->copy()->startOfWeek(Carbon::MONDAY);
        $weekEnd = $weekStart->copy()->addDays(6);

        $bookedThisWeek = Booking::whereIn('status', ['confirmed', 'pending'])
            ->whereBetween('date', [$weekStart, $weekEnd])
            ->count();

        $recentBookings = Booking::with(['studio', 'user'])
            ->whereIn('status', ['confirmed', 'pending'])
            ->orderByDesc('created_at')
            ->take(10)
            ->get();

        return view('owner.dashboard', compact(
            'todayBookings',
            'monthIncome',
            'totalSessions',
            'bookedThisWeek',
            'recentBookings',
            'studios',
        ));
    }
}
