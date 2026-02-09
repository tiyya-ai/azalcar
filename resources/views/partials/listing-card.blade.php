<div class="listing-card">
    <div class="listing-content-wrapper">
        <!-- Image Section with Carousel UI -->
        <div class="listing-image-container" 
             data-images="{{ json_encode(array_merge([$listing->main_image], $listing->images ?? [])) }}"
             data-index="0">
            <a href="{{ route('listings.show', $listing->slug) }}" class="image-slide-link">
                @if($listing->main_image)
                    <img src="{{ $listing->main_image }}" alt="{{ $listing->title }}" class="current-slide">
                @else
                    <div class="no-image-placeholder">
                        <i class="fas fa-camera"></i>
                        <span>No Photos</span>
                    </div>
                @endif
                
                @if($listing->images && is_array($listing->images) && count($listing->images) > 0)
                    <div class="image-count-badge">1/{{ count($listing->images) + 1 }}</div>
                @endif
            </a>

            <!-- Carousel Arrows -->
            <button class="carousel-arrow prev"><i class="fas fa-chevron-left"></i></button>
            <button class="carousel-arrow next"><i class="fas fa-chevron-right"></i></button>

            @if($listing->is_featured)
                <span class="badge-sponsored">Sponsored</span>
            @endif
        </div>
        
        <!-- Content Section -->
        <div class="listing-info">
            <div class="listing-top-row">
                <div class="listing-condition-label">{{ $listing->condition == 'new' ? 'New' : 'Used' }}</div>
                 @php $isFavorited = auth()->check() && $listing->isFavoritedBy(auth()->user()); @endphp
                 <button class="save-listing-btn" onclick="event.preventDefault(); toggleFavorite({{ $listing->id }})" style="{{ $isFavorited ? 'color: #FF4444;' : '' }}">
                    <i class="{{ $isFavorited ? 'fas' : 'far' }} fa-heart"></i>
                </button>
            </div>

            <div class="listing-main-details">
                <a href="{{ route('listings.show', $listing->slug) }}" class="listing-title-h3">
                    <h3>{{ $listing->year }} {{ $listing->title }}</h3>
                </a>
                <div class="listing-mileage-row">{{ number_format($listing->mileage) }} mi.</div>
                
                <div class="listing-price-container">
                    <div class="listing-primary-price">{!! \App\Helpers\Helpers::formatPrice($listing->price) !!}</div>
                    
                    <!-- Deal Badges (Mock logic) -->
                    @if($listing->price < 15000)
                        <div class="deal-badge great-deal"><i class="fas fa-arrow-down"></i> Great Deal</div>
                    @elseif($listing->price < 25000)
                        <div class="deal-badge good-deal">Good Deal</div>
                    @else
                        <div class="deal-badge fair-deal">Fair Deal</div>
                    @endif
                </div>
                
                 <div class="listing-monthly-est">Est. {!! \App\Helpers\Helpers::formatPrice($listing->price / 60) !!}/mo.*</div>

                <div class="listing-specs-summary">
                    {{ $listing->color ?? 'Black' }} Exterior • {{ ucfirst($listing->transmission) }} • {{ $listing->engine_size ?? 'N/A' }}
                </div>
            </div>
            
            <div class="listing-footer-row">
                <div class="listing-dealer-block">
                    <div class="dealer-name">{{ $listing->user->name ?? 'Dealer Name' }}</div>
                    <div class="dealer-rating">
                        <span class="rating-stars">
                            <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star-half-alt"></i>
                        </span>
                        <span class="rating-count">({{ rand(50, 500) }} reviews)</span>
                    </div>
                    <div class="dealer-location">{{ $listing->location ?? 'Chicago, IL' }}</div>
                </div>

                <div class="listing-cta-container">
                    <a href="{{ route('listings.show', $listing->slug) }}" class="btn-check-availability">Check Availability</a>
                </div>
            </div>
        </div>
    </div>
</div>
