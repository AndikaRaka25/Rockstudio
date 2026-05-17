<?php

namespace App\Http\Controllers;

use App\Services\MidtransService;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    /**
     * Handle Midtrans webhook notification.
     * This endpoint is excluded from CSRF verification.
     */
    public function webhook(Request $request)
    {
        $midtransService = app(MidtransService::class);
        $result = $midtransService->handleNotification();

        return response()->json($result);
    }
}
