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
        Schema::table('users', function (Blueprint $table) {
            // Add UUID for public-facing IDs (security)
            if (!Schema::hasColumn('users', 'uuid')) {
                $table->uuid('uuid')->unique()->after('id')->nullable();
            }
            
            // Phone verification
            if (!Schema::hasColumn('users', 'phone')) {
                $table->string('phone', 20)->nullable()->after('email_verified_at');
            }
            if (!Schema::hasColumn('users', 'phone_verified_at')) {
                $table->timestamp('phone_verified_at')->nullable()->after('phone');
            }
            
            // Seller-specific fields
            if (!Schema::hasColumn('users', 'seller_status')) {
                $table->enum('seller_status', ['none', 'pending', 'approved', 'rejected', 'suspended'])
                      ->default('none')->after('role');
            }
            if (!Schema::hasColumn('users', 'seller_bio')) {
                $table->text('seller_bio')->nullable()->after('seller_status');
                $table->string('seller_company')->nullable()->after('seller_bio');
                $table->string('seller_address')->nullable()->after('seller_company');
                $table->timestamp('seller_approved_at')->nullable()->after('seller_address');
            }
            
            // Security & tracking
            if (!Schema::hasColumn('users', 'last_login_ip')) {
                $table->string('last_login_ip', 45)->nullable()->after('seller_approved_at');
                $table->timestamp('last_login_at')->nullable()->after('last_login_ip');
            }
            
            if (!Schema::hasColumn('users', 'ban_reason')) {
                $table->text('ban_reason')->nullable()->after('last_login_at');
            }
            
            // Soft deletes
            if (!Schema::hasColumn('users', 'deleted_at')) {
                $table->softDeletes();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'uuid',
                'email_verified_at',
                'phone',
                'phone_verified_at',
                'seller_status',
                'seller_bio',
                'seller_company',
                'seller_address',
                'seller_approved_at',
                'last_login_ip',
                'last_login_at',
                'is_banned',
                'ban_reason',
                'deleted_at'
            ]);
        });
    }
};
