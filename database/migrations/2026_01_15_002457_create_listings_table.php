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
        Schema::create('listings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            
            // Relational Data
            $table->foreignId('make_id')->constrained();
            $table->foreignId('vehicle_model_id')->constrained();
            $table->foreignId('vehicle_type_id')->constrained();
            
            // Vehicle Details
            $table->decimal('price', 12, 2);
            $table->integer('year');
            $table->integer('mileage')->nullable();
            $table->string('fuel_type')->nullable(); // e.g., Petrol, Diesel, Electric
            $table->string('transmission')->nullable(); // e.g., Automatic, Manual
            $table->string('condition')->nullable(); // e.g., New, Used
            $table->string('color')->nullable();
            
            // Status and Meta
            $table->string('status')->default('active'); // active, sold, suspended
            $table->boolean('is_featured')->default(false);
            $table->string('main_image')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('listings');
    }
};
