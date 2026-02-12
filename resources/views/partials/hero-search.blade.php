@php
    // Attempt to get real makes and models from the database
    try {
        $makes = \App\Models\Make::with('models')->orderBy('name')->get();
        if ($makes->isEmpty()) {
            throw new \Exception("No makes found");
        }
        $makesList = $makes;
        $modelsByMake = $makes->mapWithKeys(function($make) {
            return [$make->slug => $make->models->map(function($m) {
                return ['name' => $m->name, 'slug' => $m->slug];
            })];
        });
    } catch (\Exception $e) {
        $makesList = collect(); 
        $modelsByMake = [];
    }
    $jsonModels = json_encode($modelsByMake);
    
    // Generate years for dropdown
    $currentYear = date('Y');
    $years = range($currentYear + 1, 1990);
@endphp

<section class="hero-section">
    <div class="hero-banner" style="background-image: url('https://platform.cstatic-images.com/ad-creative/7420ccef-ae26-4a8b-8c09-511d39d6bf12/0598_Hero_8_7-Core_Model_CARSCOM-ROGUE-B_3600x1130.png?w=2400&q=70');">
        <span class="hero-banner-overlay"></span>
        <div class="container hero-banner-inner">
            <h1 class="hero-title text-center">Imagine the possibilities</h1>
        </div>
    </div>

    <div class="hero-widget-shell">
        <div class="container">
            <div class="hero-widget-card">
                <div class="hero-widget-body">
                    <form action="{{ route('listings.search') }}" method="GET" id="hero-search-form">
                        <div class="hero-search-bar">
                            <div class="hero-search-prefix">
                                <span class="hero-search-try">Try</span>
                                <div id="suggestion-carousel" class="hero-search-carousel">
                                    <span>great deals under $20k</span>
                                    <span>SUVs w/carplay under $30k</span>
                                    <span>new mid size trucks</span>
                                    <span>3 rows under $40k</span>
                                </div>
                            </div>
                            <input
                                type="text"
                                name="q"
                                id="hero-ai-input"
                                class="hero-search-input"
                                onfocus="hideSuggestions()"
                                onblur="showSuggestions()"
                                placeholder=""
                                autocomplete="off"
                            >
                            <button type="submit" class="hero-search-icon" aria-label="Search">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </button>

                            <!-- Live Search Dropdown -->
                            <div id="live-search-dropdown" class="live-search-dropdown" style="display: none;">
                                <div id="live-search-results"></div>
                            </div>
                        </div>

                        <div class="hero-search-separator">- Or search by -</div>

                        <div class="hero-search-filters">
                            <div class="hero-filter">
                                <label for="condition" class="hero-filter-label">New/Used</label>
                                <select name="condition" id="condition" onchange="updateMatches()" class="hero-filter-input">
                                    <option value="">New & certified</option>
                                    <option value="new">New</option>
                                    <option value="used" selected>Used</option>
                                    <option value="cpo">Certified Pre-Owned</option>
                                </select>
                            </div>
                            <div class="hero-filter">
                                <label for="make" class="hero-filter-label">Make</label>
                                <select name="make" id="make" onchange="handleMakeChange()" class="hero-filter-input">
                                    <option value="">All Makes</option>
                                    @foreach($makes as $makeItem)
                                        <option value="{{ $makeItem->slug }}" {{ ($makeItem->name == 'Dodge' || $makeItem->name == 'Ford') ? 'selected' : '' }}>{{ $makeItem->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="hero-filter">
                                <label for="model" class="hero-filter-label">Model</label>
                                <select name="model" id="model" onchange="updateMatches()" class="hero-filter-input">
                                    <option value="">All models</option>
                                </select>
                            </div>

                            <div class="hero-filter">
                                <label for="city" class="hero-filter-label">City/Location</label>
                                <select name="city" id="city" onchange="updateMatches()" class="hero-filter-input">
                                    <option value="">All Cities</option>
                                    <optgroup label="🇰🇷 Korea">
                                        <option value="Seoul" {{ request('city') == 'Seoul' ? 'selected' : '' }}>Seoul</option>
                                        <option value="Busan" {{ request('city') == 'Busan' ? 'selected' : '' }}>Busan</option>
                                        <option value="Incheon" {{ request('city') == 'Incheon' ? 'selected' : '' }}>Incheon</option>
                                        <option value="Daegu" {{ request('city') == 'Daegu' ? 'selected' : '' }}>Daegu</option>
                                        <option value="Daejeon" {{ request('city') == 'Daejeon' ? 'selected' : '' }}>Daejeon</option>
                                        <option value="Gwangju" {{ request('city') == 'Gwangju' ? 'selected' : '' }}>Gwangju</option>
                                        <option value="Ulsan" {{ request('city') == 'Ulsan' ? 'selected' : '' }}>Ulsan</option>
                                        <option value="Suwon" {{ request('city') == 'Suwon' ? 'selected' : '' }}>Suwon</option>
                                        <option value="Changwon" {{ request('city') == 'Changwon' ? 'selected' : '' }}>Changwon</option>
                                        <option value="Goyang" {{ request('city') == 'Goyang' ? 'selected' : '' }}>Goyang</option>
                                    </optgroup>
                                    <optgroup label="🌍 Other">
                                        <option value="Dubai" {{ request('city') == 'Dubai' ? 'selected' : '' }}>Dubai</option>
                                        <option value="Riyadh" {{ request('city') == 'Riyadh' ? 'selected' : '' }}>Riyadh</option>
                                        <option value="Jeddah" {{ request('city') == 'Jeddah' ? 'selected' : '' }}>Jeddah</option>
                                    </optgroup>
                                </select>
                            </div>
                            <div class="hero-filter">
                                <label for="year" class="hero-filter-label">YEAR</label>
                                <select name="year" id="year" onchange="updateMatches()" class="hero-filter-input">
                                    <option value="">All Years</option>
                                    @foreach($years as $y)
                                        <option value="{{ $y }}" {{ request('year') == $y ? 'selected' : '' }}>{{ $y }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="hero-filter hero-filter-button">
                                <button type="submit" class="hero-filter-cta">
                                    Show <span id="match-count">...</span> matches
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
    const vehicleData = {
        models: {!! $jsonModels !!}
    };
    
    // Suggestion Carousel Logic
    let currentSugIndex = 0;
    const sugCount = 4;
    const sugCarousel = document.getElementById('suggestion-carousel');
    const aiInput = document.getElementById('hero-ai-input');
    
    function rotateSuggestions() {
        if (!aiInput.matches(':focus') && aiInput.value === '') {
            currentSugIndex = (currentSugIndex + 1) % sugCount;
            sugCarousel.style.transform = `translateY(-${currentSugIndex * 24}px)`;
        }
    }
    
    let sugInterval = setInterval(rotateSuggestions, 3000);
    
    function hideSuggestions() {
        document.getElementById('suggestion-carousel').parentElement.style.opacity = '0';
    }
    
    function showSuggestions() {
        if (aiInput.value === '') {
            document.getElementById('suggestion-carousel').parentElement.style.opacity = '1';
        }
    }

    function handleMakeChange() {
        const makeSelect = document.getElementById('make');
        const modelSelect = document.getElementById('model');
        const selectedMake = makeSelect.value;
        
        // Reset and populate model select
        modelSelect.innerHTML = '<option value="">All models</option>';
        
        if (selectedMake && vehicleData.models[selectedMake]) {
            vehicleData.models[selectedMake].forEach(model => {
                const option = document.createElement('option');
                option.value = model.slug;
                option.textContent = model.name;
                // Preserve selection if it was there before from request or session
                if (model.slug === "{{ request('model') }}") {
                    option.selected = true;
                }
                modelSelect.appendChild(option);
            });
            console.log(`Loaded models for: ${selectedMake}`);
        }
        updateMatches();
    }

    function updateMatches() {
        const matchCountElement = document.getElementById('match-count');
        const make = document.getElementById('make').value;
        const model = document.getElementById('model').value;
        const condition = document.getElementById('condition').value;
        
        // Mocked logic for count
        let count = 28450;
        if (make) {
            count = 1540;
            if (model) count = 219;
        }
        if (condition === 'new') count = Math.floor(count * 0.4);
        if (condition === 'used') count = Math.floor(count * 0.6);
        
        matchCountElement.textContent = count.toLocaleString();
    }

    // Live Search Functionality
    let searchTimeout;
    const aiInput = document.getElementById('hero-ai-input');
    const dropdown = document.getElementById('live-search-dropdown');
    const resultsContainer = document.getElementById('live-search-results');

    aiInput.addEventListener('input', function() {
        const query = this.value.trim();
        clearTimeout(searchTimeout);

        if (query.length < 2) {
            dropdown.style.display = 'none';
            return;
        }

        searchTimeout = setTimeout(() => {
            fetch(`/api/search/live?q=${encodeURIComponent(query)}`)
                .then(response => response.json())
                .then(data => {
                    if (data.results && data.results.length > 0) {
                        displayResults(data.results);
                        dropdown.style.display = 'block';
                    } else {
                        dropdown.style.display = 'none';
                    }
                })
                .catch(err => {
                    console.error('Search error:', err);
                    dropdown.style.display = 'none';
                });
        }, 300);
    });

    function displayResults(results) {
        resultsContainer.innerHTML = results.map(result => `
            <a href="/listing/${result.slug}" class="live-search-item">
                <div class="live-search-image">
                    <img src="${result.main_image || '/assets/images/no-image.png'}" alt="${result.title}">
                </div>
                <div class="live-search-info">
                    <div class="live-search-title">${result.year} ${result.make} ${result.model}</div>
                    <div class="live-search-price">$${result.price.toLocaleString()}</div>
                </div>
            </a>
        `).join('');
    }

    // Hide dropdown when clicking outside
    document.addEventListener('click', function(e) {
        if (!aiInput.contains(e.target) && !dropdown.contains(e.target)) {
            dropdown.style.display = 'none';
        }
    });

    document.addEventListener('DOMContentLoaded', () => {
        const makeValue = document.getElementById('make').value;
        if (makeValue) {
            handleMakeChange();
        }
    });
</script>

<style>
    .live-search-dropdown {
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        background: white;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        z-index: 1000;
        max-height: 400px;
        overflow-y: auto;
    }

    .live-search-item {
        display: flex;
        align-items: center;
        padding: 12px;
        border-bottom: 1px solid #f3f4f6;
        text-decoration: none;
        color: inherit;
        transition: background 0.2s;
    }

    .live-search-item:hover {
        background: #f9fafb;
    }

    .live-search-item:last-child {
        border-bottom: none;
    }

    .live-search-image {
        width: 60px;
        height: 45px;
        margin-right: 12px;
        flex-shrink: 0;
    }

    .live-search-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        border-radius: 4px;
    }

    .live-search-info {
        flex: 1;
    }

    .live-search-title {
        font-size: 14px;
        font-weight: 600;
        color: #1a1a1a;
        margin-bottom: 2px;
    }

    .live-search-price {
        font-size: 16px;
        font-weight: 700;
        color: #059669;
    }
</style>

