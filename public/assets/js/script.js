// Global AJAX CSRF setup
const originalFetch = window.fetch;
window.fetch = function (...args) {
    const [url, options = {}] = args;

    // Add CSRF token to POST requests
    if (options.method === 'POST' || (!options.method && url.includes('/'))) {
        options.headers = options.headers || {};
        options.headers['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
        options.credentials = 'same-origin';
    }

    return originalFetch.apply(this, args);
};

// Sample car listings data
const listings = [
    {
        id: 1,
        title: "Toyota Camry 2020, 2.5L, Automatic",
        price: "₽ 1,850,000",
        images: [
            "https://images.unsplash.com/photo-1621007947382-bb3c3994e3fb?w=400",
            "https://images.unsplash.com/photo-1550355291-bbee04a92027?w=400",
            "https://images.unsplash.com/photo-1621007947382-bb3c3994e3fb?w=400",
            "https://images.unsplash.com/photo-1550355291-bbee04a92027?w=400"
        ],
        location: "Moscow",
        time: "Today, 14:30"
    },
    {
        id: 2,
        title: "BMW X5 2021, 3.0L Diesel, Full Option",
        price: "₽ 4,500,000",
        images: [
            "https://images.unsplash.com/photo-1617531653332-bd46c24f2068?w=400",
            "https://images.unsplash.com/photo-1555215695-3004980ad54e?w=400",
            "https://images.unsplash.com/photo-1617531653332-bd46c24f2068?w=400",
            "https://images.unsplash.com/photo-1555215695-3004980ad54e?w=400"
        ],
        location: "Saint Petersburg",
        time: "Today, 13:15"
    },
    {
        id: 3,
        title: "Mercedes-Benz E-Class 2019, AMG Package",
        price: "₽ 3,200,000",
        images: [
            "https://images.unsplash.com/photo-1618843479313-40f8afb4b4d8?w=400",
            "https://images.unsplash.com/photo-1617814076367-b759c7d7ef73?w=400",
            "https://images.unsplash.com/photo-1618843479313-40f8afb4b4d8?w=400",
            "https://images.unsplash.com/photo-1617814076367-b759c7d7ef73?w=400"
        ],
        location: "Kazan",
        time: "Today, 12:45"
    },
    {
        id: 4,
        title: "Audi A6 2022, Quattro, Black Edition",
        price: "₽ 4,100,000",
        images: [
            "https://images.unsplash.com/photo-1606664515524-ed2f786a0bd6?w=400",
            "https://images.unsplash.com/photo-1603584173870-7f1efd9e697a?w=400",
            "https://images.unsplash.com/photo-1606664515524-ed2f786a0bd6?w=400",
            "https://images.unsplash.com/photo-1603584173870-7f1efd9e697a?w=400"
        ],
        location: "Novosibirsk",
        time: "Today, 11:20"
    },
    {
        id: 5,
        title: "Honda CR-V 2021, Hybrid, Excellent Condition",
        price: "₽ 2,650,000",
        images: [
            "https://images.unsplash.com/photo-1590362891991-f776e747a588?w=400",
            "https://images.unsplash.com/photo-1549317661-bd32c8ce0db2?w=400",
            "https://images.unsplash.com/photo-1590362891991-f776e747a588?w=400"
        ],
        location: "Yekaterinburg",
        time: "Today, 10:30"
    },
    {
        id: 6,
        title: "Lexus RX 350 2020, Premium Package",
        price: "₽ 3,800,000",
        images: [
            "https://images.unsplash.com/photo-1621007947382-bb3c3994e3fb?w=400",
            "https://images.unsplash.com/photo-1549317661-bd32c8ce0db2?w=400",
            "https://images.unsplash.com/photo-1621007947382-bb3c3994e3fb?w=400"
        ],
        location: "Moscow",
        time: "Yesterday, 22:15"
    },
    {
        id: 7,
        title: "Volkswagen Tiguan 2021, 2.0 TSI",
        price: "₽ 2,450,000",
        images: [
            "https://images.unsplash.com/photo-1549317661-bd32c8ce0db2?w=400",
            "https://images.unsplash.com/photo-1611821064430-4e1e7a0e50f0?w=400",
            "https://images.unsplash.com/photo-1549317661-bd32c8ce0db2?w=400"
        ],
        location: "Nizhny Novgorod",
        time: "Yesterday, 20:45"
    },
    {
        id: 8,
        title: "Porsche Cayenne 2020, Turbo S",
        price: "₽ 7,500,000",
        images: [
            "https://images.unsplash.com/photo-1611821064430-4e1e7a0e50f0?w=400",
            "https://images.unsplash.com/photo-1605559424843-9e4c228bf1c2?w=400",
            "https://images.unsplash.com/photo-1611821064430-4e1e7a0e50f0?w=400"
        ],
        location: "Samara",
        time: "Yesterday, 19:30"
    },
    {
        id: 9,
        title: "Hyundai Tucson 2022, Full Option",
        price: "₽ 2,100,000",
        images: [
            "https://images.unsplash.com/photo-1605559424843-9e4c228bf1c2?w=400",
            "https://images.unsplash.com/photo-1549317661-bd32c8ce0db2?w=400",
            "https://images.unsplash.com/photo-1605559424843-9e4c228bf1c2?w=400"
        ],
        location: "Rostov-on-Don",
        time: "Yesterday, 18:00"
    },
    {
        id: 10,
        title: "Tesla Model 3 2021, Long Range",
        price: "₽ 4,200,000",
        images: [
            "https://images.unsplash.com/photo-1560958089-b8a1929cea89?w=400",
            "https://images.unsplash.com/photo-1549317661-bd32c8ce0db2?w=400",
            "https://images.unsplash.com/photo-1560958089-b8a1929cea89?w=400"
        ],
        location: "Ufa",
        time: "Yesterday, 16:45"
    },
    {
        id: 11,
        title: "Nissan Qashqai 2020, 1.6L CVT",
        price: "₽ 1,650,000",
        images: [
            "https://images.unsplash.com/photo-1549317661-bd32c8ce0db2?w=400",
            "https://images.unsplash.com/photo-1549317661-bd32c8ce0db2?w=400",
            "https://images.unsplash.com/photo-1549317661-bd32c8ce0db2?w=400"
        ],
        location: "Krasnoyarsk",
        time: "Yesterday, 15:20"
    },
    {
        id: 12,
        title: "Mazda CX-5 2021, Skyactiv, Premium",
        price: "₽ 2,350,000",
        images: [
            "https://images.unsplash.com/photo-1549317661-bd32c8ce0db2?w=400",
            "https://images.unsplash.com/photo-1549317661-bd32c8ce0db2?w=400",
            "https://images.unsplash.com/photo-1549317661-bd32c8ce0db2?w=400"
        ],
        location: "Voronezh",
        time: "Yesterday, 14:10"
    }
];

// Recent car items data
const recentItems = [
    {
        id: 1,
        title: "Toyota Camry 2020",
        price: "₽ 1,750,000",
        image: "https://images.unsplash.com/photo-1621007947382-bb3c3994e3fb?w=200"
    },
    {
        id: 2,
        title: "BMW 5 Series 2019",
        price: "₽ 2,850,000",
        image: "https://images.unsplash.com/photo-1555215695-3004980ad54e?w=200"
    },
    {
        id: 3,
        title: "Kia Sportage 2021",
        price: "₽ 1,950,000",
        image: "https://images.unsplash.com/photo-1549317661-bd32c8ce0db2?w=200"
    },
    {
        id: 4,
        title: "Ford Explorer 2020",
        price: "₽ 3,200,000",
        image: "https://images.unsplash.com/photo-1519641471654-76ce0107ad1b?w=200"
    }
];

// DOM Elements (Selected lazily or checked for existence)
let listingsContainer, recentItemsContainer, searchInput, btnSearch, locationSelector;

function initElements() {
    listingsContainer = document.getElementById('listings-container');
    recentItemsContainer = document.getElementById('recent-items');
    searchInput = document.querySelector('.search-input');
    btnSearch = document.querySelector('.btn-search');
    locationSelector = document.querySelector('.location-selector');
}

// Initialize
document.addEventListener('DOMContentLoaded', function () {
    initElements();

    // Only load fake listings on the homepage, not on the search page or listing pages
    // Check if we're on the homepage (has #listings-container but not /search in URL)
    const isSearchPage = window.location.pathname.includes('/search') || window.location.pathname.includes('/listing');

    if (!isSearchPage && listingsContainer) {
        // Only populate fake data on homepage
        loadListings();
    }

    // Recent items are fine on homepage
    if (recentItemsContainer) {
        loadRecentItems();
    }

    setupEventListeners();
});

// Load listings (ONLY for homepage, not for search/listing pages)
function loadListings() {
    if (!listingsContainer) return;

    // Don't override if listingsContainer already has content from Blade
    if (listingsContainer.children.length > 0) {
        console.log('Listings already loaded from server (Blade template), skipping JS override');
        return;
    }

    listingsContainer.innerHTML = '';

    listings.forEach((listing, index) => {
        const card = createListingCard(listing);
        card.style.animationDelay = `${index * 0.05}s`;
        listingsContainer.appendChild(card);
    });
}

// Create listing card
function createListingCard(listing) {
    const card = document.createElement('div');
    card.className = 'listing-card';

    // Support both single image (recent) and multi-image (main)
    const images = listing.images || [listing.image];
    const imageCount = images.length;

    let galleryHtml = '';
    let segmentsHtml = '';
    let dotsHtml = '';

    images.forEach((img, i) => {
        galleryHtml += `<img src="${img}" class="gallery-img ${i === 0 ? 'active' : ''}" data-index="${i}">`;
        if (imageCount > 1) {
            segmentsHtml += `<div class="hover-segment" data-index="${i}"></div>`;
            dotsHtml += `<div class="pagination-dot ${i === 0 ? 'active' : ''}" data-index="${i}"></div>`;
        }
    });

    card.innerHTML = `
        <div class="listing-image">
            <div class="gallery-wrapper">
                ${galleryHtml}
            </div>
            <div class="hover-segments">
                ${segmentsHtml}
            </div>
            <div class="pagination-dots">
                ${dotsHtml}
            </div>
            <button class="favorite-btn">
                <i class="far fa-heart"></i>
            </button>
        </div>
        <div class="listing-info">
            <div class="listing-price">${listing.price}</div>
            <div class="listing-title">${listing.title}</div>
            <div class="listing-meta">
                <span>${listing.location}</span>
                <span>${listing.time}</span>
            </div>
        </div>
    `;

    // Gallery Hover Logic
    const segments = card.querySelectorAll('.hover-segment');
    const galleryImages = card.querySelectorAll('.gallery-img');
    const dots = card.querySelectorAll('.pagination-dot');

    segments.forEach(segment => {
        segment.addEventListener('mouseenter', function () {
            const index = this.dataset.index;

            // Update Images
            galleryImages.forEach(img => img.classList.remove('active'));
            galleryImages[index].classList.add('active');

            // Update Dots
            dots.forEach(dot => dot.classList.remove('active'));
            if (dots[index]) dots[index].classList.add('active');
        });
    });

    // Reset to first image on mouse leave from card
    card.addEventListener('mouseleave', function () {
        galleryImages.forEach(img => img.classList.remove('active'));
        galleryImages[0].classList.add('active');
        dots.forEach(dot => dot.classList.remove('active'));
        if (dots[0]) dots[0].classList.add('active');
    });

    // Add favorite functionality
    const favoriteBtn = card.querySelector('.favorite-btn');
    favoriteBtn.addEventListener('click', function (e) {
        e.stopPropagation();
        toggleFavorite(this);
    });

    // Add click handler
    card.addEventListener('click', function () {
        openListing(listing);
    });

    return card;
}

// Load recent items
function loadRecentItems() {
    recentItemsContainer.innerHTML = '';

    recentItems.forEach(item => {
        const recentItem = createRecentItem(item);
        recentItemsContainer.appendChild(recentItem);
    });
}

// Create recent item
function createRecentItem(item) {
    const div = document.createElement('div');
    div.className = 'recent-item';

    div.innerHTML = `
        <div class="recent-item-image">
            <img src="${item.image}" alt="${item.title}">
        </div>
        <div class="recent-item-info">
            <div class="recent-item-price">${item.price}</div>
            <div class="recent-item-title">${item.title}</div>
        </div>
    `;

    div.addEventListener('click', function () {
        openListing(item);
    });

    return div;
}

// Toggle favorite
function toggleFavorite(listingId) {
    if (!window.userAuthenticated) {
        window.location.href = '/login';
        return;
    }

    // Check if listingId is a button element (old usage) or an ID
    let btn, id;
    if (typeof listingId === 'object' && listingId.querySelector) {
        // Old usage: button element passed
        btn = listingId;
        id = null; // Will get from context
    } else {
        // New usage: listing ID passed
        id = listingId;
        // Find the button in the current context or globally
        // Support both .save-listing-btn (Blade) and .favorite-btn (JS-generated)
        btn = event?.target?.closest('.save-listing-btn') ||
            event?.target?.closest('.favorite-btn') ||
            event?.currentTarget;
    }

    if (!btn || btn.tagName !== 'BUTTON' && !btn.classList.contains('save-listing-btn') && !btn.classList.contains('favorite-btn')) {
        // Fallback search if we still don't have a reliable button
        btn = event?.target?.closest('button');
    }

    if (!btn) return;

    const icon = btn.querySelector('i');
    if (!icon) return;

    // Get listing ID from button data or parameter
    const listingIdToUse = id || btn.dataset.listingId || btn.closest('[data-listing-id]')?.dataset.listingId;

    // Optimistic UI update
    const wasActive = icon.classList.contains('fas');
    if (wasActive) {
        icon.classList.remove('fas');
        icon.classList.add('far');
        btn.style.color = '';
    } else {
        icon.classList.remove('far');
        icon.classList.add('fas');
        btn.style.color = '#FF4444';

        // Add animation
        btn.style.transform = 'scale(1.2)';
        setTimeout(() => {
            btn.style.transform = 'scale(1)';
        }, 200);
    }

    // Make AJAX call to server
    if (listingIdToUse) {
        fetch(`/favorites/${listingIdToUse}/toggle`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            }
        })
            .then(response => response.json())
            .then(data => {
                if (!data.success) {
                    // Revert on error
                    if (wasActive) {
                        icon.classList.remove('far');
                        icon.classList.add('fas');
                        btn.style.color = '#FF4444';
                    } else {
                        icon.classList.remove('fas');
                        icon.classList.add('far');
                        btn.style.color = '';
                    }
                    console.error('Failed to toggle favorite:', data.message);
                }
            })
            .catch(error => {
                // Revert on error
                if (wasActive) {
                    icon.classList.remove('far');
                    icon.classList.add('fas');
                    btn.style.color = '#FF4444';
                } else {
                    icon.classList.remove('fas');
                    icon.classList.add('far');
                    btn.style.color = '';
                }
                console.error('Error toggling favorite:', error);
            });
    }
}

