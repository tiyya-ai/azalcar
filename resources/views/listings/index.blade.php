<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ request('q') ? 'Search: ' . request('q') : 'Cars for Sale' }} - azalcars Style</title>
    <meta name="description" content="Find the best car deals - buy and sell cars online">
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <style>
        /* Global Page Config */
        body { background-color: #ffffff; color: #1a1a1a; font-family: 'Manrope', sans-serif; }
        a { text-decoration: none; color: inherit; }

        /* Typography */
        .page-title { font-size: 32px; font-weight: 800; color: #1a1a1a; letter-spacing: -0.5px; margin-bottom: 8px; line-height: 1.2; }
        .match-count { font-size: 18px; font-weight: 700; color: #1a1a1a; }
        
        /* Layout Grid */
        .results-main-layout {
            display: grid;
            grid-template-columns: 270px 1fr;
            gap: 32px;
            align-items: flex-start;
            padding-bottom: 60px;
        }

        /* No results styling - keep filters visible */
        .no-results-state {
            text-align: center;
            padding: 80px 20px;
        }

        /* Sidebar Styling */
        .filter-sidebar {
            background: #fff;
            padding-right: 16px; 
        }
        
        .sidebar-header {
            display: flex; justify-content: space-between; align-items: center;
            margin-bottom: 20px;
        }
        .sidebar-title { font-size: 20px; font-weight: 700; }
        .reset-link { color: #5B2D8E; font-size: 14px; font-weight: 600; cursor: pointer; }

        .filter-section {
            border-top: 1px solid #e5e7eb;
            padding: 24px 0;
        }
        .filter-section.first { border-top: none; padding-top: 0; }
        
        .filter-header {
            font-size: 16px; font-weight: 700; color: #1a1a1a;
            margin-bottom: 16px; display: flex; justify-content: space-between; cursor: pointer;
        }
        
        /* Inputs & Controls */
        .c-input, .c-select {
            width: 100%; height: 48px; border-radius: 8px; border: 1px solid #767676;
            padding: 0 16px; font-size: 14px; color: #1a1a1a;
            appearance: none; background-color: #fff;
        }
        .c-select {
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%23333' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
            background-repeat: no-repeat; background-position: right 16px center;
        }
        
        .filter-radio-group { display: flex; flex-direction: column; gap: 12px; }
        .radio-item { display: flex; align-items: center; gap: 8px; font-size: 14px; font-weight: 500; cursor: pointer; }
        .radio-item input { width: 18px; height: 18px; accent-color: #1a1a1a; }

        .toggle-row { display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px; }
        .toggle-label { font-size: 14px; font-weight: 600; color: #1a1a1a; }
        
        /* Keyword Input */
        .keyword-input-wrapper { position: relative; margin-bottom: 24px; }
        .keyword-input { 
            width: 100%; height: 48px; border-radius: 24px; border: 1px solid #767676; padding: 0 40px 0 16px; 
            font-size: 14px; color: #1a1a1a; 
        }
        .keyword-btn {
            position: absolute; right: 8px; top: 50%; transform: translateY(-50%);
            background: #1a1a1a; color: white; border: none; width: 32px; height: 32px; border-radius: 50%;
            display: flex; align-items: center; justify-content: center; cursor: pointer;
        }

        /* Applied Filters Tags */
        .applied-filters-area { display: flex; flex-wrap: wrap; gap: 8px; margin-bottom: 24px; }
        .filter-pill {
            background: #f2f2f2; border-radius: 20px; padding: 6px 12px;
            font-size: 12px; font-weight: 600; color: #1a1a1a; display: flex; align-items: center; gap: 6px;
        }
        .filter-pill i { color: #666; cursor: pointer; font-size: 10px; }

        /* Sort Dropdown */
        .sort-wrapper { display: flex; align-items: center; gap: 8px; }
        .sort-label { font-size: 14px; font-weight: 600; color: #1a1a1a; }
        .sort-select {
            border: 1px solid #e5e7eb; 
            font-size: 14px; 
            font-weight: 600; 
            color: #1a1a1a; 
            background: #ffffff; 
            cursor: pointer; 
            appearance: none; 
            padding: 8px 32px 8px 12px;
            border-radius: 6px;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%231a1a1a' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
            background-repeat: no-repeat; 
            background-position: right 10px center;
            transition: all 0.2s ease;
            outline: none;
        }
        .sort-select:hover {
            border-color: #5B2D8E;
            box-shadow: 0 2px 4px rgba(91, 45, 142, 0.1);
        }
        .sort-select:focus {
            border-color: #5B2D8E;
            box-shadow: 0 0 0 3px rgba(91, 45, 142, 0.1);
        }
        
        /* Breadcrumbs */
        .breadcrumbs { font-size: 12px; color: #555; margin-bottom: 12px; }
        .breadcrumbs a { text-decoration: underline; color: #555; }

        @media (max-width: 900px) {
            .results-main-layout { grid-template-columns: 1fr; }
            .filter-sidebar { display: none; /* Hide for mobile, would need mobile filter modal */ }
        }

        /* View Toggles */
        .view-toggle-container { display: flex; gap: 8px; margin-right: 16px; border-right: 1px solid #ddd; padding-right: 16px; }
        .view-toggle-btn { 
            background: none; border: none; cursor: pointer; color: #999; font-size: 18px; padding: 4px;
            transition: color 0.2s;
        }
        .view-toggle-btn.active { color: #1a1a1a; }
        .view-toggle-btn:hover { color: #1a1a1a; }

        /* General Card Styles */
        .listing-card {
            background: white; border: 1px solid #e5e7eb; border-radius: 8px;
            transition: box-shadow 0.2s; overflow: hidden;
            display: flex; flex-direction: column;
        }
        .listing-card:hover { box-shadow: 0 4px 16px rgba(0,0,0,0.1); border-color: #d1d5db; }

        /* No Image Placeholder Centering */
        .no-image-placeholder {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            height: 100%;
            color: #888;
            font-size: 14px;
            font-weight: 500;
        }
        .no-image-placeholder i {
            font-size: 24px;
            margin-bottom: 8px;
            color: #aaa;
        }
        
        /* List View (Default Horizontal) */
        .listings-grid.list-view { display: flex; flex-direction: column; gap: 24px; }
        .listings-grid.list-view .listing-card {
            display: flex;
            flex-direction: row;
            height: 100%;
            max-width: 100%;
            margin-bottom: 0;
        }
        
        .listings-grid.list-view .listing-content-wrapper { 
            display: flex; flex-direction: row; width: 100%; 
            min-height: 255px; /* User Fix */
        }
        .listings-grid.list-view .listing-image-container { 
            width: 300px; min-width: 300px; position: relative; background: #f5f5f5; overflow: hidden; 
        }
        
        /* Grid View (Vertical) */
        .listings-grid.grid-view { 
            display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 24px; 
        }
        
        .listings-grid.grid-view .listing-content-wrapper { 
            display: flex; flex-direction: column; width: 100%; height: 100%;
        }
        .listings-grid.grid-view .listing-image-container { 
            width: 100%; height: 200px; position: relative; background: #f5f5f5; overflow: hidden; 
        }
        
        /* Shared Child Element Styles */
        .image-slide-link { display: block; width: 100%; height: 100%; }
        .current-slide { width: 100%; height: 100%; object-fit: cover; display: block; }
        
        .image-count-badge { position: absolute; bottom: 8px; right: 8px; background: rgba(0,0,0,0.6); color: white; padding: 2px 6px; border-radius: 4px; font-size: 10px; font-weight: 700; z-index: 5; }
        
        .carousel-arrow { position: absolute; top: 50%; transform: translateY(-50%); width: 28px; height: 28px; border-radius: 50%; background: rgba(255,255,255,0.9); border: none; display: flex; align-items: center; justify-content: center; cursor: pointer; z-index: 6; opacity: 0; transition: opacity 0.2s; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .listing-image-container:hover .carousel-arrow { opacity: 1; }
        .carousel-arrow.prev { left: 8px; color: #1a1a1a; }
        .carousel-arrow.next { right: 8px; color: #1a1a1a; }
        
        .listing-info { padding: 16px; flex: 1; display: flex; flex-direction: column; position: relative; }
        .listing-top-row { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 4px; }
        
        .listing-condition-label { font-size: 10px; font-weight: 800; text-transform: uppercase; color: #6b7280; letter-spacing: 0.05em; }
        .save-listing-btn { background: none; border: none; color: #5B2D8E; font-size: 20px; cursor: pointer; padding: 0; margin-top: -4px; }
        .save-listing-btn:hover { color: #331557; }
        
        .listing-title-h3 { text-decoration: none; color: inherit; }
        .listing-title-h3 h3 { font-size: 18px; font-weight: 700; color: #1a1a1a; margin-bottom: 4px; line-height: 1.3; }
        
        .listing-mileage-row { font-size: 13px; color: #4b5563; font-weight: 500; margin-bottom: 8px; }
        
        .listing-price-container { display: flex; align-items: center; gap: 8px; margin-bottom: 2px; flex-wrap: wrap; }
        .listing-primary-price { font-size: 22px; font-weight: 800; color: #1a1a1a; line-height: 1; }
        .listing-monthly-est { font-size: 11px; color: #6b7280; margin-bottom: 12px; }
        
        .listing-specs-summary { font-size: 12px; color: #4b5563; margin-bottom: 16px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        
        .badge-sponsored { position: absolute; top: 12px; left: 12px; background: #fff; color: #1a1a1a; padding: 4px 8px; border-radius: 4px; font-size: 10px; font-weight: 700; text-transform: uppercase; box-shadow: 0 2px 4px rgba(0,0,0,0.1); z-index: 5; }
        
        .deal-badge { font-size: 10px; font-weight: 700; color: #1a1a1a; padding: 3px 6px; border-radius: 4px; display: flex; align-items: center; gap: 4px; }
        .deal-badge.great-deal { background: #E6F4EA; color: #137333; }
        .deal-badge.good-deal { background: #E8F0FE; color: #1967D2; }
        .deal-badge.fair-deal { background: #F1F3F4; color: #5F6368; }
        
        .listing-footer-row { margin-top: auto; display: flex; justify-content: space-between; align-items: flex-end; }
        .dealer-name { font-size: 12px; font-weight: 700; color: #1a1a1a; margin-bottom: 2px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 140px; }
        .rating-stars { color: #5B2D8E; font-size: 10px; }
        .rating-count { font-size: 10px; color: #6b7280; margin-left: 2px; }
        .dealer-location { font-size: 11px; color: #6b7280; margin-top: 2px; }
        
        .btn-check-availability { background-color: #1a1a1a; color: white; padding: 8px 16px; border-radius: 20px; font-weight: 700; font-size: 13px; text-decoration: none; display: inline-block; transition: background 0.2s; box-shadow: 0 2px 4px rgba(0,0,0,0.1); white-space: nowrap; }
        .btn-check-availability:hover { background-color: #333; }
        
        /* Mobile overrides */
        @media (max-width: 900px) { 
            .listings-grid.list-view .listing-content-wrapper { flex-direction: column; min-height: auto; } 
            .listings-grid.list-view .listing-image-container { width: 100%; aspect-ratio: 4/3; } 
            .listing-footer-row { flex-direction: column; align-items: flex-start; gap: 12px; } 
            .btn-check-availability { width: 100%; text-align: center; } 
        }

        /* Mobile Filter Modal */
        .mobile-filter-modal {
            display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background: white; z-index: 2000; overflow-y: auto; padding: 20px;
        }
        .mobile-filter-modal.active { display: block; }
        .mobile-filter-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; border-bottom: 1px solid #111; padding-bottom: 15px; }
        .mobile-filter-close { background: none; border: none; font-size: 24px; cursor: pointer; color: #111; }
        .mobile-filter-btn { 
            display: none; 
            background: #1a1a1a; color: white; padding: 8px 16px; border-radius: 20px; 
            font-weight: 700; font-size: 14px; margin-right: 12px; cursor: pointer; 
        }
        @media (max-width: 900px) {
            .mobile-filter-btn { display: inline-block; }
        }

    </style>
</head>

<body>
    @include('partials.header')

    <main class="main-content" style="padding-top: 24px;">
        <div class="container">
            <!-- Breadcrumbs -->
            <nav class="breadcrumbs">
                <a href="{{ route('home') }}">Home</a>
                <span style="margin: 0 6px;">/</span>
                <span>Cars for sale</span>
                @if(request('year'))
                    <span style="margin: 0 6px;">/</span>
                    <span>{{ request('year') }}</span>
                @endif
            </nav>

            <!-- Dynamic Page Title -->
            <div class="search-title-row" style="margin-bottom: 24px;">
                <h1 class="page-title">
                    @php
                        $condition = request('condition') && request('condition') != 'all' ? ucfirst(request('condition')) : 'New and used';
                        $make = request('make') ? ucwords(str_replace('-', ' ', request('make'))) : '';
                        $model = request('model') ? ucwords(str_replace('-', ' ', request('model'))) : '';
                        $year = request('year') ? 'from ' . request('year') : '';
                        $location = 'near you';
                    @endphp
                    {{ $condition }} {{ $make }} {{ $model }} {{ !$make && !$model ? 'vehicles' : '' }} for sale
                    @if($year)
                        <span style="color: #555; font-weight: 400; font-size: 0.8em; margin-left: 8px;">{{ $year }}</span>
                    @endif
                    <span style="color: #555; font-weight: 400; font-size: 0.8em; margin-left: 8px;">{{ $location }}</span>
                </h1>
            </div>

            <div class="results-main-layout">
                <!-- Sidebar Filters -->
                <aside class="filter-sidebar">
                    <div class="sidebar-header">
                        <div class="sidebar-title">Filter</div>
                        @if(count(request()->all()) > 0)
                            <a href="{{ route('listings.search') }}" class="reset-link">Clear all</a>
                        @endif
                    </div>
                    @include('listings.partials.filter-form')
                </aside>

                <!-- Results Column -->
                <div class="results-column">
                    <div class="results-header-row" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; border-bottom: 2px solid #1a1a1a; padding-bottom: 12px;">
                        <div class="match-count">{{ $listings->total() }} matches</div>
                        
                        <div style="display: flex; align-items: center;">
                            <button class="mobile-filter-btn" id="mobileFilterBtn"><i class="fas fa-sliders-h"></i> Filters</button>
                            <!-- Top View Toggle -->
                             <div class="view-toggle-container">
                                <button type="button" class="view-toggle-btn active" data-view="list" title="List View"><i class="fas fa-list"></i></button>
                                <button type="button" class="view-toggle-btn" data-view="grid" title="Grid View"><i class="fas fa-th-large"></i></button>
                            </div>

                            <div class="sort-wrapper">
                                <span class="sort-label">Sort by</span>
                                <form id="sortForm" action="{{ route('listings.search') }}" method="GET" style="margin: 0;">
                                    @foreach(request()->except('sort') as $key => $val)
                                        @if(!is_array($val)) <input type="hidden" name="{{ $key }}" value="{{ $val }}"> @endif
                                    @endforeach
                                    <select name="sort" class="sort-select" onchange="document.getElementById('sortForm').submit()">
                                        <option value="best_match">Best match</option>
                                        <option value="price_low" {{ request('sort') == 'price_low' ? 'selected' : '' }}>Lowest price</option>
                                        <option value="price_high" {{ request('sort') == 'price_high' ? 'selected' : '' }}>Highest price</option>
                                        <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Newest</option>
                                    </select>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Listings -->
                    <div class="listings-grid list-view" id="listingsContainer">
                         @forelse($listings as $listing)
                            @include('partials.listing-card')
                        @empty
                             <div class="no-results-state" style="text-align: center; padding: 80px 20px;">
                                <h3 style="font-size: 24px; font-weight: 800; color: #111;">No ads found</h3>
                                <p style="color: #666; margin-top: 8px;">Try adjusting your filters.</p>
                                <a href="{{ route('listings.search') }}" style="color: #5B2D8E; font-weight: 700; margin-top: 16px; display: inline-block;">Clear all filters</a>
                            </div>
                        @endforelse
                    </div>
                    
                    <div class="mt-8">
                        {{ $listings->appends(request()->all())->links('pagination::bootstrap-4') }}
                    </div>
                </div>
            </div>
        </div>
    </main>

    @include('partials.footer')
    @include('partials.mobile-nav')

    <script src="{{ asset('assets/js/script.js') }}"></script>
    <script>
        // View Toggle Logic
        const container = document.getElementById('listingsContainer');
        const toggles = document.querySelectorAll('.view-toggle-btn');
        
        // Load preference
        const savedView = localStorage.getItem('listingsView') || 'list';
        setView(savedView);

        toggles.forEach(btn => {
            btn.addEventListener('click', () => {
                const view = btn.dataset.view;
                setView(view);
                localStorage.setItem('listingsView', view);
            });
        });

        function setView(view) {
            container.className = `listings-grid ${view}-view`;
            toggles.forEach(btn => {
                if (btn.dataset.view === view) btn.classList.add('active');
                else btn.classList.remove('active');
            });
        }

        // Initialize Carousels
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.listing-image-container').forEach(container => {
                const prevBtn = container.querySelector('.prev');
                const nextBtn = container.querySelector('.next');
                const img = container.querySelector('.current-slide');
                const badge = container.querySelector('.image-count-badge');
                
                if (!prevBtn || !nextBtn || !img) return;

                const images = JSON.parse(container.dataset.images);
                if (images.length <= 1) {
                    prevBtn.style.display = 'none';
                    nextBtn.style.display = 'none';
                    return;
                }

                const updateImage = (index) => {
                    img.src = images[index];
                    container.dataset.index = index;
                    if (badge) badge.textContent = `${index + 1}/${images.length}`;
                };

                prevBtn.addEventListener('click', (e) => {
                    e.preventDefault();
                    e.stopPropagation(); // Stop link click
                    let index = parseInt(container.dataset.index);
                    index = (index - 1 + images.length) % images.length;
                    updateImage(index);
                });

                nextBtn.addEventListener('click', (e) => {
                    e.preventDefault();
                    e.stopPropagation(); // Stop link click
                    let index = parseInt(container.dataset.index);
                    index = (index + 1) % images.length;
                    updateImage(index);
                });
            });
        });

        // Dynamic Model Update
        // Dynamic Model Update
        const makeSelects = document.querySelectorAll('.make-select');
        const modelSelects = document.querySelectorAll('.model-select');

        if (makeSelects.length > 0) {
             const updateAllModels = (makeId, selectedModel = null) => {
                 // Update all model selects
                 modelSelects.forEach(modelSelect => {
                     if (!makeId) {
                         modelSelect.innerHTML = '<option value="">All Models</option>';
                         return;
                     }
                 });
                 
                 if(makeId) {
                    fetch(`/api/models/${makeId}`)
                       .then(r => {
                          if (!r.ok) {
                             throw new Error('API request failed');
                          }
                          return r.json();
                       })
                       .then(models => {
                          console.log('Loaded models:', models);
                          modelSelects.forEach(modelSelect => {
                             modelSelect.innerHTML = '<option value="">All Models</option>';
                             if (Array.isArray(models)) {
                                models.forEach(m => {
                                   const modelValue = m.slug || m.id;
                                   const sel = modelValue == selectedModel ? 'selected' : '';
                                   modelSelect.innerHTML += `<option value="${modelValue}" ${sel}>${m.name}</option>`;
                                });
                             }
                          });
                       })
                       .catch(error => {
                          console.error('Error loading models:', error);
                          modelSelects.forEach(modelSelect => {
                             modelSelect.innerHTML = '<option value="">Error loading models</option>';
                          });
                       });
                 }
             };

             makeSelects.forEach(makeSelect => {
                 makeSelect.addEventListener('change', function() {
                     // Sync other make selects
                     makeSelects.forEach(ms => ms.value = this.value);
                     updateAllModels(this.value);
                     // Don't auto-submit, let user control when to search
                 });
             });

             // Initialize
             if (makeSelects[0].value) { updateAllModels(makeSelects[0].value, "{{ request('model') }}"); }
        }

        // Mobile Filter Toggle
        const filterBtn = document.getElementById('mobileFilterBtn');
        const filterModal = document.getElementById('mobileFilterModal');
        const filterClose = document.getElementById('mobileFilterClose');

        if(filterBtn && filterModal && filterClose) {
            filterBtn.addEventListener('click', () => {
                filterModal.classList.add('active');
                document.body.style.overflow = 'hidden';
            });
            filterClose.addEventListener('click', () => {
                filterModal.classList.remove('active');
                document.body.style.overflow = '';
            });
        }
    </script>
    <!-- Mobile Filter Modal -->
    <div id="mobileFilterModal" class="mobile-filter-modal">
        <div class="mobile-filter-header">
            <h2 style="font-size: 24px; font-weight: 800;">Filters</h2>
            <button class="mobile-filter-close" id="mobileFilterClose"><i class="fas fa-times"></i></button>
        </div>
        @include('listings.partials.filter-form')
    </div>
</body>
</html>
