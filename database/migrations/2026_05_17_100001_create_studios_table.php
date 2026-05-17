<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('studios', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('address');
            $table->decimal('latitude', 10, 7)->default(-7.7500754);
            $table->decimal('longitude', 10, 7)->default(110.4067722);
            $table->json('images')->nullable();
            $table->json('facilities')->nullable();
            $table->integer('price_per_session')->default(85000);
            $table->integer('dp_amount')->default(40000);
            $table->integer('session_duration_minutes')->default(120);
            $table->string('operating_start', 5)->default('08:00');
            $table->string('operating_end', 5)->default('24:00');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('studios');
    }
};