// Open listing
function openListing(listing) {
    console.log('Opening listing:', listing);
    // Only redirect to static details.html if there's no proper listing slug
    // In production, this should use the actual listing route
    if (listing.slug) {
        window.location.href = `/listing/${listing.slug}`;
    } else {
        // Fallback for old fake data
        window.location.href = 'details.html';
    }
}

// Setup event listeners
function setupEventListeners() {
    // Search functionality
    if (btnSearch) {
        btnSearch.addEventListener('click', performSearch);
    }

    if (searchInput) {
        searchInput.addEventListener('keypress', function (e) {
            if (e.key === 'Enter') {
                performSearch();
            }
        });
    }

    // Location selector
    if (locationSelector) {
        locationSelector.addEventListener('click', function () {
            showLocationModal();
        });
    }

    // Category cards
    const categoryCards = document.querySelectorAll('.category-card');
    categoryCards.forEach(card => {
        card.addEventListener('click', function (e) {
            e.preventDefault();
            const category = this.querySelector('h3').textContent;
            filterByCategory(category);
        });
    });

    // Widget links
    const widgetLinks = document.querySelectorAll('.widget-link');
    widgetLinks.forEach(link => {
        link.addEventListener('click', function (e) {
            e.preventDefault();
            const service = this.querySelector('span').textContent;
            console.log('Opening service:', service);
        });
    });
}

