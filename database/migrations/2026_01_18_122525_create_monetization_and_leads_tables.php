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
        // 1. Packages Table
        Schema::create('packages', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->decimal('price', 10, 2);
            $table->integer('duration_days')->default(30);
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_top')->default(false);
            $table->integer('limit_images')->default(10);
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // 2. Leads/Analytics Table
        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('listing_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->enum('type', ['view', 'call', 'whatsapp', 'message']);
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();
        });

        // 3. Update Listings Table
        Schema::table('listings', function (Blueprint $table) {
            $table->foreignId('package_id')->nullable()->after('user_id')->constrained();
            $table->timestamp('expired_at')->nullable()->after('status');
            $table->timestamp('featured_until')->nullable()->after('is_featured');
            $table->integer('views_count')->default(0)->after('featured_until');
            $table->integer('calls_count')->default(0)->after('views_count');
            $table->integer('whatsapp_count')->default(0)->after('calls_count');
            $table->integer('ranking_score')->default(0)->after('whatsapp_count');
        });

        // 4. Conversations for Internal Messaging
        Schema::create('conversations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('listing_id')->constrained()->onDelete('cascade');
            $table->foreignId('buyer_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('seller_id')->constrained('users')->onDelete('cascade');
            $table->unique(['listing_id', 'buyer_id', 'seller_id']);
            $table->timestamps();
        });

        // 5. Update Messages Table to link with Conversations
        Schema::table('messages', function (Blueprint $table) {
            $table->foreignId('conversation_id')->nullable()->after('id')->constrained()->onDelete('cascade');
        });
        
        // 6. Global SEO Settings Table
        Schema::create('ads_seo', function (Blueprint $table) {
            $table->id();
            $table->string('path')->unique(); // URL path like /listing/toyota-camry
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->string('og_image')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ads_seo');
        Schema::table('messages', function (Blueprint $table) {
            $table->dropForeign(['conversation_id']);
            $table->dropColumn('conversation_id');
        });
        Schema::dropIfExists('conversations');
        Schema::table('listings', function (Blueprint $table) {
            $table->dropForeign(['package_id']);
            $table->dropColumn(['package_id', 'expired_at', 'featured_until', 'views_count', 'calls_count', 'whatsapp_count', 'ranking_score']);
        });
        Schema::dropIfExists('leads');
        Schema::dropIfExists('packages');
    }
};
