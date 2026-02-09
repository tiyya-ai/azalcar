<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Listing Statuses
    |--------------------------------------------------------------------------
    |
    | All possible statuses for listings in the system.
    |
    */
    'statuses' => [
        'draft',      // User is still creating the listing
        'pending',    // Submitted for admin review
        'approved',   // Admin approved, ready to go active
        'active',     // Currently visible to public
        'expired',    // Listing duration has ended
        'rejected',   // Admin rejected the listing
        'archived',   // User or admin archived
    ],

    /*
    |--------------------------------------------------------------------------
    | Status Flow
    |--------------------------------------------------------------------------
    |
    | Defines allowed status transitions. Each status can only transition
    | to the statuses listed in its array.
    |
    */
    'status_flow' => [
        'draft' => ['pending', 'archived'],
        'pending' => ['approved', 'rejected'],
        'approved' => ['active', 'rejected'],
        'active' => ['expired', 'archived'],
        'expired' => ['active', 'archived'],
        'rejected' => ['pending', 'archived'],
        'archived' => [],
    ],

    /*
    |--------------------------------------------------------------------------
    | Image Configuration
    |--------------------------------------------------------------------------
    |
    | Settings for listing image uploads and validation.
    |
    */
    'images' => [
        'max_size' => env('LISTING_IMAGE_MAX_SIZE', 5120), // KB
        'allowed_mimes' => ['image/jpeg', 'image/png', 'image/webp'],
        'allowed_extensions' => ['jpg', 'jpeg', 'png', 'webp'],
        'min_dimensions' => [
            'width' => 200,
            'height' => 200,
        ],
        'max_dimensions' => [
            'width' => 4096,
            'height' => 4096,
        ],
        'max_per_listing' => env('LISTING_MAX_IMAGES', 20),
    ],

    /*
    |--------------------------------------------------------------------------
    | Expiration Settings
    |--------------------------------------------------------------------------
    |
    | Default expiration and archival settings for listings.
    |
    */
    'auto_expire_days' => env('LISTING_AUTO_EXPIRE_DAYS', 30),
    'auto_archive_days' => env('LISTING_AUTO_ARCHIVE_DAYS', 90),
    
    /*
    |--------------------------------------------------------------------------
    | Validation Rules
    |--------------------------------------------------------------------------
    |
    | Business rules for listing creation and updates.
    |
    */
    'validation' => [
        'title' => [
            'min_length' => 10,
            'max_length' => 255,
        ],
        'description' => [
            'min_length' => 50,
            'max_length' => 10000,
        ],
        'price' => [
            'min' => 0,
            'max' => 999999999,
        ],
        'year' => [
            'min' => 1900,
            'max' => (int) date('Y') + 2,
        ],
        'mileage' => [
            'min' => 0,
            'max' => 999999,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Security Settings
    |--------------------------------------------------------------------------
    |
    | Security-related settings for listings.
    |
    */
    'security' => [
        'require_approval' => env('LISTING_REQUIRE_APPROVAL', true),
        'auto_approve_verified_users' => env('LISTING_AUTO_APPROVE_VERIFIED', false),
        'max_listings_per_user_per_day' => env('LISTING_MAX_PER_DAY', 5),
    ],
];