// Perform search
function performSearch() {
    const query = searchInput.value.trim();

    if (query) {
        console.log('Searching for:', query);

        // Add animation
        btnSearch.style.transform = 'scale(0.95)';
        setTimeout(() => {
            btnSearch.style.transform = 'scale(1)';
        }, 150);

        // Filter listings
        const filteredListings = listings.filter(listing =>
            listing.title.toLowerCase().includes(query.toLowerCase())
        );

        if (filteredListings.length > 0) {
            displayFilteredListings(filteredListings);
        } else {
            alert(`No results found for "${query}"`);
        }
    }
}

// Display filtered listings
function displayFilteredListings(filteredListings) {
    listingsContainer.innerHTML = '';

    filteredListings.forEach((listing, index) => {
        const card = createListingCard(listing);
        card.style.animationDelay = `${index * 0.05}s`;
        listingsContainer.appendChild(card);
    });

    // Scroll to results
    document.querySelector('.recommendations').scrollIntoView({
        behavior: 'smooth',
        block: 'start'
    });
}

// Show location modal
function showLocationModal() {
    const locations = [
        'Moscow',
        'Saint Petersburg',
        'Novosibirsk',
        'Yekaterinburg',
        'Kazan',
        'Nizhny Novgorod',
        'Chelyabinsk',
        'Samara',
        'Omsk',
        'Rostov-on-Don'
    ];

    const location = prompt('Select your location:\n\n' + locations.join('\n'));

    if (location) {
        document.querySelector('.location-selector span').textContent = location;
    }
}

