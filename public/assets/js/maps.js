// Google Maps Integration for azal Cars
// Map view functionality with real coordinates

let map;
let markers = [];
let infoWindow;

// Initialize map when user clicks "Show on map" button
function initializeMap() {
    // Default center (USA)
    const defaultCenter = { lat: 39.8283, lng: -98.5795 };

    // Create the map
    map = new google.maps.Map(document.getElementById('google-map'), {
        zoom: 5,
        center: defaultCenter,
        styles: [
            {
                "featureType": "poi",
                "elementType": "labels",
                "stylers": [{ "visibility": "off" }]
            }
        ],
        mapTypeControl: true,
        streetViewControl: true,
        fullscreenControl: true,
    });

    infoWindow = new google.maps.InfoWindow();

    // Load listings and add markers
    loadMapListings();
}

// Load listings data for map
async function loadMapListings() {
    try {
        // Get current search parameters
        const urlParams = new URLSearchParams(window.location.search);
        urlParams.append('format', 'json');

        const response = await fetch('/search?' + urlParams.toString());
        const listings = await response.json();

        // Clear existing markers
        clearMarkers();

        if (listings.length === 0) {
            alert('No listings found with coordinates');
            return;
        }

        const bounds = new google.maps.LatLngBounds();

        // Add marker for each listing with coordinates
        listings.forEach(listing => {
            if (listing.latitude && listing.longitude) {
                addMarker(listing, bounds);
            }
        });

        // Fit map to show all markers
        if (markers.length > 0) {
            map.fitBounds(bounds);
        }

        // Update map sidebar with listings
        updateMapSidebar(listings);

    } catch (error) {
        console.error('Error loading map listings:', error);
        alert('Failed to load map data. Please try again.');
    }
}

// Add a marker for a listing
function addMarker(listing, bounds) {
    const position = {
        lat: parseFloat(listing.latitude),
        lng: parseFloat(listing.longitude)
    };

    // Create custom marker with price
    const marker = new google.maps.Marker({
        position: position,
        map: map,
        title: listing.title,
        label: {
            text: '$' + (listing.price / 1000).toFixed(0) + 'k',
            color: '#fff',
            fontSize: '12px',
            fontWeight: 'bold'
        },
        icon: {
            path: google.maps.SymbolPath.CIRCLE,
            scale: 20,
            fillColor: '#4a90e2',
            fillOpacity: 1,
            strokeColor: '#fff',
            strokeWeight: 2,
        }
    });

    // Add click listener to show info window
    marker.addListener('click', () => {
        const content = `
            <div style="max-width: 250px;">
                <h3 style="margin: 0 0 8px 0; font-size: 16px;">${listing.title}</h3>
                <p style="margin: 0 0 8px 0; font-size: 14px; color: #666;">
                    ${listing.year} • ${listing.make} ${listing.model}
                </p>
                <p style="margin: 0 0 12px 0; font-size: 18px; font-weight: bold; color: #4a90e2;">
                    $${listing.price.toLocaleString()}
                </p>
                <p style="margin: 0 0 12px 0; font-size: 14px;">
                    <i class="fas fa-map-marker-alt"></i> ${listing.location}
                </p>
                <a href="/listing/${listing.slug}" 
                   style="display: block; padding: 8px 16px; background: #4a90e2; color: white; text-align: center; text-decoration: none; border-radius: 4px;">
                    View Details
                </a>
            </div>
        `;

        infoWindow.setContent(content);
        infoWindow.open(map, marker);
    });

    markers.push(marker);
    bounds.extend(position);
}

// Clear all markers from map
function clearMarkers() {
    markers.forEach(marker => marker.setMap(null));
    markers = [];
}

// Update sidebar with listings
function updateMapSidebar(listings) {
    const mapListings = document.getElementById('map-listings');
    if (!mapListings) return;

    mapListings.innerHTML = '';

    listings.forEach(listing => {
        const card = document.createElement('div');
        card.className = 'map-listing-item';
        card.style.cssText = 'padding: 16px; border-bottom: 1px solid #e0e0e0; cursor: pointer;';

        card.innerHTML = `
            <h4 style="margin: 0 0 8px 0; font-size: 14px;">${listing.title}</h4>
            <p style="margin: 0 0 4px 0; font-size: 16px; font-weight: bold; color: #4a90e2;">
                $${listing.price.toLocaleString()}
            </p>
            <p style="margin: 0; font-size: 12px; color: #666;">
                <i class="fas fa-map-marker-alt"></i> ${listing.location}
            </p>
        `;

        // Click to center map on this listing
        card.addEventListener('click', () => {
            if (listing.latitude && listing.longitude) {
                map.setCenter({
                    lat: parseFloat(listing.latitude),
                    lng: parseFloat(listing.longitude)
                });
                map.setZoom(12);

                // Find and trigger the marker
                const marker = markers.find(m =>
                    m.getPosition().lat() === parseFloat(listing.latitude) &&
                    m.getPosition().lng() === parseFloat(listing.longitude)
                );
                if (marker) {
                    google.maps.event.trigger(marker, 'click');
                }
            }
        });

        mapListings.appendChild(card);
    });
}

// Initialize when Google Maps script loads
window.initGoogleMaps = initializeMap;

// Export for external use
window.mapFunctions = {
    initialize: initializeMap,
    reload: loadMapListings,
    clear: clearMarkers
};
