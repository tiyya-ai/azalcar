<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'email_verified')) {
                $table->boolean('email_verified')->default(false)->after('email_verified_at');
            }
            if (!Schema::hasColumn('users', 'phone_verified')) {
                $table->boolean('phone_verified')->default(false)->after('email_verified');
            }
            if (!Schema::hasColumn('users', 'phone_number')) {
                $table->string('phone_number')->nullable()->after('phone_verified');
            }
            if (!Schema::hasColumn('users', 'total_sales')) {
                $table->integer('total_sales')->default(0)->after('phone_number');
            }
            if (!Schema::hasColumn('users', 'average_rating')) {
                $table->decimal('average_rating', 3, 2)->default(0)->after('total_sales');
            }
            if (!Schema::hasColumn('users', 'reviews_count')) {
                $table->integer('reviews_count')->default(0)->after('average_rating');
            }
            if (!Schema::hasColumn('users', 'verified_at')) {
                $table->timestamp('verified_at')->nullable()->after('reviews_count');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $columns = ['email_verified', 'phone_verified', 'phone_number', 'total_sales', 'average_rating', 'reviews_count', 'verified_at'];
            foreach ($columns as $column) {
                if (Schema::hasColumn('users', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
