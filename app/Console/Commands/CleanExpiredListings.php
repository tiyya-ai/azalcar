<?php

namespace App\Console\Commands;

use App\Models\Listing;
use App\Services\ListingService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CleanExpiredListings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'listings:clean-expired {--archive-days=90 : Days after expiration to auto-archive}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Mark expired listings as expired and optionally archive old ones';

    protected ListingService $listingService;

    public function __construct(ListingService $listingService)
    {
        parent::__construct();
        $this->listingService = $listingService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Cleaning expired listings...');

        // Find listings that have passed their expiration date but are still active
        $expiredListings = Listing::where('expired_at', '<', now())
            ->whereIn('status', ['active', 'approved'])
            ->get();

        if ($expiredListings->isEmpty()) {
            $this->info('No expired listings found.');
        } else {
            $count = 0;
            foreach ($expiredListings as $listing) {
                try {
                    $this->listingService->expireListing($listing);
                    $count++;
                    $this->line("✓ Expired listing #{$listing->id}: {$listing->title}");
                } catch (\Exception $e) {
                    $this->error("✗ Failed to expire listing #{$listing->id}: {$e->getMessage()}");
                    Log::error('Failed to expire listing', [
                        'listing_id' => $listing->id,
                        'error' => $e->getMessage(),
                    ]);
                }
            }

            $this->info("Successfully expired {$count} listings.");
        }

        // Handle featured expiration
        $this->info('Checking featured listings...');
        $expiredFeatured = Listing::where('is_featured', true)
            ->where('featured_until', '<', now())
            ->get();

        if ($expiredFeatured->isNotEmpty()) {
            $featuredCount = 0;
            foreach ($expiredFeatured as $listing) {
                try {
                    $this->listingService->expireFeaturedStatus($listing);
                    $featuredCount++;
                    $this->line("✓ Expired featured status for listing #{$listing->id}");
                } catch (\Exception $e) {
                    $this->error("✗ Failed to expire featured status for listing #{$listing->id}: {$e->getMessage()}");
                }
            }

            $this->info("Expired featured status for {$featuredCount} listings.");
        }

        // Auto-archive old expired listings
        $archiveDays = (int) $this->option('archive-days');
        if ($archiveDays > 0) {
            $this->info("Archiving listings expired more than {$archiveDays} days ago...");
            
            $archiveDate = now()->subDays($archiveDays);
            $toArchive = Listing::where('status', 'expired')
                ->where('expired_at', '<', $archiveDate)
                ->get();

            if ($toArchive->isNotEmpty()) {
                $archivedCount = 0;
                foreach ($toArchive as $listing) {
                    $listing->status = 'archived';
                    $listing->save();
                    $archivedCount++;
                }

                $this->info("Archived {$archivedCount} old expired listings.");
            }
        }

        $this->info('Cleanup complete!');
        return 0;
    }
}