// Filter by category
function filterByCategory(category) {
    console.log('Filtering by category:', category);
    alert(`Showing listings in category: ${category}`);
}

// Smooth scroll for anchor links
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        const href = this.getAttribute('href');
        if (href && href.length > 1) { // Ensure it's not just "#"
            e.preventDefault();
            const target = document.querySelector(href);
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        }
    });
});

// Header scroll effect
let lastScrollTop = 0;
const header = document.querySelector('.header');

if (header) {
    window.addEventListener('scroll', function () {
        const scrollTop = window.pageYOffset || document.documentElement.scrollTop;

        if (scrollTop > 100) {
            header.style.boxShadow = '0 2px 12px rgba(0, 0, 0, 0.15)';
        } else {
            header.style.boxShadow = '0 1px 3px rgba(0, 0, 0, 0.08)';
        }

        lastScrollTop = scrollTop;
    });

    // Add hover effects to category cards
    const categoryCards = document.querySelectorAll('.category-card');
    categoryCards.forEach(card => {
        card.addEventListener('mouseenter', function () {
            // Zoom/Rotate effects removed
        });

        card.addEventListener('mouseleave', function () {
            // Zoom/Rotate effects removed
        });
    });

    // Lazy loading for images
    if ('IntersectionObserver' in window) {
        const imageObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    if (img.dataset.src) {
                        img.src = img.dataset.src;
                        img.removeAttribute('data-src');
                        observer.unobserve(img);
                    }
                }
            });
        });

        document.querySelectorAll('img[data-src]').forEach(img => {
            imageObserver.observe(img);
        });
    }

    // Add ripple effect to buttons
    document.querySelectorAll('.btn, .btn-categories, .btn-search').forEach(button => {
        button.addEventListener('click', function (e) {
            const ripple = document.createElement('span');
            const rect = this.getBoundingClientRect();
            const size = Math.max(rect.width, rect.height);
            const x = e.clientX - rect.left - size / 2;
            const y = e.clientY - rect.top - size / 2;

            ripple.style.width = ripple.style.height = size + 'px';
            ripple.style.left = x + 'px';
            ripple.style.top = y + 'px';
            ripple.classList.add('ripple');

            this.appendChild(ripple);

            setTimeout(() => {
                ripple.remove();
            }, 600);
        });
    });

    // Add ripple styles dynamically
    const style = document.createElement('style');
    style.textContent = `
    .btn, .btn-categories, .btn-search {
        position: relative;
        overflow: hidden;
    }
    
    .ripple {
        position: absolute;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.6);
        transform: scale(0);
        animation: ripple-animation 0.6s ease-out;
        pointer-events: none;
    }
    
    @keyframes ripple-animation {
        to {
            transform: scale(4);
            opacity: 0;
        }
    }
`;
    document.head.appendChild(style);
}


