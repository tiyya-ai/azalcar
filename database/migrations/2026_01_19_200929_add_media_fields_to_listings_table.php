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
            $table->string('video_url')->nullable()->after('images');
            $table->json('media_files')->nullable()->after('video_url');
            $table->string('media_type')->default('images')->after('media_files'); // images, video, mixed
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('listings', function (Blueprint $table) {
            $table->dropColumn(['video_url', 'media_files', 'media_type']);
        });
    }
};
