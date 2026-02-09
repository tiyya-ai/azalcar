<?php

namespace App\Services;

use App\Models\Package;
use App\Models\User;
use App\Models\Listing;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PackageService
{
    /**
     * Check if user can create a listing with the given package
     *
     * @param User $user
     * @param Package $package
     * @return bool
     * @throws \Exception
     */
    public function canUserCreateListing(User $user, Package $package): bool
    {
        // Check if package is active
        if (!$package->is_active) {
            throw new \Exception('This package is not currently available.');
        }

        // Check if user has reached the package limit
        if ($user->hasReachedPackageLimit($package)) {
            throw new \Exception("You have reached the maximum number of listings ({$package->max_listings}) for this package.");
        }

        // Check if user has sufficient balance (if package has a price)
        if ($package->price > 0 && $user->balance < $package->price) {
            throw new \Exception('Insufficient balance. Please top up your wallet.');
        }

        return true;
    }

    /**
     * Enforce image limit for a package
     *
     * @param Package $package
     * @param int $imageCount
     * @return void
     * @throws \Exception
     */
    public function enforceImageLimit(Package $package, int $imageCount): void
    {
        if ($imageCount > $package->limit_images) {
            throw new \Exception("Your package allows a maximum of {$package->limit_images} images. You attempted to upload {$imageCount}.");
        }
    }

    /**
     * Apply package to a listing
     *
     * @param Listing $listing
     * @param Package $package
     * @param User $user
     * @return void
     * @throws \Exception
     */
    public function applyPackageToListing(Listing $listing, Package $package, User $user): void
    {
        DB::transaction(function () use ($listing, $package, $user) {
            // Deduct balance if package has a price
            if ($package->price > 0) {
                if (!$user->updateBalance($package->price, 'subtract')) {
                    throw new \Exception('Failed to deduct package price from balance.');
                }

                // Create transaction record
                Transaction::create([
                    'user_id' => $user->id,
                    'amount' => $package->price,
                    'type' => 'debit',
                    'description' => "Package applied to listing: {$listing->title} ({$package->name})",
                    'payment_method' => 'wallet',
                    'currency' => 'USD',
                    'status' => 'completed',
                    'reference_id' => 'PKG-' . $listing->id . '-' . time(),
                ]);
            }

            // Update listing with package details
            $listing->package_id = $package->id;
            $listing->expired_at = $package->getExpirationDate();

            // Apply featured status if package supports it
            if ($package->canFeatureListing()) {
                $listing->is_featured = true;
                $listing->featured_until = $package->getFeaturedExpirationDate();
            }

            $listing->save();

            Log::info('Package applied to listing', [
                'listing_id' => $listing->id,
                'package_id' => $package->id,
                'user_id' => $user->id,
                'price' => $package->price,
            ]);
        });
    }

    /**
     * Upgrade listing package
     *
     * @param Listing $listing
     * @param Package $newPackage
     * @return void
     * @throws \Exception
     */
    public function upgradeListingPackage(Listing $listing, Package $newPackage): void
    {
        $user = $listing->user;
        $currentPackage = $listing->package;

        // Validate upgrade
        if ($currentPackage && $newPackage->price <= $currentPackage->price) {
            throw new \Exception('You can only upgrade to a higher-tier package.');
        }

        // Calculate price difference
        $priceDifference = $newPackage->price - ($currentPackage ? $currentPackage->price : 0);

        DB::transaction(function () use ($listing, $newPackage, $user, $priceDifference) {
            // Deduct price difference
            if ($priceDifference > 0) {
                if (!$user->updateBalance($priceDifference, 'subtract')) {
                    throw new \Exception('Insufficient balance for package upgrade.');
                }

                // Create transaction record
                Transaction::create([
                    'user_id' => $user->id,
                    'amount' => $priceDifference,
                    'type' => 'debit',
                    'description' => "Package upgrade for listing: {$listing->title} ({$newPackage->name})",
                    'payment_method' => 'wallet',
                    'currency' => 'USD',
                    'status' => 'completed',
                    'reference_id' => 'UPG-' . $listing->id . '-' . time(),
                ]);
            }

            // Update listing
            $listing->package_id = $newPackage->id;
            
            // Extend expiration
            $listing->expired_at = $newPackage->getExpirationDate();

            // Update featured status
            if ($newPackage->canFeatureListing()) {
                $listing->is_featured = true;
                $listing->featured_until = $newPackage->getFeaturedExpirationDate();
            }

            $listing->save();

            Log::info('Package upgraded', [
                'listing_id' => $listing->id,
                'new_package_id' => $newPackage->id,
                'price_difference' => $priceDifference,
            ]);
        });
    }

    /**
     * Renew listing with same package
     *
     * @param Listing $listing
     * @return void
     * @throws \Exception
     */
    public function renewListing(Listing $listing): void
    {
        $package = $listing->package;
        $user = $listing->user;

        if (!$package) {
            throw new \Exception('Listing does not have an associated package.');
        }

        // Prevent free renewals
        if ($package->price <= 0) {
            throw new \Exception('Free packages cannot be renewed. Please upgrade to a paid package.');
        }

        DB::transaction(function () use ($listing, $package, $user) {
            // Deduct renewal price
            if (!$user->updateBalance($package->price, 'subtract')) {
                throw new \Exception('Insufficient balance for renewal.');
            }

            // Create transaction record
            Transaction::create([
                'user_id' => $user->id,
                'amount' => $package->price,
                'type' => 'debit',
                'description' => "Listing renewal: {$listing->title} ({$package->name})",
                'payment_method' => 'wallet',
                'currency' => 'USD',
                'status' => 'completed',
                'reference_id' => 'RNW-' . $listing->id . '-' . time(),
            ]);

            // Extend expiration
            $listing->expired_at = $package->getExpirationDate();
            
            // Reactivate if expired
            if ($listing->status === 'expired') {
                $listing->status = 'active';
            }

            // Renew featured status if applicable
            if ($package->canFeatureListing()) {
                $listing->is_featured = true;
                $listing->featured_until = $package->getFeaturedExpirationDate();
            }

            $listing->save();

            Log::info('Listing renewed', [
                'listing_id' => $listing->id,
                'package_id' => $package->id,
                'new_expiration' => $listing->expired_at,
            ]);

            // Send notification
            $user->notify(new \App\Notifications\ListingRenewed($listing));
        });
    }

    /**
     * Get available packages for a user
     *
     * @param User $user
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAvailablePackages(User $user)
    {
        return Package::active()
            ->ordered()
            ->get()
            ->filter(function ($package) use ($user) {
                // Only show packages user can afford or free packages
                return $package->price == 0 || $user->balance >= $package->price;
            });
    }

    /**
     * Validate package selection for listing creation
     *
     * @param Package $package
     * @param array $listingData
     * @return void
     * @throws \Exception
     */
    public function validatePackageForListing(Package $package, array $listingData): void
    {
        // Validate image count
        $imageCount = 0;
        if (isset($listingData['gallery']) && is_array($listingData['gallery'])) {
            $imageCount += count($listingData['gallery']);
        }
        if (isset($listingData['main_image'])) {
            $imageCount++;
        }

        $this->enforceImageLimit($package, $imageCount);

        // Validate featured request
        if (isset($listingData['is_featured']) && $listingData['is_featured']) {
            if (!$package->canFeatureListing()) {
                throw new \Exception('This package does not support featured listings.');
            }
        }
    }
}
