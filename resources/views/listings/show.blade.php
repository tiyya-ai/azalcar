@extends('layouts.app')

@section('title', $listing->title . ' - azalcars Style')

@section('content')
<script type="application/ld+json">
{
  "@@context": "https://schema.org/",
  "@@type": "Vehicle",
  "name": "{{ $listing->year }} {{ $listing->make->name }} {{ $listing->vehicleModel->name }}",
  "image": "{{ $listing->main_image }}",
  "description": "{{ Str::limit($listing->description, 160) }}",
  "offers": {
    "@@type": "Offer",
    "price": "{{ $listing->price }}",
    "priceCurrency": "USD",
    "availability": "https://schema.org/InStock"
  },
  "brand": {
    "@@type": "Brand",
    "name": "{{ $listing->make->name }}"
  },
  "vehicleModel": "{{ $listing->vehicleModel->name }}",
  "vehicleEngine": "{{ $listing->engine_size }}",
  "mileageFromOdometer": {
    "@@type": "QuantitativeValue",
    "value": "{{ $listing->mileage }}",
    "unitCode": "SMI"
  }
}
</script>
<div class="listing-detail-page">
    <!-- Sticky Header (Hidden by default) -->
    <div id="sticky-header" class="sticky-listing-header">
        <div class="container sticky-content">
            <div class="sticky-info">
                <div class="sticky-title">{{ $listing->year }} {{ $listing->make->name }} {{ $listing->vehicleModel->name }}</div>
                <div class="sticky-price">{!! \App\Helpers\Helpers::formatPrice($listing->price) !!}</div>
            </div>
            <div class="sticky-actions">
                <button class="btn-sticky-action" onclick="openMessageModal()">Send message</button>
            </div>
        </div>
    </div>

    <!-- ... existing container ... -->
    <div class="container" style="max-width: 1200px; margin: 0 auto; padding: 20px 24px;">
        <!-- ... existing content ... -->
        <!-- Breadcrumbs -->
        <nav class="breadcrumbs">
            <a href="{{ url('/') }}">Home</a>
            <span class="divider">/</span>
            <a href="{{ route('listings.search', ['make' => $listing->make->slug]) }}">{{ $listing->make->name }}</a>
            <span class="divider">/</span>
            <a href="{{ route('listings.search', ['make' => $listing->make->slug, 'model' => $listing->vehicleModel->id]) }}">{{ $listing->vehicleModel->name }}</a>
            <span class="divider">/</span>
            <span class="current">{{ $listing->year }} {{ $listing->make->name }} {{ $listing->vehicleModel->name }}</span>
        </nav>

        <!-- Gallery Section (Full Width Top) -->
        @php
            $allImages = [];
            if($listing->main_image) $allImages[] = $listing->main_image;
            if($listing->images) {
                foreach($listing->images as $img) {
                    $allImages[] = $img;
                }
            }
            $count = count($allImages);
        @endphp

        <div class="cars-gallery-section" style="margin-bottom: 32px;">
            @if($count > 0)
                <div class="cars-gallery-grid">
                    <!-- Main Image (Left) -->
                    <div class="cars-main-image" onclick="openFullGallery(0)">
                        <img src="{{ $allImages[0] }}" alt="Main Image">
                    </div>

                    <!-- Right Grid (4 thumbnails) -->
                    <div class="cars-thumbnail-grid">
                        @for($i = 1; $i < min(5, $count); $i++)
                            <div class="cars-thumbnail-item {{ $i == 4 ? 'has-overlay' : '' }}" onclick="openFullGallery({{ $i }})">
                                <img src="{{ $allImages[$i] }}" alt="Gallery Image {{ $i }}">
                                @if($i == 4 && $count > 1)
                                    <div class="gallery-count-overlay" onclick="openFullGallery(0)">
                                        <i class="far fa-images"></i> See gallery ({{ $count }})
                                    </div>
                                @endif
                            </div>
                        @endfor
                        
                        <!-- Fill empty slots if less than 4 images -->
                        @if($count < 5)
                            @for($j = $count; $j < 5; $j++)
                                <div class="cars-thumbnail-item empty-slot">
                                    <div class="no-image-placeholder">
                                        <i class="fas fa-camera"></i>
                                    </div>
                                </div>
                            @endfor
                        @endif
                    </div>
                </div>
            @else
                <div class="no-photo-placeholder-large">
                    <i class="fas fa-camera"></i>
                    <span>No Photos Available</span>
                </div>
            @endif
        </div>

        <!-- Full Screen Gallery Modal (Hidden) -->
        <!-- Full Screen Gallery Modal -->
        <div id="fullGalleryModal" class="gallery-modal">
            <span class="close-gallery" onclick="closeFullGallery()">&times;</span>
            <div class="gallery-modal-content" style="position: relative; height: 100vh; display: flex; align-items: center; justify-content: center; background: black; border: none; width: 100%; max-width: none;">
                
                @foreach($allImages as $index => $img)
                    <div class="mySlides" style="display: none; width: 100%; height: 100%; align-items: center; justify-content: center;">
                        <div class="numbertext" style="position: absolute; top: 20px; left: 20px; color: #f2f2f2; font-size: 16px; font-weight: bold; z-index: 1001;">{{ $index + 1 }} / {{ count($allImages) }}</div>
                        <img src="{{ $img }}" style="max-width: 100%; max-height: 90vh; object-fit: contain; width: auto; height: auto;">
                    </div>
                @endforeach
                
                <a class="prev-slide" onclick="plusSlides(-1)">&#10094;</a>
                <a class="next-slide" onclick="plusSlides(1)">&#10095;</a>
            </div>
        </div>

        <!-- Title Section (Below Gallery) -->
        <div class="listing-header" style="margin-bottom: 32px;">
            <h1 class="listing-title" style="margin-bottom: 16px;">
                {{ $listing->year }} {{ $listing->make->name }} {{ $listing->vehicleModel->name }}
                @if($listing->year >= 2024)
                    <span class="badge-new">New</span>
                @endif
            </h1>
        </div>

        <div class="listing-content-grid">
            <!-- Left Column: Details -->
            <div class="main-column">
                
                <!-- Main Price & Key Info block (New) -->
                <div class="price-main-block" style="background: white; border: 1px solid #e0e0e0; border-radius: 12px; padding: 24px; margin-bottom: 32px;">
                    <div style="display: flex; align-items: baseline; gap: 12px; margin-bottom: 8px;">
                        <span style="font-size: 32px; font-weight: 800; color: #1a1a1a;">{!! \App\Helpers\Helpers::formatPrice($listing->price) !!}</span>
                        @php
                            $monthlyPrice = \App\Helpers\Helpers::convertPrice($listing->price / 60);
                        @endphp
                        @if(isset($listing->original_price) && $listing->original_price > $listing->price)
                            <span style="font-size: 14px; color: #2ecc71; font-weight: 700;">
                                <i class="fas fa-arrow-down"></i> {!! \App\Helpers\Helpers::formatPrice($listing->original_price - $listing->price) !!} price drop
                            </span>
                        @endif
                    </div>

                    <div style="font-size: 14px; text-decoration: underline; margin-bottom: 16px; color: #1a1a1a; font-weight: 500;">
                        {!! \App\Helpers\Helpers::formatPrice($monthlyPrice) !!} Est. monthly payment
                    </div>

                    <div style="font-size: 16px; font-weight: 700; margin-bottom: 20px;">
                        {{ number_format($listing->mileage) }} mi.
                    </div>

                    <div style="display: flex; gap: 8px;">
                        <span class="badge-pill" style="background: #fff0eb; color: #b04632; font-size: 12px; padding: 6px 12px; border-radius: 20px; font-weight: 700;">
                            Fair Deal
                        </span>
                         <span class="badge-pill" style="background: #f0f3f5; color: #1a1a1a; font-size: 12px; padding: 6px 12px; border-radius: 20px; font-weight: 700;">
                            High Demand
                        </span>
                    </div>
                </div>

                <!-- Basics / Characteristics -->
                <div class="detail-section" id="basics">
                    <h2 class="section-heading">Characteristics</h2>
                    <div class="basics-grid">
                        <div class="basic-item">
                            <span class="label">Exterior Color</span>
                            <span class="value">{{ $listing->color ?? 'Generic' }}</span>
                        </div>
                        <div class="basic-item">
                            <span class="label">Interior Color</span>
                            <span class="value">{{ $listing->interior_color ?? 'Black' }}</span>
                        </div>
                        <div class="basic-item">
                            <span class="label">Drivetrain</span>
                            <span class="value">{{ $listing->drivetrain ?? 'FWD' }}</span>
                        </div>
                        <div class="basic-item">
                            <span class="label">MPG</span>
                            <span class="value">25-30 estimated</span>
                        </div>
                        <div class="basic-item">
                            <span class="label">Fuel type</span>
                            <span class="value">{{ $listing->fuel_type }}</span>
                        </div>
                        <div class="basic-item">
                            <span class="label">Transmission</span>
                            <span class="value">{{ $listing->transmission }}</span>
                        </div>
                        <div class="basic-item">
                            <span class="label">Engine</span>
                            <span class="value">{{ $listing->engine_size ?? '2.0L I4' }}</span>
                        </div>
                        <div class="basic-item">
                            <span class="label">VIN</span>
                            <span class="value">{{ $listing->vin ?? 'N/A' }}</span>
                        </div>
                        <div class="basic-item">
                            <span class="label">Stock #</span>
                            <span class="value">{{ $listing->id * 1234 }}</span>
                        </div>
                        <div class="basic-item">
                            <span class="label">Mileage</span>
                            <span class="value">{{ number_format($listing->mileage) }} mi.</span>
                        </div>
                    </div>
                </div>

                <!-- Features / Additional Options -->
                @php
                    $features = is_array($listing->features) ? $listing->features : (is_string($listing->features) ? json_decode($listing->features, true) : []);
                @endphp
                @if($features && count($features) > 0)
                <div class="detail-section">
                    <h2 class="section-heading">Additional options</h2>
                    <ul class="features-list">
                        @foreach($features as $feature)
                        <li><i class="fas fa-check"></i> {{ $feature }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <!-- Seller's Description -->
                <div class="detail-section">
                    <h2 class="section-heading">Description</h2>
                    <div class="description-content">
                        {!! $listing->description !!}
                    </div>
                </div>

                <!-- Extended Media (Video & 360) -->
                @if($listing->video_url || $listing->v360_url)
                <div class="detail-section" id="extended-media">
                    <h2 class="section-heading">Extended Media</h2>
                    
                    @if($listing->video_url)
                        <div class="video-container mb-32">
                            <h3 style="font-size: 18px; font-weight: 700; margin-bottom: 16px;"><i class="fab fa-youtube text-danger"></i> Video Walkaround</h3>
                            @php
                                $video_id = '';
                                if (preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/|youtube\.com/live/)([^"&?/ ]{11})%i', $listing->video_url, $match)) {
                                    $video_id = $match[1];
                                }
                                $vimeo_id = '';
                                if (preg_match('%(?:vimeo\.com/|player\.vimeo\.com/video/)([0-9]+)%i', $listing->video_url, $match)) {
                                    $vimeo_id = $match[1];
                                }
                            @endphp

                            @if($video_id)
                                <div class="ratio ratio-16x9" style="border-radius: 12px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.1);">
                                    <iframe src="https://www.youtube.com/embed/{{ $video_id }}" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                                </div>
                            @elseif($vimeo_id)
                                <div class="ratio ratio-16x9" style="border-radius: 12px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.1);">
                                    <iframe src="https://player.vimeo.com/video/{{ $vimeo_id }}" title="Vimeo video player" frameborder="0" allow="autoplay; fullscreen; picture-in-picture" allowfullscreen></iframe>
                                </div>
                            @else
                                <div class="alert alert-light" style="border: 1px dashed #ddd; text-align: center; border-radius: 12px; padding: 20px;">
                                    <a href="{{ $listing->video_url }}" target="_blank" class="text-primary fw-bold">
                                        <i class="fas fa-external-link-alt"></i> View External Video
                                    </a>
                                </div>
                            @endif
                        </div>
                    @endif

                    @if($listing->v360_url)
                        <div class="v360-container">
                            <h3 style="font-size: 18px; font-weight: 700; margin-bottom: 16px;"><i class="fas fa-3d-rotate text-info"></i> 360° Virtual Tour</h3>
                            <div class="ratio ratio-16x9" style="border-radius: 12px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.1); background: #000;">
                                <iframe src="{{ $listing->v360_url }}" allowfullscreen frameborder="0" scrolling="no"></iframe>
                            </div>
                            <div class="mt-12 text-center">
                                <a href="{{ $listing->v360_url }}" target="_blank" class="btn btn-sm btn-outline-primary mt-2">
                                    <i class="fas fa-expand me-1"></i> Open in Full Screen
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
                @endif

            </div>

            <!-- Right Column: Sidebar (Sticky) -->
            <div class="sidebar-column">
                <div class="sidebar-card contact-card">
                    <!-- Price moved to main column -->

                    <!-- Reservation Button / Status -->
                    @if($listing->isReserved())
                        <div class="reservation-status-box" style="background: #fff3cd; border: 1px solid #ffeeba; border-radius: 8px; padding: 16px; margin-bottom: 24px; text-align: center;">
                            <div style="font-size: 24px; color: #856404; margin-bottom: 8px;"><i class="fas fa-lock"></i></div>
                            <h3 style="color: #856404; font-size: 18px; font-weight: 700; margin-bottom: 4px;">Reserved</h3>
                            <p style="color: #856404; font-size: 14px; margin: 0;">Until {{ $listing->reserved_until->format('M d, H:i') }}</p>
                        </div>
                    @else
                        @auth
                            @if($listing->user_id !== auth()->id())
                                <a href="{{ route('reservations.create', $listing->slug) }}" class="action-row" style="background: #10b981; color: white; border: none; margin-bottom: 16px; text-decoration: none;">
                                    <span class="icon" style="background: rgba(255,255,255,0.2); color: white;"><i class="fas fa-calendar-check"></i></span>
                                    <span class="text" style="color: white;">Reserve Now</span>
                                </a>
                            @endif
                        @else
                            <a href="{{ route('login') }}" class="action-row" style="background: #10b981; color: white; border: none; margin-bottom: 16px; text-decoration: none;">
                                <span class="icon" style="background: rgba(255,255,255,0.2); color: white;"><i class="fas fa-calendar-check"></i></span>
                                <span class="text" style="color: white;">Login to Reserve</span>
                            </a>
                        @endauth
                    @endif

                    <div class="contact-actions-list">
                        <button class="action-row" id="reveal-phone-btn">
                            <span class="icon"><i class="fas fa-phone"></i></span>
                            <span class="text" id="phone-text">Show phone number</span>
                        </button>
                        <button class="action-row" onclick="openMessageModal()">
                            <span class="icon"><i class="fas fa-comment-alt"></i></span>
                            <span class="text">Write a message</span>
                        </button>
                    </div>
                    
                    <hr class="sidebar-divider">

                    <!-- Seller Info -->
                    <div class="seller-info" style="display: flex; gap: 16px; align-items: center; padding: 4px;">
                        <div class="seller-avatar-circular" style="width: 50px; height: 50px; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-size: 18px; font-weight: 700; flex-shrink: 0; box-shadow: 0 4px 10px rgba(96, 65, 224, 0.15); overflow: hidden;">
                            @if($listing->user->profile_photo_path)
                                <img src="{{ asset('storage/' . $listing->user->profile_photo_path) }}" alt="{{ $listing->user->name }}" style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">
                            @else
                                <div style="width: 100%; height: 100%; background: linear-gradient(135deg, #6041E0 0%, #a29bfe 100%); display: flex; align-items: center; justify-content: center;">
                                    {{ strtoupper(substr($listing->user->name, 0, 1)) }}
                                </div>
                            @endif
                        </div>
                        <div class="seller-details-premium" style="flex: 1;">
                            <div style="display: flex; align-items: center; gap: 6px; margin-bottom: 0px;">
                                <span class="seller-name" style="font-size: 14px; font-weight: 500; color: #1a1a1a; letter-spacing: -0.2px;">{{ $listing->user->name }}</span>
                            </div>
                            
                            <div class="seller-subtitle" style="font-size: 12px; color: #57606f; margin-bottom: 4px;">
                                Salesperson
                            </div>

                            <div class="seller-rating-premium" style="display: flex; align-items: center; gap: 4px; margin-top: 2px;">
                                @php
                                    $rating = $listing->user->average_rating;
                                    $count = $listing->user->reviews_count;
                                @endphp
                                <span style="font-size: 13px; font-weight: 600; color: #1a1a1a;">{{ number_format($rating, 1) }}</span>
                                <div class="stars-outer" style="color: #a29bfe; font-size: 12px; display: flex; gap: 1px;">
                                    @for($i = 1; $i <= 5; $i++)
                                        @if($i <= floor($rating))
                                            <i class="fas fa-star"></i>
                                        @elseif($i == ceil($rating) && $rating - floor($rating) >= 0.5)
                                            <i class="fas fa-star-half-alt"></i>
                                        @else
                                            <i class="fas fa-star" style="opacity: 0.3;"></i>
                                        @endif
                                    @endfor
                                </div>
                                <span style="font-size: 12px; color: #57606f;">({{ $count }})</span>
                            </div>
                            @auth
                                <button class="btn-leave-review" onclick="openReviewModal()" style="margin-top: 6px; padding: 4px 10px; background: transparent; color: #6041E0; border: 1px solid #6041E0; border-radius: 4px; font-size: 11px; font-weight: 600; cursor: pointer;">Leave a Review</button>
                            @endauth
                        </div>
                    </div>
                    
                    <style>
                        .btn-leave-review:hover {
                            background: #f8f9fa;
                            border-color: #6041E0;
                            color: #6041E0;
                        }
                    </style>
                </div>

                <!-- Safety Tips (Classic Classifieds Style) -->
                <div class="sidebar-card safety-card">
                    <h3>Safety tips</h3>
                    <ul>
                        <li>Don't send money remotely</li>
                        <li>Verify the vehicle in person</li>
                        <li>Check the documents carefully</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Similar Cars Section (Restored & Styled) -->
        @if(isset($similarListings) && $similarListings->count() > 0)
        <div class="similar-section" style="margin-top: 60px; margin-bottom: 40px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
                <h2 style="font-size: 24px; font-weight: 700; color: #1a1a1a; margin: 0;">Similar cars at this dealership</h2>
                <a href="{{ route('listings.search') }}" style="color: #6041E0; font-weight: 700; text-decoration: none;">See all similar cars</a>
            </div>
            
            <div class="similar-grid-scroll">
                @foreach($similarListings as $similar)
                <a href="{{ route('listings.show', $similar->slug) }}" class="similar-card-item">
                    <div class="similar-img-box">
                        @if($similar->main_image)
                            <img src="{{ $similar->main_image }}" alt="{{ $similar->title }}">
                        @else
                            <div class="no-photo-small">
                                <i class="fas fa-camera"></i>
                            </div>
                        @endif
                        <div class="similar-badge-overlay">Great Deal</div>
                    </div>
                    <div class="similar-details">
                        <div class="sim-price">{!! \App\Helpers\Helpers::formatPrice($similar->price) !!}</div>
                        <div class="sim-title">{{ $similar->year }} {{ $similar->make->name }} {{ $similar->vehicleModel->name }}</div>
                        <div class="sim-meta">{{ number_format($similar->mileage) }} mi.</div>
                    </div>
                </a>
                @endforeach
            </div>
        </div>
        @endif
    </div>
</div>

<!-- Message Modal -->
<!-- Message Modal (azalcars Style) -->
<div id="messageModal" class="modal-overlay">
    <div class="modal-box-cars-style">
        <button class="close-modal-cars" onclick="closeMessageModal()">&times;</button>
        
        <!-- Header -->
        <div class="cars-modal-header">
            <h2 class="cars-modal-title">Contact seller</h2>
            <div class="cars-modal-phone">(877) 396-8035</div>
        </div>

        <!-- Dealer Info -->
        <div class="cars-modal-dealer-info">
            <div class="dealer-name-row">
                <span class="dealer-name">{{ $listing->user->name }}</span>
                <div class="dealer-rating-badge">
                    <i class="fas fa-star"></i> {{ number_format($listing->user->average_rating, 1) }}
                </div>
            </div>
            <div class="dealer-location">{{ $listing->location ?? 'Location N/A' }}</div>
        </div>

        <form action="{{ route('messages.store') }}" method="POST" class="cars-modal-form">
            @csrf
            <input type="hidden" name="listing_id" value="{{ $listing->id }}">
            
            <!-- Name Row -->
            <div class="cars-form-row">
                <input type="text" name="first_name" class="cars-input" placeholder="First name" required>
                <input type="text" name="last_name" class="cars-input" placeholder="Last name" required>
            </div>

            <!-- Email -->
            <input type="email" name="email" class="cars-input full-width" placeholder="Email" required>

            <!-- Phone -->
            <input type="tel" name="phone" class="cars-input full-width" placeholder="Phone (optional)">

            <!-- Message -->
            <div class="cars-input-wrapper-msg">
                <label class="cars-msg-label">Message</label>
                <textarea name="message" class="cars-input-textarea">I'd like to know if the {{ $listing->condition ?? 'Used' }} {{ $listing->year }} {{ $listing->make->name }} {{ $listing->vehicleModel->name }} you have listed on azalcars for {!! strip_tags(\App\Helpers\Helpers::formatPrice($listing->price)) !!} is still available.</textarea>
            </div>

            <!-- Submit -->
            <div class="cars-form-footer">
                <p class="chars-remaining">1291 characters remaining.</p>
                <button type="submit" class="btn-check-availability">Check availability</button>
                <p class="legal-text">
                    By clicking here, you authorize azalcars and its sellers/partners to contact you by text/calls which may include marketing and be by autodialer. Calls may be prerecorded. You also agree to our <a href="#">Privacy Notice</a>. Consent is not required to purchase goods/services.
                </p>
            </div>
        </form>
    </div>
</div>

<!-- Review Modal -->
<div id="reviewModal" class="modal-overlay">
    <div class="modal-box-cars-style">
        <button class="close-modal-cars" onclick="closeReviewModal()">&times;</button>
        
        <div class="cars-modal-header">
            <h2 class="cars-modal-title">Rate {{ $listing->user->name }}</h2>
        </div>

        @if(session('success'))
            <div class="alert alert-success mb-3">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger mb-3">{{ session('error') }}</div>
        @endif

        <form action="{{ route('reviews.store') }}" method="POST" class="cars-modal-form">
            @csrf
            <input type="hidden" name="seller_id" value="{{ $listing->user_id }}">
            <input type="hidden" name="listing_id" value="{{ $listing->id }}">
            
            <!-- Star Rating -->
            <div class="mb-4">
                <label class="form-label fw-bold">Your Rating</label>
                <div class="star-rating-input">
                    <input type="radio" name="rating" value="5" id="star5" required>
                    <label for="star5" title="5 stars">★</label>
                    <input type="radio" name="rating" value="4" id="star4">
                    <label for="star4" title="4 stars">★</label>
                    <input type="radio" name="rating" value="3" id="star3">
                    <label for="star3" title="3 stars">★</label>
                    <input type="radio" name="rating" value="2" id="star2">
                    <label for="star2" title="2 stars">★</label>
                    <input type="radio" name="rating" value="1" id="star1">
                    <label for="star1" title="1 star">★</label>
                </div>
            </div>

            <!-- Comment -->
            <div class="mb-4">
                <label class="form-label fw-bold">Your Review (Optional)</label>
                <textarea name="comment" class="cars-input-textarea" rows="4" placeholder="Share your experience with this seller..."></textarea>
            </div>

            <button type="submit" class="btn-check-availability">Submit Review</button>
        </form>
    </div>
</div>

<style>
    /* Sticky Header */
    .sticky-listing-header {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        background: white;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        z-index: 1000;
        transform: translateY(-100%);
        transition: transform 0.3s ease;
    }
    .sticky-listing-header.visible {
        transform: translateY(0);
    }
    .sticky-content {
        max-width: 1200px;
        margin: 0 auto;
        padding: 12px 24px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .sticky-info .sticky-title {
        font-weight: 700;
        font-size: 16px;
        color: #1a1a1a;
    }
    .sticky-info .sticky-price {
        font-size: 14px;
        font-weight: 600;
        color: #555;
    }
    .btn-sticky-action {
        background: #6041E0;
        color: white;
        border: none;
        padding: 10px 24px;
        border-radius: 24px;
        font-weight: 700;
        cursor: pointer;
        font-size: 14px;
    }
    .btn-sticky-action:hover {
        background: #4c30c4;
    }

    /* Extended Media */
    .ratio {
        position: relative;
        width: 100%;
    }
    .ratio::before {
        display: block;
        padding-top: var(--bs-aspect-ratio);
        content: "";
    }
    .ratio > * {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
    }
    .ratio-16x9 {
        --bs-aspect-ratio: 56.25%;
    }
    .mb-32 {
        margin-bottom: 32px;
    }
    .mt-12 {
        margin-top: 12px;
    }
    
    /* ========== PAGE LAYOUT ========== */
    .listing-detail-page {
        background-color: #f9f9f9;
        min-height: 100vh;
        font-family: 'Apercu Pro', 'DM Sans', sans-serif;
        color: #1a1a1a;
        padding-top: 0; /* Sticky fix */
    }

    /* Breadcrumbs */
    .breadcrumbs {
        display: flex;
        gap: 8px;
        font-size: 14px;
        color: #555;
        margin-bottom: 24px;
    }
    .breadcrumbs a {
        text-decoration: none;
        color: #555;
    }
    .breadcrumbs a:hover {
        text-decoration: underline;
    }
    .breadcrumbs .current {
        color: #1a1a1a;
        font-weight: 500;
    }

    /* Header */
    .listing-header {
        margin-bottom: 24px;
    }
    .listing-title {
        font-size: 32px;
        font-weight: 800;
        margin-bottom: 8px;
        letter-spacing: -0.5px;
    }
    .badge-new {
        background: #2ecc71;
        color: white;
        font-size: 12px;
        padding: 4px 10px;
        border-radius: 4px;
        vertical-align: middle;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .listing-subtitle {
        font-size: 16px;
        color: #555;
        display: flex;
        gap: 8px;
    }

    /* Grid Layout */
    .listing-content-grid {
        display: grid;
        grid-template-columns: 1fr 360px;
        gap: 32px;
    }

    /* ========== MAIN COLUMN ========== */
    .main-column {
        display: flex;
        flex-direction: column;
        gap: 40px;
    }

    /* Gallery */
    .gallery-section {
        background: white;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }
    .main-image-container {
        height: 500px;
        background: #f8f9fa;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .main-image-container img {
        max-width: 100%;
        max-height: 100%;
        object-fit: contain;
    }
    .no-photo-placeholder {
        color: #666;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 8px;
    }
    .thumbnails-scroll {
        display: flex;
        gap: 8px;
        padding: 12px;
        overflow-x: auto;
        background: #1a1a1a;
    }
    .thumb-item {
        width: 100px;
        height: 70px;
        flex-shrink: 0;
        cursor: pointer;
        border: 2px solid transparent;
        border-radius: 4px;
        overflow: hidden;
        opacity: 0.7;
        transition: all 0.2s;
    }
    .thumb-item.active, .thumb-item:hover {
        border-color: #6041E0;
        opacity: 1;
    }
    .thumb-item img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    /* Detail Sections */
    .detail-section {
        background: white;
        border-radius: 12px;
        padding: 32px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
    }
    .section-heading {
        font-size: 24px;
        font-weight: 700;
        margin-bottom: 24px;
        border-bottom: 1px solid #eee;
        padding-bottom: 12px;
    }

    /* Basics Grid */
    .basics-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 16px 40px;
    }
    .basic-item {
        display: flex;
        justify-content: space-between;
        border-bottom: 1px dotted #ddd;
        padding-bottom: 8px;
    }
    .basic-item .label {
        color: #666;
        font-size: 15px;
    }
    .basic-item .value {
        font-weight: 600;
        color: #1a1a1a;
    }

    /* Features List */
    .features-list {
        list-style: none;
        padding: 0;
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 12px;
    }
    .features-list li {
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 15px;
        color: #333;
    }
    .features-list li i {
        color: #6041E0;
        font-size: 12px;
    }

    /* Description */
    .description-content {
        line-height: 1.8;
        font-size: 16px;
        color: #444;
        white-space: pre-line;
    }

    /* ========== SIDEBAR ========== */
    .sidebar-column {
        display: flex;
        flex-direction: column;
        gap: 24px;
    }
    
    .sidebar-card {
        background: white;
        border-radius: 12px;
        padding: 24px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08); /* Stronger shadow for floating feel */
        border: 1px solid #eee;
    }
    .contact-card {
        position: sticky;
        top: 24px;
    }

    .listing-price {
        font-size: 36px;
        font-weight: 800;
        color: #1a1a1a;
        margin-bottom: 4px;
        letter-spacing: -1px;
    }
    .price-label {
        color: #27ae60;
        font-weight: 600;
        font-size: 14px;
        margin-bottom: 24px;
    }
    
    .btn-primary-action {
        width: 100%;
        background: #6041E0; /* azalcars purple */
        color: white;
        border: none;
        padding: 16px;
        border-radius: 30px; /* Pill shape */
        font-size: 18px;
        font-weight: 700;
        cursor: pointer;
        transition: background 0.2s;
        margin-bottom: 16px;
    }
    .btn-primary-action:hover {
        background: #4c30c4;
    }

    .contact-actions-list {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }
    .action-row {
        background: none;
        border: 1px solid #ddd;
        border-radius: 8px;
        padding: 12px;
        display: flex;
        align-items: center;
        gap: 12px;
        width: 100%;
        cursor: pointer;
        transition: all 0.2s;
        text-align: left;
    }
    .action-row:hover {
        background: #f8f9fa;
        border-color: #bbb;
    }
    .action-row .icon {
        width: 32px;
        height: 32px;
        background: #f0f0f0;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #1a1a1a;
    }
    .action-row .text {
        font-weight: 600;
        color: #1a1a1a;
        font-size: 15px;
    }

    .sidebar-divider {
        border: none;
        border-top: 1px solid #eee;
        margin: 24px 0;
    }

    .seller-info {
        display: flex;
        gap: 16px;
        align-items: center;
    }
    .seller-logo {
        width: 48px;
        height: 48px;
        background: #6041E0;
        color: white;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
    }
    .seller-name {
        font-weight: 700;
        color: #1a1a1a;
        margin-bottom: 4px;
    }
    .seller-rating {
        color: #f39c12;
        font-size: 12px;
    }

    /* Safety Card */
    .safety-card h3 {
        font-size: 16px;
        margin-bottom: 12px;
    }
    .safety-card ul {
        padding-left: 20px;
        color: #666;
        font-size: 14px;
        line-height: 1.6;
    }

    /* Modal */
    .modal-overlay {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.6);
        z-index: 1000;
        justify-content: center;
        align-items: center;
        backdrop-filter: blur(4px);
    }
    .modal-box {
        background: white;
        border-radius: 16px;
        padding: 32px;
        width: 100%;
        max-width: 500px;
        position: relative;
        animation: slideUp 0.3s ease;
    }
    @@keyframes slideUp {
        from { transform: translateY(20px); opacity: 0; }
        to { transform: translateY(0); opacity: 1; }
    }
    .close-modal {
        position: absolute;
        top: 20px;
        right: 20px;
        border: none;
        background: none;
        font-size: 28px;
        cursor: pointer;
        color: #999;
    }
    .modal-header {
        text-align: center;
        margin-bottom: 24px;
    }
    .form-group {
        margin-bottom: 16px;
    }
    .form-group label {
        display: block;
        margin-bottom: 8px;
        font-weight: 600;
        font-size: 14px;
    }
    .form-input {
        width: 100%;
        padding: 12px;
        border: 1px solid #ddd;
        border-radius: 8px;
        font-family: inherit;
    }
    .btn-submit {
        width: 100%;
        padding: 14px;
        background: #6041E0;
        color: white;
        border: none;
        border-radius: 30px;
        font-weight: 700;
        cursor: pointer;
        font-size: 16px;
    }

    /* Responsive */
    @media (max-width: 1024px) {
        .listing-content-grid {
            grid-template-columns: 1fr;
            gap: 24px;
        }
        .sidebar-card.contact-card {
            position: static;
        }
    }

    @media (max-width: 768px) {
        .basics-grid, .features-list {
            grid-template-columns: 1fr;
        }
        .listing-title {
            font-size: 24px;
        }
        .price-main-block {
            padding: 20px;
        }
        .thumbnails-grid {
            grid-template-columns: repeat(3, 1fr);
            gap: 8px;
        }
    }

    @media (max-width: 480px) {
        .price-main-block div:first-child {
            flex-direction: column;
            gap: 4px !important;
        }
    }
    
    /* azalcars Contact Modal Styles */
    .modal-box-cars-style {
        background: white;
        padding: 40px;
        border-radius: 12px;
        max-width: 600px;
        width: 100%;
        position: relative;
        box-shadow: 0 4px 20px rgba(0,0,0,0.15);
    }
    .close-modal-cars {
        position: absolute;
        top: 20px;
        right: 20px;
        font-size: 24px;
        cursor: pointer;
        background: none;
        border: none;
        color: #1a1a1a;
    }
    .cars-modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }
    .cars-modal-title {
        font-size: 28px;
        font-weight: 800;
        color: #1a1a1a;
    }
    .cars-modal-phone {
        font-size: 18px;
        font-weight: 700;
        color: #1a1a1a;
    }
    .cars-modal-dealer-info {
        margin-bottom: 24px;
    }
    .dealer-name-row {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 4px;
    }
    .dealer-name {
        font-size: 16px;
        color: #1a1a1a;
    }
    .dealer-rating-badge {
        font-size: 16px;
        color: #1a1a1a;
        font-weight: 400;
    }
    .dealer-rating-badge i {
        color: #8c52ff; /* Star color */
    }
    .dealer-location {
        font-size: 14px;
        color: #555;
    }
    .cars-form-row {
        display: flex;
        gap: 16px;
        margin-bottom: 16px;
    }
    .cars-input {
        flex: 1;
        background: #f0f3f5;
        border: none;
        border-radius: 8px;
        padding: 14px 16px;
        font-size: 16px;
        font-family: inherit;
        color: #1a1a1a;
    }
    .cars-input.full-width {
        width: 100%;
        margin-bottom: 16px;
    }
    .cars-input::placeholder {
        color: #666;
    }
    .cars-input:focus {
        outline: 2px solid #6041E0;
        background: #fff;
    }
    .cars-input-wrapper-msg {
        background: #f0f3f5;
        border-radius: 8px;
        padding: 16px;
        margin-bottom: 8px;
    }
    .cars-msg-label {
        display: block;
        font-size: 12px;
        color: #555;
        margin-bottom: 4px;
        font-weight: 600;
    }
    .cars-input-textarea {
        width: 100%;
        background: transparent;
        border: none;
        font-family: inherit;
        font-size: 16px;
        color: #1a1a1a;
        resize: none;
        height: 80px;
        line-height: 1.4;
    }
    .cars-input-textarea:focus {
        outline: none;
    }
    .chars-remaining {
        font-size: 12px;
        color: #666;
        margin-bottom: 16px;
    }
    .btn-check-availability {
        width: 100%;
        background: #8c52ff; /* Purple */
        color: white;
        border: none;
        padding: 16px;
        border-radius: 30px;
        font-size: 18px;
        font-weight: 700;
        cursor: pointer;
        margin-bottom: 16px;
        transition: background 0.2s;
    }
    .btn-check-availability:hover {
        background: #733ae0;
    }
    .legal-text {
        font-size: 11px;
        color: #666;
        line-height: 1.4;
    }
    .legal-text a {
        color: #666;
        text-decoration: underline;
    }


    @media (max-width: 600px) {
        .cars-modal-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 8px;
        }
        .modal-box-cars-style {
            padding: 24px;
        }
    }
    
    /* azalcars Gallery Section */
    .cars-gallery-section {
        margin-bottom: 32px;
    }
    
    .cars-gallery-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 8px;
        height: 450px;
    }
    
    .cars-main-image {
        height: 100%;
        cursor: pointer;
        position: relative;
        overflow: hidden;
        border-radius: 8px 0 0 8px;
        background: #f5f5f5;
    }
    
    .cars-main-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.3s ease;
    }
    
    .cars-main-image:hover img {
        transform: scale(1.02);
    }
    
    .cars-thumbnail-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        grid-template-rows: 1fr 1fr;
        gap: 8px;
        height: 100%;
    }
    
    .cars-thumbnail-item {
        position: relative;
        cursor: pointer;
        overflow: hidden;
        background: #f5f5f5;
        height: 100%;
    }
    
    .cars-thumbnail-item img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.3s ease;
    }
    
    .cars-thumbnail-item:hover img {
        transform: scale(1.05);
    }
    
    /* Border radius for thumbnails */
    .cars-thumbnail-item:nth-child(1) {
        border-radius: 0 8px 0 0; /* Top right */
    }
    
    .cars-thumbnail-item:nth-child(4),
    .cars-thumbnail-item.has-overlay {
        border-radius: 0 0 8px 0; /* Bottom right */
    }
    
    /* Gallery Count Overlay (on bottom-right thumbnail) */
    .gallery-count-overlay {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        top: 0;
        background: rgba(0, 0, 0, 0.7);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 16px;
        font-weight: 700;
        gap: 8px;
        cursor: pointer;
        transition: background 0.3s ease;
    }
    
    .gallery-count-overlay:hover {
        background: rgba(0, 0, 0, 0.8);
    }
    
    .gallery-count-overlay i {
        font-size: 20px;
    }
    
    /* Empty slot styling */
    .cars-thumbnail-item.empty-slot {
        background: #e5e5e5;
        cursor: default;
    }
    
    .no-image-placeholder {
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #999;
        font-size: 32px;
    }
    
    /* Responsive */
    @media (max-width: 768px) {
        .cars-gallery-grid {
            grid-template-columns: 1fr;
            height: auto;
        }
        
        .cars-main-image {
            height: 300px;
            border-radius: 8px 8px 0 0;
        }
        
        .cars-thumbnail-grid {
            display: none; /* Hide thumbnails on mobile */
        }
    }

    .show-all-btn {
        position: absolute;
        bottom: 20px;
        right: 20px;
        background: white;
        color: #1a1a1a;
        border: 1px solid #1a1a1a;
        padding: 8px 16px;
        border-radius: 8px;
        font-weight: 700;
        font-size: 14px;
        display: flex;
        align-items: center;
        gap: 8px;
        cursor: pointer;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        transition: 0.2s;
        z-index: 10;
    }
    .show-all-btn:hover {
        background: #f1f1f1;
        transform: scale(1.02);
    }

    /* Modal Styles */
    .gallery-modal {
        display: none;
        position: fixed;
        z-index: 9999;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        overflow: auto;
        background-color: rgba(0,0,0,0.95);
        padding-top: 50px;
    }
    .gallery-modal-content {
        margin: auto;
        display: block;
        width: 80%;
        max-width: 1000px;
        display: flex;
        flex-direction: column;
        gap: 20px;
    }
    .gallery-modal-img {
        width: 100%;
        height: auto;
        margin-bottom: 20px;
    }
    .close-gallery {
        position: absolute;
        top: 15px;
        right: 35px;
        color: #f1f1f1;
        font-size: 40px;
        font-weight: bold;
        transition: 0.3s;
        cursor: pointer;
    }
    .close-gallery:hover,
    .close-gallery:focus {
        color: #bbb;
        text-decoration: none;
        cursor: pointer;
    }

    .no-photo-placeholder-large {
        height: 400px;
        background: #eee;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-align: center;
        border-radius: 12px;
        color: #888;
        font-size: 18px;
        font-weight: 500;
    }
    .no-photo-placeholder-large i {
        font-size: 48px;
        margin-bottom: 12px;
        color: #aaa;
    }

    @media (max-width: 768px) {
        .airbnb-grid {
            grid-template-columns: 1fr;
            height: 300px;
        }
        .airbnb-sub-grid {
            display: none; /* Hide subgrid on mobile initially or stack */
        }
        .airbnb-main-img {
            border-radius: 12px;
        }
    }
    
    /* Similar Cars Section */
    .similar-grid-scroll {
        display: flex;
        overflow-x: auto;
        gap: 20px;
        padding-bottom: 20px;
        scroll-behavior: smooth;
    }
    .similar-grid-scroll::-webkit-scrollbar {
        height: 8px;
    }
    .similar-grid-scroll::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 4px;
    }
    .similar-grid-scroll::-webkit-scrollbar-thumb {
        background: #ccc;
        border-radius: 4px;
    }
    .similar-grid-scroll::-webkit-scrollbar-thumb:hover {
        background: #aaa;
    }

    .similar-card-item {
        flex: 0 0 240px;
        text-decoration: none;
        color: inherit;
        background: white;
        border: 1px solid #eee;
        border-radius: 8px;
        overflow: hidden;
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .similar-card-item:hover {
        box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    }
    .similar-img-box {
        height: 150px;
        background: #f4f4f4;
        position: relative;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .similar-img-box img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .no-photo-small {
        color: #ccc;
        font-size: 24px;
    }
    .similar-badge-overlay {
        position: absolute;
        top: 8px;
        left: 8px;
        background: #27ae60;
        color: white;
        font-size: 10px;
        font-weight: 700;
        padding: 3px 6px;
        border-radius: 4px;
        text-transform: uppercase;
    }
    .similar-details {
        padding: 12px;
    }
    .sim-price {
        font-size: 20px;
        font-weight: 800;
        color: #1a1a1a;
        margin-bottom: 4px;
    }
    .sim-title {
        font-size: 14px;
        font-weight: 600;
        color: #333;
        line-height: 1.4;
        height: 40px;
        overflow: hidden;
        margin-bottom: 4px;
    }
    .sim-meta {
        font-size: 13px;
        color: #666;
    }
    /* Modal Navigation Buttons */
    .prev-slide, .next-slide {
        cursor: pointer;
        position: absolute;
        top: 50%;
        width: auto;
        padding: 16px;
        margin-top: -50px;
        color: white;
        font-weight: bold;
        font-size: 40px;
        transition: 0.6s ease;
        border-radius: 0 3px 3px 0;
        user-select: none;
        -webkit-user-select: none;
        z-index: 1002;
        text-decoration: none;
    }
    .next-slide {
        right: 20px;
        border-radius: 3px 0 0 3px;
    }
    .prev-slide {
        left: 20px;
        border-radius: 3px 0 0 3px;
    }
    .prev-slide:hover, .next-slide:hover {
        background-color: rgba(0,0,0,0.8);
        color: white;
        text-decoration: none;
    }
    /* Hide scrollbar for modal content */
    .gallery-modal-content::-webkit-scrollbar {
        display: none;
    }

    /* Star Rating Input */
    .star-rating-input {
        display: flex;
        flex-direction: row-reverse;
        justify-content: flex-end;
        gap: 8px;
        font-size: 40px;
    }
    
    .star-rating-input input[type="radio"] {
        display: none;
    }
    
    .star-rating-input label {
        cursor: pointer;
        color: #ddd;
        transition: color 0.2s;
    }
    
    .star-rating-input input[type="radio"]:checked ~ label,
    .star-rating-input label:hover,
    .star-rating-input label:hover ~ label {
        color: #ffc107;
    }
</style>

<script>
    // Sticky Header Scroll Logic
    window.addEventListener('scroll', function() {
        const header = document.getElementById('sticky-header');
        // Show after scrolling past 400px
        if (window.scrollY > 400) {
            header.classList.add('visible');
        } else {
            header.classList.remove('visible');
        }
    });

    function updateMainImage(src, el) {
        document.getElementById('main-display-image').src = src;
        document.querySelectorAll('.thumb-item').forEach(t => t.classList.remove('active'));
        el.classList.add('active');
    }

    function openMessageModal() {
        document.getElementById('messageModal').style.display = 'flex';
    }

    function closeMessageModal() {
        document.getElementById('messageModal').style.display = 'none';
    }

    function openReviewModal() {
        document.getElementById('reviewModal').style.display = 'flex';
    }

    function closeReviewModal() {
        document.getElementById('reviewModal').style.display = 'none';
    }

    // Consolidated Window Click Handler
    window.onclick = function(event) {
        const msgModal = document.getElementById('messageModal');
        const reviewModal = document.getElementById('reviewModal');
        const galleryModal = document.getElementById('fullGalleryModal');
        
        if (event.target == msgModal) {
            closeMessageModal();
        }
        if (event.target == reviewModal) {
            closeReviewModal();
        }
        if (event.target == galleryModal) {
            closeFullGallery();
        }
    }

    // Lead Tracking & Interaction Logic
    document.addEventListener('DOMContentLoaded', function() {
        const listingId = {{ $listing->id }};
        
        // 1. Track View Event (AJAX)
        fetch("{{ route('listing.track') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ listing_id: listingId, type: 'view' })
        });

        // 2. Phone Reveal (Secure)
        const phoneBtn = document.getElementById('reveal-phone-btn');
        if (phoneBtn) {
            phoneBtn.addEventListener('click', function() {
                const phoneText = document.getElementById('phone-text');
                phoneText.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Loading...';
                
                fetch(`/listing/${listingId}/reveal-phone`)
                    .then(response => response.json())
                    .then(data => {
                        phoneText.innerHTML = `<span style="font-weight:800; color:#27ae60;">${data.phone}</span>`;
                        this.style.borderColor = '#27ae60';
                        this.style.background = 'rgba(39, 174, 96, 0.1)';
                        // Disable button after reveal
                        this.style.pointerEvents = 'none';
                    })
                    .catch(err => {
                        phoneText.innerText = 'Error showing number';
                    });
            });
        }
    });


    // Gallery Modal Logic
    var slideIndex = 1;

    function openFullGallery(n) {
        document.getElementById("fullGalleryModal").style.display = "block";
        showSlides(slideIndex = n + 1);
        document.body.style.overflow = 'hidden'; // Disable page scroll
    }

    function closeFullGallery() {
        document.getElementById("fullGalleryModal").style.display = "none";
        document.body.style.overflow = 'auto'; // Enable page scroll
    }

    function plusSlides(n) {
        showSlides(slideIndex += n);
    }

    function showSlides(n) {
        var i;
        var slides = document.getElementsByClassName("mySlides");
        if (slides.length === 0) return;
        
        if (n > slides.length) {slideIndex = 1}
        if (n < 1) {slideIndex = slides.length}
        
        for (i = 0; i < slides.length; i++) {
            slides[i].style.display = "none";
        }
        slides[slideIndex-1].style.display = "flex";
    }

    // Keyboard Navigation
    document.addEventListener('keydown', function(event) {
        if(document.getElementById("fullGalleryModal").style.display === "block") {
            if (event.key === "ArrowLeft") {
                plusSlides(-1);
            } else if (event.key === "ArrowRight") {
                plusSlides(1);
            } else if (event.key === "Escape") {
                closeFullGallery();
            }
        }
    });
</script>

@endsection
