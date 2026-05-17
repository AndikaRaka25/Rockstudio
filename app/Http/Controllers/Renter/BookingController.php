<?php

namespace App\Http\Controllers\Renter;

use App\Http\Controllers\Controller;
use App\Models\Studio;
use App\Models\Booking;

class BookingController extends Controller
{
    public function index(Studio $studio = null)
    {
        $studios = Studio::where('is_active', true)->get();
        $selectedStudio = $studio ?? $studios->first();

        return view('renter.booking', [
            'studios' => $studios,
            'selectedStudio' => $selectedStudio,
        ]);
    }

    public function myBookings()
    {
        $bookings = Booking::where('user_id', auth()->id())
            ->with('studio')
            ->orderByDesc('date')
            ->orderByDesc('session_number')
            ->paginate(10);

        return view('renter.my-bookings', compact('bookings'));
    }

    public function show(Booking $booking)
    {
        // Pastikan hanya pemilik booking yang bisa melihat
        abort_if($booking->user_id !== auth()->id(), 403);
        $booking->load('studio');

        return view('renter.booking-detail', compact('booking'));
    }

    public function downloadPdf(Booking $booking)
    {
        abort_if($booking->user_id !== auth()->id(), 403);
        $booking->load('studio');

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.receipt', compact('booking'));
        return $pdf->download('Resi-Booking-' . $booking->id . '.pdf');
    }

    public function sendEmail(\Illuminate\Http\Request $request, Booking $booking)
    {
        abort_if($booking->user_id !== auth()->id(), 403);
        
        $request->validate([
            'target_email' => 'required|email'
        ]);

        $booking->load('studio');

        try {
            $pdfContent = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.receipt', compact('booking'))->output();
            
            \Illuminate\Support\Facades\Mail::to($request->target_email)
                ->send(new \App\Mail\BookingReceiptMail($booking, $pdfContent));

            return back()->with('success', 'Resi berhasil dikirim ke ' . $request->target_email);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Mail Error: ' . $e->getMessage());
            return back()->with('error', 'Gagal mengirim email: ' . $e->getMessage());
        }
    }
}
