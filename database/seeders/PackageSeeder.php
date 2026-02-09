<?php

namespace Database\Seeders;

use App\Models\Package;
use Illuminate\Database\Seeder;

class PackageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Package::updateOrCreate(['slug' => 'free'], [
            'name' => 'Free Listing',
            'price' => 0,
            'duration_days' => 30,
            'is_featured' => false,
            'is_top' => false,
            'limit_images' => 4,
            'description' => 'Perfect for selling Your personal car.',
        ]);

        Package::updateOrCreate(['slug' => 'featured'], [
            'name' => 'Featured Package',
            'price' => 10.00,
            'duration_days' => 7,
            'is_featured' => true,
            'is_top' => false,
            'limit_images' => 10,
            'description' => 'Your car will be highlighted and shown in the Featured section.',
        ]);

        Package::updateOrCreate(['slug' => 'top'], [
            'name' => 'Premium Top Ad',
            'price' => 25.00,
            'duration_days' => 14,
            'is_featured' => true,
            'is_top' => true,
            'limit_images' => 25,
            'description' => 'Maximum visibility! Shown at the top of search results and homepage.',
        ]);
    }
}
