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
        // Add UUID column to listings table (missing but used in model)
        if (!Schema::hasColumn('listings', 'uuid')) {
            Schema::table('listings', function (Blueprint $table) {
                $table->uuid('uuid')->nullable()->after('id');
            });
            
            // Populate existing records with UUIDs
            DB::table('listings')->whereNull('uuid')->get()->each(function ($listing) {
                DB::table('listings')
                    ->where('id', $listing->id)
                    ->update(['uuid' => (string) Str::uuid()]);
            });
            
            // Make it non-nullable and unique
            Schema::table('listings', function (Blueprint $table) {
                $table->uuid('uuid')->nullable(false)->unique()->change();
            });
        }

        // Add missing package fields
        Schema::table('packages', function (Blueprint $table) {
            if (!Schema::hasColumn('packages', 'max_listings')) {
                $table->integer('max_listings')->default(1)->after('limit_images');
            }
            if (!Schema::hasColumn('packages', 'max_featured_days')) {
                $table->integer('max_featured_days')->default(0)->after('max_listings');
            }
            if (!Schema::hasColumn('packages', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('max_featured_days');
            }
            if (!Schema::hasColumn('packages', 'sort_order')) {
                $table->integer('sort_order')->default(0)->after('is_active');
            }
        });

        // Add composite indexes for performance
        Schema::table('listings', function (Blueprint $table) {
            // Index for user's active listings query
            if (!$this->indexExists('listings', 'listings_user_id_status_index')) {
                $table->index(['user_id', 'status'], 'listings_user_id_status_index');
            }
            
            // Index for make/model searches
            if (!$this->indexExists('listings', 'listings_make_id_status_index')) {
                $table->index(['make_id', 'status'], 'listings_make_id_status_index');
            }
            
            // Index for featured listings
            if (!$this->indexExists('listings', 'listings_is_featured_status_index')) {
                $table->index(['is_featured', 'status'], 'listings_is_featured_status_index');
            }
            
            // Index for expiration queries
            if (!$this->indexExists('listings', 'listings_expired_at_status_index')) {
                $table->index(['expired_at', 'status'], 'listings_expired_at_status_index');
            }
            
            // Index for featured expiration
            if (!$this->indexExists('listings', 'listings_featured_until_index')) {
                $table->index('featured_until');
            }
        });

        // Add indexes to packages
        Schema::table('packages', function (Blueprint $table) {
            if (!$this->indexExists('packages', 'packages_is_active_index')) {
                $table->index('is_active');
            }
            if (!$this->indexExists('packages', 'packages_sort_order_index')) {
                $table->index('sort_order');
            }
        });

        // Add indexes to users
        Schema::table('users', function (Blueprint $table) {
            if (!$this->indexExists('users', 'users_role_index')) {
                $table->index('role');
            }
            if (!$this->indexExists('users', 'users_seller_status_index')) {
                $table->index('seller_status');
            }
            if (!$this->indexExists('users', 'users_status_index')) {
                $table->index('status');
            }
        });

        // Add indexes to transactions
        Schema::table('transactions', function (Blueprint $table) {
            if (!$this->indexExists('transactions', 'transactions_user_id_status_index')) {
                $table->index(['user_id', 'status']);
            }
            if (!$this->indexExists('transactions', 'transactions_type_status_index')) {
                $table->index(['type', 'status']);
            }
        });

        // Add indexes to reservations
        Schema::table('reservations', function (Blueprint $table) {
            if (!$this->indexExists('reservations', 'reservations_listing_id_status_index')) {
                $table->index(['listing_id', 'status']);
            }
            if (!$this->indexExists('reservations', 'reservations_user_id_status_index')) {
                $table->index(['user_id', 'status']);
            }
            if (!$this->indexExists('reservations', 'reservations_expires_at_index')) {
                $table->index('expires_at');
            }
        });

        if (DB::getDriverName() !== 'sqlite') {
            // Add check constraints for business rules - MySQL only for ALTER TABLE
            DB::statement('ALTER TABLE listings ADD CONSTRAINT listings_price_positive CHECK (price >= 0)');
            DB::statement('ALTER TABLE listings ADD CONSTRAINT listings_year_valid CHECK (year >= 1900 AND year <= ' . (date('Y') + 2) . ')');
            DB::statement('ALTER TABLE packages ADD CONSTRAINT packages_price_positive CHECK (price >= 0)');
            DB::statement('ALTER TABLE packages ADD CONSTRAINT packages_duration_positive CHECK (duration_days > 0)');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop check constraints
        if (DB::getDriverName() !== 'sqlite') {
            DB::statement('ALTER TABLE listings DROP CONSTRAINT IF EXISTS listings_price_positive');
            DB::statement('ALTER TABLE listings DROP CONSTRAINT IF EXISTS listings_year_valid');
            DB::statement('ALTER TABLE packages DROP CONSTRAINT IF EXISTS packages_price_positive');
            DB::statement('ALTER TABLE packages DROP CONSTRAINT IF EXISTS packages_duration_positive');
        }

        // Drop indexes
        Schema::table('listings', function (Blueprint $table) {
            $table->dropIndex('listings_user_id_status_index');
            $table->dropIndex('listings_make_id_status_index');
            $table->dropIndex('listings_is_featured_status_index');
            $table->dropIndex('listings_expired_at_status_index');
            $table->dropIndex('listings_featured_until_index');
        });

        Schema::table('packages', function (Blueprint $table) {
            $table->dropIndex('packages_is_active_index');
            $table->dropIndex('packages_sort_order_index');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex('users_role_index');
            $table->dropIndex('users_seller_status_index');
            $table->dropIndex('users_status_index');
        });

        Schema::table('transactions', function (Blueprint $table) {
            $table->dropIndex('transactions_user_id_status_index');
            $table->dropIndex('transactions_type_status_index');
        });

        Schema::table('reservations', function (Blueprint $table) {
            $table->dropIndex('reservations_listing_id_status_index');
            $table->dropIndex('reservations_user_id_status_index');
            $table->dropIndex('reservations_expires_at_index');
        });

        // Drop package columns
        Schema::table('packages', function (Blueprint $table) {
            $table->dropColumn(['max_listings', 'max_featured_days', 'is_active', 'sort_order']);
        });

        // Drop UUID column
        Schema::table('listings', function (Blueprint $table) {
            $table->dropColumn('uuid');
        });
    }

    /**
     * Check if an index exists
     */
    private function indexExists(string $table, string $index): bool
    {
        if (DB::getDriverName() === 'sqlite') {
            $result = DB::select(
                "SELECT 1 FROM sqlite_master WHERE type = 'index' AND tbl_name = ? AND name = ?",
                [$table, $index]
            );
            return !empty($result);
        }

        $result = DB::select(
            "SELECT 1 FROM INFORMATION_SCHEMA.STATISTICS 
             WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ? AND INDEX_NAME = ?",
            [DB::getDatabaseName(), $table, $index]
        );
        
        return !empty($result);
    }
};