// View Toggle Logic
function setupViewToggle() {
    const viewBtns = document.querySelectorAll('.view-btn');
    const listingsContainer = document.getElementById('listings-container');

    if (!listingsContainer || viewBtns.length === 0) return;

    viewBtns.forEach(btn => {
        btn.addEventListener('click', function () {
            // Remove active class from all buttons
            viewBtns.forEach(b => b.classList.remove('active'));
            // Add active class to clicked button
            this.classList.add('active');

            // Check icon to determine view type
            const icon = this.querySelector('i');
            if (icon.classList.contains('fa-list')) {
                listingsContainer.classList.add('list-view');
            } else {
                listingsContainer.classList.remove('list-view');
            }
        });
    });
}

// Call setupViewToggle on init
// Call setupViewToggle on init
document.addEventListener('DOMContentLoaded', function () {
    setupViewToggle();
    setupMapView();

});

// Map View Logic
function setupMapView() {
    const showMapBtns = document.querySelectorAll('.sidebar-map-btn-container button');
    const closeMapBtn = document.getElementById('close-map-view');
    const mapViewContainer = document.getElementById('map-view-container');
    const mapListingsContainer = document.getElementById('map-listings');

    if (!mapViewContainer) return;

    // Show Map
    showMapBtns.forEach(btn => {
        btn.addEventListener('click', function () {
            mapViewContainer.style.display = 'flex';
            document.body.style.overflow = 'hidden'; // Prevent background scrolling

            // Populate map sidebar with listings if empty
            if (mapListingsContainer && mapListingsContainer.children.length === 0) {
                listings.forEach((listing, index) => {
                    // Create a simplified card for map sidebar
                    const card = createListingCard(listing);
                    // Force vertical layout style for sidebar if reused, 
                    // or we might need a separate create function if styles conflict excessively.
                    // For now, let's reuse but ensure it fits.
                    card.classList.remove('list-view-card'); // Ensure standard vertical or adapted
                    mapListingsContainer.appendChild(card);
                });
            }
        });
    });

    // Close Map
    if (closeMapBtn) {
        closeMapBtn.addEventListener('click', function () {
            mapViewContainer.style.display = 'none';
            document.body.style.overflow = '';
        });
    }
}
// Global Gallery Hover Logic for both Blade and JS-generated cards
document.addEventListener('mouseover', function (e) {
    const segment = e.target.closest('.hover-segment');
    if (segment) {
        const card = segment.closest('.listing-card');
        const index = segment.dataset.imageIndex || segment.dataset.index;

        const galleryImages = card.querySelectorAll('.gallery-img');
        const dots = card.querySelectorAll('.pagination-dot');

        galleryImages.forEach(img => img.classList.remove('active'));
        if (galleryImages[index]) galleryImages[index].classList.add('active');

        dots.forEach(dot => dot.classList.remove('active'));
        if (dots[index]) dots[index].classList.add('active');
    }
});

document.addEventListener('mouseout', function (e) {
    const card = e.target.closest('.listing-card');
    if (card && !e.relatedTarget?.closest('.listing-card')) {
        const galleryImages = card.querySelectorAll('.gallery-img');
        const dots = card.querySelectorAll('.pagination-dot');

        galleryImages.forEach(img => img.classList.remove('active'));
        if (galleryImages[0]) galleryImages[0].classList.add('active');

        dots.forEach(dot => dot.classList.remove('active'));
        if (dots[0]) dots[0].classList.add('active');
    }
});
