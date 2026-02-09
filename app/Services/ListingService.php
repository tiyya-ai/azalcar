<?php

namespace App\Services;

use App\Models\Listing;
use App\Models\Package;
use App\Models\User;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ListingService
{
    protected ImageService $imageService;

    public function __construct(ImageService $imageService)
    {
        $this->imageService = $imageService;
    }

    /**
     * Create a new listing
     *
     * @param User $user
     * @param array $data
     * @param Package $package
     * @return Listing
     * @throws \Exception
     */
    public function createListing(User $user, array $data, Package $package): Listing
    {
        return DB::transaction(function () use ($user, $data, $package) {
            // Validate user can create listing
            if (!$user->canCreateListing()) {
                throw new \Exception('You have reached your daily listing limit.');
            }

            // Validate package limit
            if ($user->hasReachedPackageLimit($package)) {
                throw new \Exception("You have reached the maximum number of listings for this package ({$package->max_listings}).");
            }

            // Validate image count
            $imageCount = 0;
            if (isset($data['gallery']) && is_array($data['gallery'])) {
                $imageCount = count($data['gallery']);
            }
            if (isset($data['main_image'])) {
                $imageCount++;
            }

            if (!$package->validateImageCount($imageCount)) {
                throw new \Exception("Your package allows a maximum of {$package->limit_images} images.");
            }

            // Prepare listing data
            $listingData = $this->prepareListingData($data);
            
            // Create listing instance
            $listing = new Listing($listingData);
            $listing->user_id = $user->id;
            $listing->package_id = $package->id;
            $listing->status = config('listing.security.require_approval', true) ? 'pending' : 'approved';
            $listing->expired_at = $package->getExpirationDate();
            
            // Handle featured status
            if ($package->canFeatureListing()) {
                $listing->is_featured = true;
                $listing->featured_until = $package->getFeaturedExpirationDate();
            }

            // Handle main image upload
            if (isset($data['main_image']) && $data['main_image']) {
                $listing->main_image = $this->imageService->uploadImage(
                    $data['main_image'],
                    'listings'
                );
            }

            // Handle gallery images
            if (isset($data['gallery']) && is_array($data['gallery'])) {
                $galleryPaths = [];
                foreach ($data['gallery'] as $image) {
                    $galleryPaths[] = $this->imageService->uploadImage(
                        $image,
                        'listings/gallery'
                    );
                }
                $listing->images = $galleryPaths;
            }

            $listing->save();

            // Create transaction record if package has a price
            if ($package->price > 0) {
                $this->createTransaction($user, $package, $listing);
            }

            // Send notifications
            $this->sendCreationNotifications($listing);

            Log::info('Listing created successfully', [
                'listing_id' => $listing->id,
                'user_id' => $user->id,
                'package_id' => $package->id,
                'status' => $listing->status,
            ]);

            return $listing;
        });
    }

    /**
     * Update an existing listing
     *
     * @param Listing $listing
     * @param array $data
     * @return Listing
     * @throws \Exception
     */
    public function updateListing(Listing $listing, array $data): Listing
    {
        return DB::transaction(function () use ($listing, $data) {
            // Prepare data (remove protected fields)
            $updateData = $this->prepareListingData($data);

            // Handle main image upload
            if (isset($data['main_image']) && $data['main_image']) {
                // Delete old image
                if ($listing->main_image) {
                    $this->imageService->deleteImage($listing->main_image);
                }
                
                $updateData['main_image'] = $this->imageService->uploadImage(
                    $data['main_image'],
                    'listings'
                );
            }

            // Handle main image deletion
            if (isset($data['delete_main_image']) && $data['delete_main_image']) {
                if ($listing->main_image) {
                    $this->imageService->deleteImage($listing->main_image);
                }
                $updateData['main_image'] = null;
            }

            // Handle gallery images
            $currentImages = $listing->images ?? [];

            // Remove deleted images
            if (isset($data['removed_images']) && is_array($data['removed_images'])) {
                foreach ($data['removed_images'] as $imagePath) {
                    $this->imageService->deleteImage($imagePath);
                    $currentImages = array_filter($currentImages, fn($img) => $img !== $imagePath);
                }
            }

            // Add new images
            if (isset($data['gallery']) && is_array($data['gallery'])) {
                // Validate total image count
                $totalImages = count($currentImages) + count($data['gallery']);
                if ($listing->package && !$listing->package->validateImageCount($totalImages)) {
                    throw new \Exception("Your package allows a maximum of {$listing->package->limit_images} images.");
                }

                foreach ($data['gallery'] as $image) {
                    $currentImages[] = $this->imageService->uploadImage(
                        $image,
                        'listings/gallery'
                    );
                }
            }

            $updateData['images'] = array_values($currentImages);

            // Update the listing
            $listing->update($updateData);

            Log::info('Listing updated successfully', [
                'listing_id' => $listing->id,
                'updated_fields' => array_keys($updateData),
            ]);

            return $listing->fresh();
        });
    }

    /**
     * Delete a listing
     *
     * @param Listing $listing
     * @return bool
     */
    public function deleteListing(Listing $listing): bool
    {
        return DB::transaction(function () use ($listing) {
            // Delete all images
            if ($listing->main_image) {
                $this->imageService->deleteImage($listing->main_image);
            }

            if ($listing->images && is_array($listing->images)) {
                foreach ($listing->images as $image) {
                    $this->imageService->deleteImage($image);
                }
            }

            // Soft delete the listing
            $deleted = $listing->delete();

            Log::info('Listing deleted', [
                'listing_id' => $listing->id,
                'user_id' => $listing->user_id,
            ]);

            return $deleted;
        });
    }

    /**
     * Approve a listing (admin action)
     *
     * @param Listing $listing
     * @return bool
     */
    public function approveListing(Listing $listing): bool
    {
        $listing->status = 'approved';
        
        // Set expiration if not already set
        if (!$listing->expired_at && $listing->package) {
            $listing->expired_at = $listing->package->getExpirationDate();
        }

        // Set featured expiration if applicable
        if ($listing->is_featured && !$listing->featured_until && $listing->package) {
            $listing->featured_until = $listing->package->getFeaturedExpirationDate();
        }

        $saved = $listing->save();

        if ($saved) {
            // Send approval notification
            $listing->user->notify(new \App\Notifications\ListingApproved($listing));
            
            Log::info('Listing approved', [
                'listing_id' => $listing->id,
                'expired_at' => $listing->expired_at,
            ]);
        }

        return $saved;
    }

    /**
     * Reject a listing (admin action)
     *
     * @param Listing $listing
     * @param string|null $reason
     * @return bool
     */
    public function rejectListing(Listing $listing, ?string $reason = null): bool
    {
        $listing->status = 'rejected';
        $saved = $listing->save();

        if ($saved) {
            // Send rejection notification
            $listing->user->notify(new \App\Notifications\ListingRejected($listing, $reason));
            
            Log::info('Listing rejected', [
                'listing_id' => $listing->id,
                'reason' => $reason,
            ]);
        }

        return $saved;
    }

    /**
     * Expire a listing
     *
     * @param Listing $listing
     * @return bool
     */
    public function expireListing(Listing $listing): bool
    {
        $listing->status = 'expired';
        $saved = $listing->save();

        if ($saved) {
            // Send expiration notification
            $listing->user->notify(new \App\Notifications\ListingExpired($listing));
            
            Log::info('Listing expired', [
                'listing_id' => $listing->id,
            ]);
        }

        return $saved;
    }

    /**
     * Expire featured status
     *
     * @param Listing $listing
     * @return bool
     */
    public function expireFeaturedStatus(Listing $listing): bool
    {
        $listing->is_featured = false;
        $saved = $listing->save();

        if ($saved) {
            Log::info('Featured status expired', [
                'listing_id' => $listing->id,
            ]);
        }

        return $saved;
    }

    /**
     * Prepare listing data by removing protected fields
     *
     * @param array $data
     * @return array
     */
    protected function prepareListingData(array $data): array
    {
        // Remove fields that should not be mass assigned
        $protected = [
            'id', 'user_id', 'package_id', 'status', 'is_featured',
            'expired_at', 'featured_until', 'is_reserved', 'reserved_until',
            'uuid', 'created_at', 'updated_at', 'deleted_at',
            'main_image', 'gallery', 'removed_images', 'delete_main_image'
        ];

        return array_diff_key($data, array_flip($protected));
    }

    /**
     * Create transaction record for listing creation
     *
     * @param User $user
     * @param Package $package
     * @param Listing $listing
     * @return void
     */
    protected function createTransaction(User $user, Package $package, Listing $listing): void
    {
        Transaction::create([
            'user_id' => $user->id,
            'amount' => $package->price,
            'type' => 'debit',
            'description' => "Listing created: {$listing->title} ({$package->name} package)",
            'payment_method' => 'wallet',
            'currency' => 'USD',
            'status' => 'completed',
            'reference_id' => 'LST-' . $listing->id . '-' . time(),
        ]);
    }

    /**
     * Send notifications for listing creation
     *
     * @param Listing $listing
     * @return void
     */
    protected function sendCreationNotifications(Listing $listing): void
    {
        // Notify admins if listing is pending
        if ($listing->status === 'pending') {
            $admins = User::where('role', 'admin')->get();
            foreach ($admins as $admin) {
                $admin->notify(new \App\Notifications\NewListingPending($listing));
            }
        }
    }
}
