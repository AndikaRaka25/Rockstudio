<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventory_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('studio_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->enum('category', ['alat_musik', 'alat_rekaman', 'alat_elektronik'])->default('alat_musik');
            $table->unsignedInteger('quantity')->default(1);
            $table->enum('condition', ['baik', 'cukup', 'perlu_perbaikan'])->default('baik');
            $table->date('purchase_date')->nullable();
            $table->integer('purchase_price')->nullable();
            $table->text('notes')->nullable();
            $table->string('image_url')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_items');
    }
};
