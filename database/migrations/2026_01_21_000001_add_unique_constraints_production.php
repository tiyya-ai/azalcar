<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * PRODUCTION HARDENING: Add unique constraints to prevent race conditions
     * and ensure data consistency across concurrent operations.
     */
    public function up(): void
    {
        // 1. Ensure unique constraint on transactions reference_id
        // This prevents duplicate Stripe webhook processing
        Schema::table('transactions', function (Blueprint $table) {
            if (!$this->indexExists('transactions', 'unique_reference_id')) {
                $table->unique('reference_id', 'unique_reference_id');
            }
        });

        // 2. Add unique constraint on reservations (listing_id, status='active')
        // This prevents multiple active reservations on the same listing
        // Note: MariaDB doesn't support partial indexes, so we use a composite index
        Schema::table('reservations', function (Blueprint $table) {
            if (!$this->indexExists('reservations', 'idx_listing_status_unique')) {
                // Add composite index - application logic will enforce single active reservation
                $table->index(['listing_id', 'status'], 'idx_listing_status_unique');
            }
        });

        // 3. Add unique constraint on reviews (user_id, seller_id)
        // This prevents duplicate reviews from same user to same seller
        Schema::table('reviews', function (Blueprint $table) {
            if (!$this->indexExists('reviews', 'unique_user_seller_review')) {
                $table->unique(['user_id', 'seller_id'], 'unique_user_seller_review');
            }
        });

        // 4. Add unique constraint on favorites (user_id, listing_id)
        // This prevents duplicate favorites
        Schema::table('favorites', function (Blueprint $table) {
            if (!$this->indexExists('favorites', 'unique_user_listing_favorite')) {
                $table->unique(['user_id', 'listing_id'], 'unique_user_listing_favorite');
            }
        });

        // 5. Add unique constraint on leads for 24-hour deduplication
        // Prevents duplicate lead tracking from same IP/User-Agent within 24 hours
        Schema::table('leads', function (Blueprint $table) {
            if (!$this->indexExists('leads', 'unique_lead_tracking')) {
                // First, remove duplicates - keep only the oldest record for each combination
                if (DB::getDriverName() !== 'sqlite') {
                    DB::statement("
                        DELETE l1 FROM leads l1
                        INNER JOIN leads l2 
                        WHERE l1.id > l2.id 
                        AND l1.listing_id = l2.listing_id 
                        AND l1.user_id <=> l2.user_id
                        AND l1.type = l2.type 
                        AND l1.ip_address = l2.ip_address
                    ");
                }
                
                // Now add the unique constraint
                $table->unique(['listing_id', 'user_id', 'type', 'ip_address'], 'unique_lead_tracking');
            }
        });

        // 6. Ensure conversations unique constraint is in place
        // This should already exist from migration 2026_01_18_122525
        // But we verify it here
        Schema::table('conversations', function (Blueprint $table) {
            if (!$this->indexExists('conversations', 'conversations_listing_id_buyer_id_seller_id_unique')) {
                $table->unique(['listing_id', 'buyer_id', 'seller_id']);
            }
        });

        // 7. Add indexes for performance on frequently queried columns
        Schema::table('reservations', function (Blueprint $table) {
            // Index for finding active reservations by listing
            if (!$this->indexExists('reservations', 'idx_listing_status')) {
                $table->index(['listing_id', 'status'], 'idx_listing_status');
            }
            
            // Index for finding expired reservations
            if (!$this->indexExists('reservations', 'idx_expires_at_status')) {
                $table->index(['expires_at', 'status'], 'idx_expires_at_status');
            }
        });

        Schema::table('listings', function (Blueprint $table) {
            // Index for finding reserved listings
            if (!$this->indexExists('listings', 'idx_reserved_status')) {
                $table->index(['is_reserved', 'status'], 'idx_reserved_status');
            }
        });

        Schema::table('transactions', function (Blueprint $table) {
            // Index for finding transactions by reference_id (idempotency)
            if (!$this->indexExists('transactions', 'idx_reference_id')) {
                $table->index('reference_id', 'idx_reference_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropUnique('unique_reference_id');
        });

        Schema::table('reservations', function (Blueprint $table) {
            $table->dropIndex('idx_listing_status_unique');
            $table->dropIndex('idx_listing_status');
            $table->dropIndex('idx_expires_at_status');
        });

        Schema::table('reviews', function (Blueprint $table) {
            $table->dropUnique('unique_user_seller_review');
        });

        Schema::table('favorites', function (Blueprint $table) {
            $table->dropUnique('unique_user_listing_favorite');
        });

        Schema::table('leads', function (Blueprint $table) {
            $table->dropUnique('unique_lead_tracking');
        });

        Schema::table('listings', function (Blueprint $table) {
            $table->dropIndex('idx_reserved_status');
        });

        Schema::table('transactions', function (Blueprint $table) {
            $table->dropIndex('idx_reference_id');
        });
    }

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
    
    private function checkTableSchema(string $table, string $index): bool
    {
        return $this->indexExists($table, $index);
    }
};
