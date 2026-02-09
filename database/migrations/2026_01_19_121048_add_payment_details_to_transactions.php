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
        Schema::table('transactions', function (Blueprint $table) {
            $table->string('reference_id')->nullable()->after('status')->index(); // e.g. Stripe Intent ID
            $table->string('payment_method')->default('system')->after('reference_id'); // stripe, paypal, wallet, system
            $table->string('currency', 3)->default('USD')->after('amount');
        });
    }

    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn(['reference_id', 'payment_method', 'currency']);
        });
    }
};
