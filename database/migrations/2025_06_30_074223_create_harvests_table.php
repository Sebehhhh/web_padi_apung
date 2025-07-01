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
        Schema::create('harvests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('crop_type_id')->constrained('crop_types')->cascadeOnUpdate()->restrictOnDelete();
            $table->date('harvest_date');
            $table->decimal('land_area_m2', 10, 2);
            $table->decimal('total_weight_kg', 12, 2);
            $table->decimal('total_weight_ton', 12, 4)->virtualAs('total_weight_kg/1000');
            $table->decimal('productivity_kg_m2', 12, 4)->virtualAs('total_weight_kg/land_area_m2');
            $table->enum('quality', ['A', 'B', 'C'])->nullable();
            $table->string('notes', 255)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('harvests');
    }
};
