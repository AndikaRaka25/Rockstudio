<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Studio;
use Illuminate\Http\Request;

class BookingManageController extends Controller
{
    public function index(Request $request)
    {
        $query = Booking::with(['studio', 'user'])->orderByDesc('date')->orderByDesc('session_number');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('studio_id')) {
            $query->where('studio_id', $request->studio_id);
        }

        $bookings = $query->paginate(15);
        $studios = Studio::all();

        return view('owner.bookings.index', compact('bookings', 'studios'));
    }

    public function schedule(Studio $studio = null)
    {
        $studios = Studio::where('is_active', true)->get();
        $selectedStudio = $studio ?? $studios->first();

        return view('owner.bookings.schedule', [
            'studios' => $studios,
            'selectedStudio' => $selectedStudio,
        ]);
    }
}
