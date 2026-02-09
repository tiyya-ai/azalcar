<?php

namespace App\Console\Commands;

use App\Models\Listing;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class CleanOrphanImages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'images:clean-orphans {--dry-run : Show what would be deleted without actually deleting}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove orphan images that are not referenced in any listing';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $dryRun = $this->option('dry-run');
        
        if ($dryRun) {
            $this->warn('DRY RUN MODE - No files will be deleted');
        }

        $this->info('Scanning for orphan images...');

        // Get all image paths from database
        $dbImages = $this->getDatabaseImages();
        $this->info('Found ' . count($dbImages) . ' images referenced in database');

        // Get all image files from storage
        $storageImages = $this->getStorageImages();
        $this->info('Found ' . count($storageImages) . ' images in storage');

        // Find orphans (files in storage but not in database)
        $orphans = array_diff($storageImages, $dbImages);

        if (empty($orphans)) {
            $this->info('No orphan images found!');
            return 0;
        }

        $this->warn('Found ' . count($orphans) . ' orphan images');

        if ($dryRun) {
            $this->table(['Orphan Images'], array_map(fn($img) => [$img], $orphans));
            $this->info('Run without --dry-run to delete these files');
            return 0;
        }

        // Confirm deletion
        if (!$this->confirm('Do you want to delete these orphan images?')) {
            $this->info('Deletion cancelled');
            return 0;
        }

        // Delete orphans
        $deleted = 0;
        $failed = 0;

        foreach ($orphans as $orphan) {
            try {
                if (Storage::disk('public')->delete($orphan)) {
                    $deleted++;
                    $this->line("✓ Deleted: {$orphan}");
                } else {
                    $failed++;
                    $this->error("✗ Failed to delete: {$orphan}");
                }
            } catch (\Exception $e) {
                $failed++;
                $this->error("✗ Error deleting {$orphan}: {$e->getMessage()}");
                Log::error('Failed to delete orphan image', [
                    'path' => $orphan,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        $this->info("Deleted {$deleted} orphan images");
        if ($failed > 0) {
            $this->warn("Failed to delete {$failed} images");
        }

        return 0;
    }

    /**
     * Get all image paths from database
     *
     * @return array
     */
    protected function getDatabaseImages(): array
    {
        $images = [];

        // Get all listings
        $listings = Listing::withTrashed()->get();

        foreach ($listings as $listing) {
            // Main image
            if ($listing->main_image) {
                $cleanPath = str_replace('/storage/', '', $listing->main_image);
                $images[] = $cleanPath;
            }

            // Gallery images
            if ($listing->images && is_array($listing->images)) {
                foreach ($listing->images as $image) {
                    $cleanPath = str_replace('/storage/', '', $image);
                    $images[] = $cleanPath;
                }
            }
        }

        return array_unique($images);
    }

    /**
     * Get all image files from storage
     *
     * @return array
     */
    protected function getStorageImages(): array
    {
        $images = [];

        // Get files from listings directory
        $listingFiles = Storage::disk('public')->allFiles('listings');
        $images = array_merge($images, $listingFiles);

        // Get files from listings/gallery directory
        $galleryFiles = Storage::disk('public')->allFiles('listings/gallery');
        $images = array_merge($images, $galleryFiles);

        // Filter only image files
        $images = array_filter($images, function ($file) {
            $extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));
            return in_array($extension, ['jpg', 'jpeg', 'png', 'webp', 'gif']);
        });

        return array_values($images);
    }
}
