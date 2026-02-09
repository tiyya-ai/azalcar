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
        Schema::create('commissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('listing_id')->constrained()->onDelete('cascade');
            $table->foreignId('seller_id')->constrained('users')->onDelete('cascade');
            $table->decimal('listing_price', 12, 2);
            $table->decimal('commission_percentage', 5, 2)->default(3.00); // 3%
            $table->decimal('commission_amount', 12, 2);
            $table->decimal('commission_cap', 12, 2)->default(900000.00); // 30M won * 3% = 900K won
            $table->decimal('final_commission', 12, 2); // After applying cap
            $table->string('status')->default('pending'); // pending, paid, waived
            $table->timestamp('paid_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // Add commission tracking to transactions table
        Schema::table('transactions', function (Blueprint $table) {
            $table->foreignId('commission_id')->nullable()->constrained()->onDelete('set null')->after('user_id');
            $table->foreignId('listing_id')->nullable()->constrained()->onDelete('set null')->after('commission_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropForeign(['commission_id']);
            $table->dropForeign(['listing_id']);
            $table->dropColumn(['commission_id', 'listing_id']);
        });
        
        Schema::dropIfExists('commissions');
    }
};
