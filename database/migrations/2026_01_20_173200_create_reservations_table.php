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
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('listing_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Buyer
            $table->foreignId('seller_id')->constrained('users')->onDelete('cascade');
            
            // Deposit Details
            $table->decimal('listing_price', 12, 2);
            $table->decimal('deposit_percentage', 5, 2); // 1-10%
            $table->decimal('deposit_amount', 12, 2); // Minimum 100,000 won
            
            // Reservation Duration
            $table->timestamp('reserved_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->integer('extension_count')->default(0); // Max 3 extensions
            $table->integer('duration_hours')->default(24); // 24 hours or 72 hours (3 days)
            
            // Status
            $table->string('status')->default('active'); // active, completed, expired, cancelled
            
            // Forfeiture
            $table->boolean('deposit_forfeited')->default(false);
            $table->decimal('forfeiture_amount', 12, 2)->nullable();
            $table->timestamp('forfeited_at')->nullable();
            
            // Payment
            $table->foreignId('transaction_id')->nullable()->constrained()->onDelete('set null');
            
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // Add reservation status to listings
        Schema::table('listings', function (Blueprint $table) {
            $table->boolean('is_reserved')->default(false)->after('status');
            $table->timestamp('reserved_until')->nullable()->after('is_reserved');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('listings', function (Blueprint $table) {
            $table->dropColumn(['is_reserved', 'reserved_until']);
        });
        
        Schema::dropIfExists('reservations');
    }
};
