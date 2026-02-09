<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AddIndexesForPerformance extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('listings', function (Blueprint $table) {
            // Check if indexes don't already exist before creating
            $indexesFound = $this->getExistingIndexes('listings');

            if (!isset($indexesFound['listings_status_index'])) {
                $table->index('status');
            }
            if (!isset($indexesFound['listings_is_featured_index'])) {
                $table->index('is_featured');
            }
            if (!isset($indexesFound['listings_make_id_vehicle_model_id_index'])) {
                $table->index(['make_id', 'vehicle_model_id']);
            }
            if (!isset($indexesFound['listings_user_id_index'])) {
                $table->index('user_id');
            }
        });

        Schema::table('leads', function (Blueprint $table) {
            $indexesFound = $this->getExistingIndexes('leads');

            if (!isset($indexesFound['leads_listing_id_type_index'])) {
                $table->index(['listing_id', 'type']);
            }
        });

        Schema::table('messages', function (Blueprint $table) {
            $indexesFound = $this->getExistingIndexes('messages');

            if (!isset($indexesFound['messages_conversation_id_index'])) {
                $table->index('conversation_id');
            }
        });

        Schema::table('conversations', function (Blueprint $table) {
            $indexesFound = $this->getExistingIndexes('conversations');

            if (!isset($indexesFound['conversations_buyer_id_seller_id_index'])) {
                $table->index(['buyer_id', 'seller_id']);
            }
        });
    }

    private function getExistingIndexes($tableName)
    {
        $indexes = [];
        $driver = DB::getDriverName();
        if ($driver === 'mysql') {
            $results = DB::select("SHOW INDEX FROM `$tableName`");
            foreach ($results as $result) {
                $indexes[$result->Key_name] = true;
            }
        } elseif ($driver === 'sqlite') {
            $results = DB::select("PRAGMA index_list(`$tableName`)");
            foreach ($results as $result) {
                $indexes[$result->name] = true;
            }
        } else {
            // For other drivers, assume no indexes to avoid errors
            $indexes = [];
        }
        return $indexes;
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('listings', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropIndex(['is_featured']);
            $table->dropIndex(['make_id', 'vehicle_model_id']);
            $table->dropIndex(['user_id']);
        });

        Schema::table('leads', function (Blueprint $table) {
            $table->dropIndex(['listing_id', 'type']);
        });

        Schema::table('messages', function (Blueprint $table) {
            $table->dropIndex(['conversation_id']);
        });

        Schema::table('conversations', function (Blueprint $table) {
            $table->dropIndex(['buyer_id', 'seller_id']);
        });
    }
}