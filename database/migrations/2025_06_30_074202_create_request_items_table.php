<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('request_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('request_id')->constrained('requests')->cascadeOnUpdate()->cascadeOnDelete();
            $table->string('item_name', 100);
            $table->string('item_type', 50)->nullable();
            $table->decimal('quantity', 10, 2)->default(1); // Bisa 10.50, dst
            $table->string('unit', 20)->nullable(); // satuan (kg, liter, paket, dst)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('request_items');
    }
};
