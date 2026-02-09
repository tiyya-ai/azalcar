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
            $table->index('status', 'idx_listings_status');
            $table->index('is_featured', 'idx_listings_featured');
            $table->index(['make_id', 'vehicle_model_id'], 'idx_listings_make_model');
            $table->index('user_id', 'idx_listings_user');
            $table->index('created_at', 'idx_listings_created');
            $table->index(['latitude', 'longitude'], 'idx_listings_location');
        });

        Schema::table('leads', function (Blueprint $table) {
            $table->index(['listing_id', 'type'], 'idx_leads_listing_type');
            $table->index('created_at', 'idx_leads_created');
        });

        Schema::table('conversations', function (Blueprint $table) {
            $table->index('buyer_id', 'idx_conversations_buyer');
            $table->index('seller_id', 'idx_conversations_seller');
            $table->index('updated_at', 'idx_conversations_updated');
        });

        Schema::table('messages', function (Blueprint $table) {
            $table->index('conversation_id', 'idx_messages_conversation');
            $table->index(['receiver_id', 'is_read'], 'idx_messages_receiver_read');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('listings', function (Blueprint $table) {
            $table->dropIndex('idx_listings_status');
            $table->dropIndex('idx_listings_featured');
            $table->dropIndex('idx_listings_make_model');
            $table->dropIndex('idx_listings_user');
            $table->dropIndex('idx_listings_created');
            $table->dropIndex('idx_listings_location');
        });

        Schema::table('leads', function (Blueprint $table) {
            $table->dropIndex('idx_leads_listing_type');
            $table->dropIndex('idx_leads_created');
        });

        Schema::table('conversations', function (Blueprint $table) {
            $table->dropIndex('idx_conversations_buyer');
            $table->dropIndex('idx_conversations_seller');
            $table->dropIndex('idx_conversations_updated');
        });

        Schema::table('messages', function (Blueprint $table) {
            $table->dropIndex('idx_messages_conversation');
            $table->dropIndex('idx_messages_receiver_read');
        });
    }
};
