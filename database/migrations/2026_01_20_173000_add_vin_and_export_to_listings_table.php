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
        Schema::table('listings', function (Blueprint $table) {
            // VIN/Chassis Number - Unique to prevent duplicates
            $table->string('vin', 17)->nullable()->unique()->after('slug');
            
            // Export Eligibility
            $table->boolean('is_exportable')->default(false)->after('status');
            
            // License Plate (for auto-population feature)
            $table->string('license_plate', 20)->nullable()->after('vin');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('listings', function (Blueprint $table) {
            $table->dropColumn(['vin', 'is_exportable', 'license_plate']);
        });
    }
};
