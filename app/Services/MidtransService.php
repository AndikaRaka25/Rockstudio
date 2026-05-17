<?php

namespace App\Services;

use App\Models\Booking;
use Midtrans\Config as MidtransConfig;
use Midtrans\Snap;
use Midtrans\Notification;

class MidtransService
{
    public function __construct()
    {
        MidtransConfig::$serverKey = config('midtrans.server_key');
        MidtransConfig::$isProduction = config('midtrans.is_production');
        MidtransConfig::$isSanitized = true;
        MidtransConfig::$is3ds = true;
    }

    /**
     * Create a Snap token for payment.
     */
    public function createSnapToken(Booking $booking): string
    {
        $orderId = app(BookingService::class)->generateOrderId();

        $params = [
            'transaction_details' => [
                'order_id' => $orderId,
                'gross_amount' => $booking->dp_amount,
            ],
            'customer_details' => [
                'first_name' => $booking->booker_name,
                'phone' => $booking->booker_phone,
                'email' => $booking->user->email ?? '',
            ],
            'item_details' => [
                [
                    'id' => "SESI-{$booking->session_number}",
                    'price' => $booking->dp_amount,
                    'quantity' => 1,
                    'name' => "DP Studio {$booking->studio->name} - Sesi {$booking->session_number} ({$booking->start_time}-{$booking->end_time})",
                ],
            ],
        ];

        $snapToken = Snap::getSnapToken($params);

        // Save to booking
        $booking->update([
            'midtrans_order_id' => $orderId,
            'snap_token' => $snapToken,
        ]);

        return $snapToken;
    }

    /**
     * Handle incoming Midtrans webhook notification.
     */
    public function handleNotification(): array
    {
        $notification = new Notification();

        $orderId = $notification->order_id;
        $transactionStatus = $notification->transaction_status;
        $fraudStatus = $notification->fraud_status ?? null;
        $paymentType = $notification->payment_type ?? 'unknown';

        $booking = Booking::where('midtrans_order_id', $orderId)->first();

        if (!$booking) {
            return ['status' => 'error', 'message' => 'Booking not found'];
        }

        if ($transactionStatus === 'capture' || $transactionStatus === 'settlement') {
            if ($fraudStatus === null || $fraudStatus === 'accept') {
                app(BookingService::class)->confirmBooking($booking, $paymentType, $orderId);
                return ['status' => 'success', 'message' => 'Payment confirmed'];
            }
        } elseif (in_array($transactionStatus, ['cancel', 'deny', 'expire'])) {
            $booking->update(['status' => 'expired']);
            return ['status' => 'failed', 'message' => 'Payment ' . $transactionStatus];
        } elseif ($transactionStatus === 'pending') {
            return ['status' => 'pending', 'message' => 'Payment pending'];
        }

        return ['status' => 'unknown', 'message' => 'Unhandled status: ' . $transactionStatus];
    }
}
