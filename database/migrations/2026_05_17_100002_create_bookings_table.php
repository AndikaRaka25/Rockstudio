<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('studio_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->date('date');
            $table->unsignedTinyInteger('session_number'); // 1-8
            $table->string('start_time', 5); // "08:00"
            $table->string('end_time', 5);   // "10:00"
            $table->enum('status', [
                'pending',      // Kuning - locked, menunggu pembayaran (5 menit)
                'confirmed',    // Merah - sudah bayar
                'cancelled',    // Abu-abu - dibatalkan
                'expired',      // Abu-abu - lock habis / pembayaran expired
                'rescheduled',  // Abu-abu - sudah dipindahkan
            ])->default('pending');
            $table->string('band_name')->nullable();
            $table->string('booker_name');
            $table->string('booker_phone', 20);
            $table->text('notes')->nullable();
            $table->string('payment_method')->nullable(); // qris, bank_transfer
            $table->string('midtrans_order_id')->nullable()->unique();
            $table->string('snap_token')->nullable();
            $table->integer('amount')->default(0);
            $table->integer('dp_amount')->default(0);
            $table->timestamp('locked_at')->nullable();
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->foreignId('original_booking_id')->nullable()->constrained('bookings')->onDelete('set null');
            $table->timestamps();

            // Composite index untuk query jadwal mingguan
            $table->index(['studio_id', 'date', 'session_number']);
            // Unique constraint: 1 sesi aktif per studio per tanggal
            // (enforced in application logic via pessimistic lock, not DB unique because of status changes)
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
