<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>azalcars - Shop New & Used Cars</title>
    <meta name="description" content="Shop new & used cars, research & compare models, find local dealers/sellers, calculate payments, value your car, sell/trade in your car & more at azalcars.">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- DM Sans Font (azalcars uses this) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        /* ========== azalcars.com PIXEL-PERFECT REPLICA ========== */
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Apercu Pro', 'DM Sans', sans-serif;
            background: #ffffff;
            color: #000000;
            line-height: 1.5;
        }
        
        h1 {
            font-family: 'DM Sans', sans-serif;
        }
        
        h2, h3, h4, h5, h6 {
            font-family: 'Apercu Pro', 'DM Sans', sans-serif;
        }
        
        /* ========== HERO SECTION ========== */
        .hero-section {
            background: #F5F5F5;
            padding: 48px 0 64px;
        }
        
        .hero-container {
            max-width: 1280px;
            margin: 0 auto;
            padding: 0 24px;
        }
        
        /* Search Tabs */
        .search-tabs {
            display: flex;
            gap: 0;
            margin-bottom: 24px;
        }
        
        .search-tab {
            padding: 16px 32px;
            background: transparent;
            border: none;
            font-family: 'DM Sans', sans-serif;
            font-size: 16px;
            font-weight: 700;
            color: #5E5E5E;
            cursor: pointer;
            border-bottom: 3px solid transparent;
            transition: all 0.2s;
        }
        
        .search-tab.active {
            color: #000000;
            border-bottom-color: #000000;
        }
        
        .search-tab:hover {
            color: #000000;
        }
        
        /* Main Search Bar - EXACT azalcars Style */
        .main-search-container {
            margin-bottom: 20px;
        }
        
        .main-search-wrapper {
            position: relative;
            display: flex;
            align-items: center;
            background: white;
            border: 1px solid #A3AEAC;
            border-radius: 56px;
            overflow: hidden;
            transition: all 0.2s;
            height: 56px;
        }
        
        .main-search-wrapper:focus-within {
            border-color: #6041E0;
            box-shadow: 0 0 0 3px rgba(96, 65, 224, 0.1);
        }
        
        .main-search-input {
            flex: 1;
            padding: 0 24px;
            font-family: 'DM Sans', sans-serif;
            font-size: 16px;
            border: none;
            outline: none;
            background: transparent;
            height: 100%;
        }
        
        .main-search-btn {
            padding: 0 24px;
            background: transparent;
            border: none;
            cursor: pointer;
            color: #000000;
            font-size: 18px;
            transition: all 0.2s;
            height: 100%;
            display: flex;
            align-items: center;
        }
        
        .main-search-btn:hover {
            color: #6041E0;
        }

        /* Live Search Suggestions Dropdown */
        .search-suggestions-dropdown {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: white;
            border: 1px solid #E5E7EB;
            border-radius: 12px;
            margin-top: 8px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            z-index: 1000;
            display: none;
            overflow: hidden;
            max-height: 400px;
            overflow-y: auto;
        }

        .search-suggestion-item {
            display: flex;
            align-items: center;
            padding: 12px 20px;
            gap: 16px;
            text-decoration: none;
            color: inherit;
            transition: background 0.2s;
            border-bottom: 1px solid #F3F4F6;
        }

        .search-suggestion-item:last-child {
            border-bottom: none;
        }

        .search-suggestion-item:hover, .search-suggestion-item.highlighted {
            background: #F9FAFB;
        }

        .suggestion-image {
            width: 60px;
            height: 40px;
            border-radius: 6px;
            object-fit: cover;
            background: #F3F4F6;
        }

        .suggestion-info {
            flex: 1;
        }

        .suggestion-title {
            font-size: 14px;
            font-weight: 700;
            color: #1A1D1C;
            margin-bottom: 2px;
            display: block;
        }

        .suggestion-price {
            font-size: 13px;
            color: #6041E0;
            font-weight: 700;
        }

        .no-results-suggestion {
            padding: 20px;
            text-align: center;
            color: #5E5E5E;
            font-size: 14px;
        }

        .searching-loader {
            padding: 12px 20px;
            color: #9CA3AF;
            font-size: 13px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        /* Search Divider */
        .search-divider {
            text-align: center;
            margin: 20px 0;
            color: #5E5E5E;
            font-size: 14px;
            font-weight: 400;
        }
        
        /* Filter Grid - Exact 2x3 Grid */
        .filter-grid-new {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            grid-template-rows: repeat(2, 1fr);
            gap: 12px;
        }
        
        .filter-group-new {
            position: relative;
        }
        
        /* Button stays in 3rd column, 2nd row */
        .filter-group-new.submit-group {
            grid-column: 3;
            grid-row: 2;
        }
        
        /* Floating Labels INSIDE Inputs - azalcars Style */
        .filter-label-new {
            position: absolute;
            top: 6px;
            left: 12px;
            font-size: 11px;
            font-weight: 700;
            color: #5E5E5E;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            pointer-events: none;
            z-index: 1;
            transition: color 0.2s;
        }
        
        .filter-select-new {
            width: 100%;
            height: 52px;
            padding: 20px 12px 6px 12px;
            font-family: 'DM Sans', sans-serif;
            font-size: 14px;
            font-weight: 700;
            border: 1px solid #A3AEAC;
            border-radius: 12px;
            background: white;
            cursor: pointer;
            outline: none;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%236041E0' d='M6 9L1 4h10z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 16px center;
            padding-right: 36px;
        }
        
        input.filter-select-new {
            background-image: none;
            padding-right: 12px;
        }
        
        .filter-select-new:hover {
            border-color: #6041E0;
            background-color: #F9FAFB;
        }
        
        .filter-select-new:focus {
            border-color: #6041E0;
            box-shadow: 0 0 0 4px rgba(96, 65, 224, 0.1);
        }
        
        .filter-group-new:focus-within .filter-label-new {
            color: #6041E0;
        }
        
        /* Button - Premium CTA Styling */
        .search-btn-new {
            width: 100%;
            height: 52px;
            padding: 0 24px;
            background: linear-gradient(135deg, #6041E0 0%, #4c30c4 100%);
            color: white;
            border: none;
            border-radius: 12px;
            font-family: 'DM Sans', sans-serif;
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 4px 12px rgba(96, 65, 224, 0.25);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }
        
        .search-btn-new:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(96, 65, 224, 0.35);
            background: linear-gradient(135deg, #6b4de3 0%, #5639d1 100%);
        }

        .search-btn-new:active {
            transform: translateY(0);
        }
        
        /* ========== DUAL PROMO SECTION ========== */
        .dual-promo-section {
            background: #ffffff;
            padding: 48px 0;
        }
        
        .dual-promo-container {
            max-width: 1280px;
            margin: 0 auto;
            padding: 0 24px;
        }
        
        .promo-grid-dual {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
        }
        
        .promo-card {
            border-radius: 12px;
            padding: 32px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            min-height: 280px;
            position: relative;
            overflow: hidden;
            border: 1px solid transparent;
        }
        
        .deals-card {
            background: #E8F5F5; /* Mint green */
        }
        
        .promo-news-card {
            background: #ffffff;
            border: 1px solid #E5E7EB;
        }
        
        .promo-content {
            flex: 1;
            z-index: 2;
        }
        
        .promo-label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: #1A1D1C;
            margin-bottom: 8px;
        }
        
        .promo-title {
            font-size: 24px;
            font-weight: 700;
            color: #1A1D1C;
            margin-bottom: 12px;
            line-height: 1.2;
        }
        
        .promo-text {
            font-size: 14px;
            color: #5E5E5E;
            margin-bottom: 24px;
            line-height: 1.5;
            max-width: 260px;
        }
        
        .promo-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 12px 24px;
            border-radius: 50px;
            font-size: 14px;
            font-weight: 700;
            text-decoration: none;
            transition: all 0.2s;
            cursor: pointer;
        }
        
        .promo-btn.black-solid {
            background: #1A1D1C;
            color: white;
            border: 1px solid #1A1D1C;
        }
        
        .promo-btn.black-solid:hover {
            background: #333333;
        }
        
        .promo-btn.black-outlined {
            background: transparent;
            color: #1A1D1C;
            border: 1px solid #1A1D1C;
            margin-bottom: 12px;
        }
        
        .promo-btn.black-outlined:hover {
            background: rgba(0, 0, 0, 0.05);
        }
        
        .promo-actions-stacked {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
        }
        
        .promo-graphic {
            flex: 0 0 auto;
            align-self: center;
            margin-left: 20px;
        }
        
        .graphic-circle {
            width: 140px;
            height: 140px;
            background: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }
        
        .graphic-circle img {
            width: 80%;
            height: auto;
        }
        
        .promo-graphic-news {
            position: absolute;
            bottom: 0px;
            right: 0px;
            width: 50%;
            height: 100%;
            display: flex;
            align-items: flex-end;
            justify-content: flex-end;
            pointer-events: none;
        }
        
        .promo-graphic-news img {
            max-width: 100%;
            max-height: 90%;
            object-fit: contain;
        }
        
        /* ========== TRENDING SEARCHES SECTION ========== */
        .trending-section {
            background: #F5F5F5;
            padding: 64px 0;
        }
        
        .trending-container {
            max-width: 1280px;
            margin: 0 auto;
            padding: 0 24px;
        }
        
        .trending-container h2 {
            margin-bottom: 32px;
        }
        
        .trending-tabs-wrapper {
            position: relative;
            margin-bottom: 24px;
            display: flex;
            align-items: center;
        }
        
        .trending-tabs {
            display: flex;
            gap: 12px;
            overflow-x: auto;
            scrollbar-width: none;
            -ms-overflow-style: none;
            padding-right: 48px;
        }
        
        .trending-tabs::-webkit-scrollbar {
            display: none;
        }
        
        .trending-tab {
            padding: 10px 20px;
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 50px;
            font-size: 14px;
            font-weight: 700;
            color: #1A1D1C;
            cursor: pointer;
            white-space: nowrap;
            transition: all 0.2s;
        }
        
        .trending-tab.active {
            background: #1A1D1C;
            color: #ffffff;
            border-color: #1A1D1C;
        }
        
        .scroll-arrow-trending {
            position: absolute;
            right: 0;
            width: 32px;
            height: 32px;
            background: #1A1D1C;
            color: white;
            border: none;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            z-index: 5;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        
        .cars-grid-trending {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 12px;
            margin-bottom: 24px;
        }
        
        .see-more-trending {
            font-size: 14px;
            font-weight: 700;
            color: #1A1D1C;
            text-decoration: underline;
            display: inline-block;
        }
        
        .cars-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
        }
        
        .car-card {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            transition: all 0.3s;
            text-decoration: none;
            color: inherit;
            display: block;
        }
        
        .car-card:hover {
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
        }
        
        .car-image-wrapper {
            position: relative;
            width: 100%;
            height: 200px;
            background: #f3f4f6;
            overflow: hidden;
        }
        
        .car-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .car-badge {
            position: absolute;
            top: 12px;
            left: 12px;
            background: #6041E0;
            color: white;
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 700;
        }
        
        .price-drop-badge {
            position: absolute;
            top: 12px;
            right: 12px;
            background: #10b981;
            color: white;
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 700;
        }
        
        .car-info {
            padding: 16px;
        }
        
        .car-title {
            font-size: 16px;
            font-weight: 700;
            color: #000000;
            margin-bottom: 8px;
            line-height: 1.3;
        }
        
        .car-price {
            font-size: 20px;
            font-weight: 700;
            color: #000000;
            margin-bottom: 8px;
        }
        
        .car-meta {
            font-size: 14px;
            color: #5E5E5E;
            margin-bottom: 4px;
        }
        
        .car-location {
            font-size: 13px;
            color: #9ca3af;
        }
        
        /* ========== POPULAR BRANDS SECTION ========== */
        .brands-section {
            background: #ffffff;
            padding: 64px 0;
            border-top: 1px solid #e5e7eb;
        }
        
        .brands-container {
            max-width: 1280px;
            margin: 0 auto;
            padding: 0 24px;
        }
        
        .brands-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
            gap: 20px;
            margin-top: 32px;
        }
        
        .brand-card {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 24px;
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            text-decoration: none;
            transition: all 0.2s;
        }
        
        .brand-card:hover {
            border-color: #6041E0;
            box-shadow: 0 4px 12px rgba(96, 65, 224, 0.08);
        }
        
        .brand-logo-wrapper {
            width: 64px;
            height: 64px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 12px;
        }
        
        .brand-logo-wrapper img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
        }
        
        .brand-name {
            font-size: 14px;
            font-weight: 700;
            color: #1A1D1C;
        }

        .see-more-brands {
            font-size: 14px;
            font-weight: 700;
            color: #1A1D1C;
            text-decoration: underline;
            display: inline-block;
            margin-top: 32px;
        }

        /* ========== RESPONSIVE ========== */
        @media (max-width: 1024px) {
            .filter-grid-new {
                grid-template-columns: repeat(2, 1fr);
                grid-template-rows: auto;
            }
            .filter-group-new.submit-group {
                grid-column: span 2;
                grid-row: auto;
            }
            .promo-grid-dual {
                grid-template-columns: 1fr;
            }
            .cars-grid {
                grid-template-columns: repeat(2, 1fr);
            }
            .search-tab {
                padding: 12px 20px;
                font-size: 15px;
            }
        }
        
        @media (max-width: 768px) {
            .filter-grid-new {
                grid-template-columns: 1fr;
            }
            .filter-group-new.submit-group {
                grid-column: 1;
            }
            .search-tabs {
                width: 100%;
                justify-content: center;
            }
            .search-tab {
                flex: 1;
                padding: 12px 10px;
                font-size: 14px;
                text-align: center;
            }
            .main-search-input {
                padding: 0 16px;
                font-size: 14px;
            }
            .trending-tabs {
                padding-right: 24px;
                overflow-x: auto;
                white-space: nowrap;
                -webkit-overflow-scrolling: touch;
            }
            .cars-grid-trending {
                grid-template-columns: repeat(2, 1fr);
            }
            .cars-grid {
                grid-template-columns: 1fr;
            }
            .promo-card {
                padding: 24px;
                flex-direction: column;
                align-items: flex-start;
                min-height: auto;
            }
            .promo-graphic, .promo-graphic-news {
                position: static;
                width: 100%;
                height: 120px;
                margin: 20px 0 0 0;
                justify-content: center;
            }
            .hero-container h1 {
                font-size: 28px !important;
            }
            .main-search-wrapper {
                height: 48px;
            }
            .category-card.card-large {
                grid-column: span 3;
            }
            .categories-grid {
                grid-template-columns: repeat(3, 1fr);
            }
        }

        @media (max-width: 480px) {
            .cars-grid-trending {
                grid-template-columns: 1fr;
            }
            .categories-grid {
                grid-template-columns: repeat(2, 1fr);
            }
            .category-card.card-large {
                grid-column: span 2;
            }
        }

        /* Sell Way & News Section Responsiveness */
        .sell-way-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 40px;
            flex-wrap: wrap;
        }
        .sell-options-wrapper {
            flex: 1;
            display: flex;
            align-items: stretch;
            gap: 32px;
        }
        .sell-option {
            flex: 1;
            display: flex;
            gap: 16px;
        }
        .sell-divider {
            position: relative;
            width: 1px;
            background: #e5e7eb;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .sell-divider-text {
            position: absolute;
            background: #F5F5F5;
            padding: 4px 0;
            color: #6b7280;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
        }
        .sell-illustration {
            flex: 0 0 320px;
            text-align: right;
        }

        @media (max-width: 992px) {
            .sell-options-wrapper {
                width: 100%;
                flex-direction: column;
                gap: 40px;
            }
            .sell-divider {
                width: 100%;
                height: 1px;
            }
            .sell-divider-text {
                padding: 0 8px;
            }
            .sell-illustration {
                display: none;
            }
            .news-layout {
                grid-template-columns: 1fr !important;
                gap: 30px !important;
            }
            .featured-news div:first-child {
                height: 240px !important;
            }
        }

        @media (max-width: 480px) {
            .side-news-item {
                flex-direction: column;
                align-items: flex-start !important;
                gap: 12px !important;
            }
            .side-news-item div:first-child {
                width: 100% !important;
                height: 180px !important;
            }
        }
    </style>
</head>
<body>
    @include('partials.header')

    <!-- HERO SECTION -->
    <section class="hero-section">
        <div class="hero-container">
            <!-- New Search Tabs -->
            <div class="search-tabs">
                <button class="search-tab active" data-tab="shop">Shop cars for sale</button>
                <button class="search-tab" data-tab="sell">Sell your car</button>
            </div>

            <!-- Shop Tab Content -->
            <div id="shop-content" class="tab-content active">
                <!-- Main Search Bar -->
                <div class="main-search-container">
                    <form action="{{ route('listings.search') }}" method="GET" class="main-search-wrapper-form">
                        <div class="main-search-wrapper">
                            <input 
                                type="text" 
                                name="q"
                                class="main-search-input" 
                                placeholder="Search over 500,000 new and used cars"
                                autocomplete="off"
                            >
                            <button type="submit" class="main-search-btn">
                                <i class="fas fa-search"></i>
                            </button>
                            
                            <!-- Search Suggestions Dropdown -->
                            <div class="search-suggestions-dropdown" id="searchSuggestions">
                                <!-- Results will be dynamically populated -->
                            </div>
                        </div>
                    </form>
                </div>
                
                <!-- Divider -->
                <div class="search-divider">
                    <span>- Or search by -</span>
                </div>
                
                <!-- Filter Grid -->
                <form action="{{ route('listings.search') }}" method="GET" class="main-search-form">
                    <div class="filter-grid-new">
                        <div class="filter-group-new">
                            <label class="filter-label-new">New/used</label>
                            <select name="condition" class="filter-select-new">
                                <option value="">New & used</option>
                                <option value="new">New</option>
                                <option value="used">Used</option>
                            </select>
                        </div>
                        
                        <div class="filter-group-new">
                            <label class="filter-label-new">Make</label>
                            <select name="make" class="filter-select-new">
                                <option value="">All Makes</option>
                                @foreach($makes->take(20) as $make)
                                    <option value="{{ $make->slug }}">{{ $make->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="filter-group-new">
                            <label class="filter-label-new">Model</label>
                            <select name="model" class="filter-select-new">
                                <option value="">All Models</option>
                            </select>
                        </div>
                        
                        <div class="filter-group-new">
                            <label class="filter-label-new">YEAR</label>
                            <select name="year" class="filter-select-new">
                                <option value="">All Years</option>
                                @if(isset($availableYears) && !empty($availableYears))
                                    @foreach($availableYears as $year)
                                        <option value="{{ $year }}">{{ $year }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>

                        <div class="filter-group-new">
                            <label class="filter-label-new">MILEAGE</label>
                            <select name="max_mileage" class="filter-select-new">
                                <option value="">All Mileage</option>
                                <option value="10000">10 Miles</option>
                                <option value="20000">20 Miles</option>
                                <option value="30000">30 Miles</option>
                                <option value="40000">40 Miles</option>
                                <option value="50000">50 Miles</option>
                                <option value="60000">60 Miles</option>
                                <option value="70000">70 Miles</option>
                                <option value="80000">80 Miles</option>
                                <option value="90000">90 Miles</option>
                                <option value="100000">100 Miles</option>
                                <option value="150000">150 Miles</option>
                                <option value="200000">200 Miles</option>
                                <option value="250000">250 Miles</option>
                                <option value="500000">500 Miles</option>
                            </select>
                        </div>

                        <div class="filter-group-new submit-group">
                            <button type="submit" class="search-btn-new">Show matches</button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Sell Tab Content -->
            <div id="sell-content" class="tab-content" style="display: none;">
                <form action="{{ route('listings.create') }}" method="GET" class="sell-form">
                    <!-- Toggle Switch -->
                    <div class="toggle-switch-container">
                        <div class="toggle-switch">
                            <input type="radio" id="toggle-plate" name="search_type" value="plate" checked class="toggle-input">
                            <label for="toggle-plate" class="toggle-label">License plate</label>
                            
                            <input type="radio" id="toggle-vin" name="search_type" value="vin" class="toggle-input">
                            <label for="toggle-vin" class="toggle-label">VIN</label>
                            
                            <div class="toggle-slider"></div>
                        </div>
                    </div>

                    <!-- Input Fields -->
                    <div class="sell-inputs-row">
                        <div class="sell-input-group plate-group">
                            <input type="text" name="license_plate" placeholder="License plate" class="filter-select-new" style="background-image: none; padding-right: 12px;">
                        </div>
                        <div class="sell-input-group plate-group" style="max-width: 120px;">
                            <select name="state" class="filter-select-new">
                                <option value="">State</option>
                                <option value="AL">AL</option>
                                <option value="AK">AK</option>
                                <option value="AZ">AZ</option>
                                <option value="AR">AR</option>
                                <option value="CA">CA</option>
                                <!-- Add more states as needed -->
                            </select>
                        </div>
                        
                        <div class="sell-input-group vin-group" style="display: none; flex: 1;">
                            <input type="text" name="vin" placeholder="VIN" class="filter-select-new" style="background-image: none; padding-right: 12px;">
                        </div>
                    </div>

                    <!-- Radio Options -->
                    <div class="estimate-options">
                        <p class="estimate-label">Estimate car value for:</p>
                        <div class="radio-option">
                            <input type="radio" id="instant-offer" name="estimate_type" value="instant" checked>
                            <label for="instant-offer">Instant offer</label>
                        </div>
                        <div class="radio-option">
                            <input type="radio" id="sell-yourself" name="estimate_type" value="self">
                            <label for="sell-yourself">Selling it yourself on azalcars.com</label>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="search-btn-new" style="background: #8b5cf6; margin-top: 24px;">
                        Get estimate
                    </button>
                    
                    <p class="disclaimer-text">
                        By clicking here, you authorize azalcars.com to continue with collecting your information. We only save this data to provide you a listing to sell your car. We value and protect your privacy. <a href="{{ route('pages.privacy') }}">Privacy Notice</a>
                    </p>
                </form>
            </div>
        </div>
    </section>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Tab Switching
            const tabs = document.querySelectorAll('.search-tab');
            const contents = document.querySelectorAll('.tab-content');

            tabs.forEach(tab => {
                tab.addEventListener('click', () => {
                    // Remove active class from all
                    tabs.forEach(t => t.classList.remove('active'));
                    contents.forEach(c => {
                        c.classList.remove('active');
                        c.style.display = 'none';
                    });

                    // Add active class to clicked
                    tab.classList.add('active');
                    const targetId = tab.getAttribute('data-tab') + '-content';
                    const targetContent = document.getElementById(targetId);
                    if (targetContent) {
                        targetContent.classList.add('active');
                        targetContent.style.display = 'block';
                    }
                });
            });

            // Sell Form Toggle (Plate vs VIN)
            const typeRadios = document.querySelectorAll('input[name="search_type"]');
            const plateGroups = document.querySelectorAll('.plate-group');
            const vinGroup = document.querySelector('.vin-group');

            typeRadios.forEach(radio => {
                radio.addEventListener('change', (e) => {
                    if (e.target.value === 'plate') {
                        plateGroups.forEach(g => g.style.display = 'block');
                        vinGroup.style.display = 'none';
                        // Move slider logic if using CSS slider, handled by :checked usually
                    } else {
                        plateGroups.forEach(g => g.style.display = 'none');
                        vinGroup.style.display = 'block';
                    }
                });
            });
        });
    </script>
    
    <style>
        /* New Styles for Tabs & Sell Form */
        .toggle-switch-container {
            display: flex;
            margin-bottom: 20px;
        }
        
        .toggle-switch {
            position: relative;
            background: #ffffff;
            border: 1px solid #1a1a1a;
            border-radius: 50px;
            display: flex;
            overflow: hidden;
            width: fit-content;
        }
        
        .toggle-input {
            display: none;
        }
        
        .toggle-label {
            padding: 10px 24px;
            cursor: pointer;
            z-index: 2;
            font-weight: 700;
            font-size: 14px;
            transition: color 0.3s;
        }
        
        .toggle-input:checked + .toggle-label {
            color: #ffffff;
            background: #1a1a1a;
        }
        
        .sell-inputs-row {
            display: flex;
            gap: 12px;
            margin-bottom: 24px;
        }
        
        .sell-input-group {
            flex: 1;
        }
        
        .estimate-options {
            margin-bottom: 24px;
        }
        
        .estimate-label {
            font-size: 16px;
            font-weight: 400;
            margin-bottom: 12px;
            color: #1a1a1a;
        }
        
        .radio-option {
            display: flex;
            align-items: center;
            margin-bottom: 12px;
        }
        
        .radio-option input[type="radio"] {
            appearance: none;
            width: 20px;
            height: 20px;
            border: 2px solid #1a1a1a;
            border-radius: 50%;
            margin-right: 12px;
            position: relative;
            cursor: pointer;
        }
        
        .radio-option input[type="radio"]:checked::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 10px;
            height: 10px;
            background: #1a1a1a;
            border-radius: 50%;
        }

        .radio-option label {
            font-size: 16px;
            color: #1a1a1a;
            cursor: pointer;
        }
        
        .disclaimer-text {
            font-size: 12px;
            color: #6b7280;
            margin-top: 16px;
            line-height: 1.5;
        }
        
        .disclaimer-text a {
            color: #1a1a1a;
            text-decoration: underline;
            font-weight: 700;
        }
    </style>

    <!-- DUAL PROMO SECTION -->
    <section class="dual-promo-section">
        <div class="dual-promo-container">
            <div class="promo-grid-dual">
                <!-- Left Card: Deals -->
                <div class="promo-card deals-card">
                    <div class="promo-content">
                        <h2 class="promo-title">Top deals near you</h2>
                        <p class="promo-text">See cars in your area that are specifically priced to sell fast.</p>
                        <a href="{{ route('listings.search') }}" class="promo-btn black-solid">Shop deals</a>
                    </div>
                    <div class="promo-graphic">
                        <div class="graphic-circle">
                            <img src="{{ asset('assets/images/deals_illustration.png') }}" alt="Deals Illustration">
                        </div>
                    </div>
                </div>

                <!-- Right Card: News -->
                <div class="promo-card promo-news-card">
                    <div class="promo-content">
                        <span class="promo-label">azalcars news</span>
                        <h2 class="promo-title">Latest Global Car Trends</h2>
                        <p class="promo-text">We analyze the global market to determine which new cars offer the best value.</p>
                        <div class="promo-actions-stacked">
                            <a href="{{ route('news.index') }}" class="promo-btn black-outlined">View All News</a>
                            <a href="{{ route('listings.search') }}" class="promo-btn black-solid">Shop all cars</a>
                        </div>
                    </div>
                    <div class="promo-graphic-news">
                        <img src="{{ asset('assets/images/news_promotion.png') }}" alt="Car News Promotion">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- TRENDING SEARCHES SECTION -->
    <section class="trending-section">
        <div class="trending-container">
            <h2 class="section-title">Trending searches near you</h2>
            
            <div class="trending-tabs-wrapper">
                <div class="trending-tabs" id="trendingTabs">
                    <button class="trending-tab active" data-target="grid-under5k">Used Under $5K</button>
                    <button class="trending-tab" data-target="grid-challenger">Dodge Challenger 2020+ Under $80K</button>
                    <button class="trending-tab" data-target="grid-porsche">Porsche 911</button>
                    <button class="trending-tab" data-target="grid-suv">Used SUV</button>
                    <button class="trending-tab" data-target="grid-mercedes">Mercedes-Benz</button>
                    <button class="trending-tab" data-target="grid-corvette">Used Chevrolet Corvette Convertible</button>
                    <button class="trending-tab" data-target="grid-genesis">Genesis</button>
                </div>
                <button class="scroll-arrow-trending" id="scrollTabsBtn">
                    <i class="fas fa-arrow-right"></i>
                </button>
            </div>

            <!-- Content Grids -->
            <div class="trending-content">
                <!-- Grid: Under $5K -->
                <div class="trending-grid-wrapper active" id="grid-under5k">
                    <div class="cars-grid-trending">
                        @foreach($latestListings->take(10) as $listing)
                        <a href="{{ route('listings.show', $listing->slug) }}" class="car-card">
                            <div class="car-image-wrapper" style="height: 140px;">
                                @if($listing->main_image)
                                    <img src="{{ $listing->main_image }}" alt="{{ $listing->title }}" class="car-image">
                                @else
                                    <div class="car-image" style="display: flex; align-items: center; justify-content: center;">
                                        <i class="fas fa-car" style="font-size: 32px; color: #d1d5db;"></i>
                                    </div>
                                @endif
                            </div>
                            <div class="car-info">
                                @if($loop->index < 2)
                                <div class="news-category" style="font-size: 10px; margin-bottom: 2px; color: #5E5E5E; font-weight: normal; text-transform: none;">Sponsored</div>
                                @endif
                                <h3 class="car-title" style="font-size: 14px; margin-bottom: 4px; font-weight: 700;">{{ Str::limit($listing->title, 25) }}</h3>
                                <div class="car-price" style="font-size: 18px; font-weight: 700; margin-bottom: 4px;">{!! \App\Helpers\Helpers::formatPrice($listing->price) !!}</div>
                                <div class="car-meta" style="font-size: 12px; color: #5E5E5E;">{{ number_format($listing->mileage) }} mi.</div>
                            </div>
                        </a>
                        @endforeach
                    </div>
                    <a href="{{ route('listings.search') }}?condition=used&max_price=5000" class="see-more-trending">See more Used Under $5K</a>
                </div>

                <!-- Grid: Challenger -->
                <div class="trending-grid-wrapper" id="grid-challenger" style="display: none;">
                    <div class="cars-grid-trending">
                        @foreach($latestListings->reverse()->take(10) as $listing)
                        <a href="{{ route('listings.show', $listing->slug) }}" class="car-card">
                            <div class="car-image-wrapper" style="height: 140px;">
                                @if($listing->main_image)
                                    <img src="{{ $listing->main_image }}" alt="{{ $listing->title }}" class="car-image">
                                @else
                                    <div class="car-image" style="display: flex; align-items: center; justify-content: center;">
                                        <i class="fas fa-car" style="font-size: 32px; color: #d1d5db;"></i>
                                    </div>
                                @endif
                            </div>
                            <div class="car-info">
                                <h3 class="car-title" style="font-size: 14px; margin-bottom: 4px; font-weight: 700;">{{ Str::limit($listing->title, 25) }}</h3>
                                <div class="car-price" style="font-size: 18px; font-weight: 700; margin-bottom: 4px;">{!! \App\Helpers\Helpers::formatPrice($listing->price) !!}</div>
                                <div class="car-meta" style="font-size: 12px; color: #5E5E5E;">{{ number_format($listing->mileage) }} mi.</div>
                            </div>
                        </a>
                        @endforeach
                    </div>
                    <a href="{{ route('listings.search') }}?q=Dodge+Challenger&year=2020&max_price=80000" class="see-more-trending">See more Dodge Challenger 2020+ Under $80K</a>
                </div>

                <!-- (Mocking other grids by showing the same with different skip values if skipped listings exist) -->
                @foreach(['porsche', 'suv', 'mercedes', 'corvette', 'genesis'] as $tab)
                <div class="trending-grid-wrapper" id="grid-{{ $tab }}" style="display: none;">
                    <div class="cars-grid-trending">
                        @foreach($latestListings->shuffle()->take(10) as $listing)
                        <a href="{{ route('listings.show', $listing->slug) }}" class="car-card">
                            <div class="car-image-wrapper" style="height: 140px;">
                                @if($listing->main_image)
                                    <img src="{{ $listing->main_image }}" alt="{{ $listing->title }}" class="car-image">
                                @else
                                    <div class="car-image" style="display: flex; align-items: center; justify-content: center;">
                                        <i class="fas fa-car" style="font-size: 32px; color: #d1d5db;"></i>
                                    </div>
                                @endif
                            </div>
                            <div class="car-info">
                                <h3 class="car-title" style="font-size: 14px; margin-bottom: 4px; font-weight: 700;">{{ Str::limit($listing->title, 25) }}</h3>
                                <div class="car-price" style="font-size: 18px; font-weight: 700; margin-bottom: 4px;">{!! \App\Helpers\Helpers::formatPrice($listing->price) !!}</div>
                                <div class="car-meta" style="font-size: 12px; color: #5E5E5E;">{{ number_format($listing->mileage) }} mi.</div>
                            </div>
                        </a>
                        @endforeach
                    </div>
                    <a href="{{ route('listings.search') }}" class="see-more-trending">See more Results</a>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- POPULAR BRANDS SECTION -->
    <section class="brands-section">
        <div class="brands-container">
            <h2 class="section-title">Shop by brand</h2>
            <div class="brands-grid">
                @php
                    $brands = [
                        // Korean brands first
                        ['name' => 'Hyundai', 'logo' => 'hyundai.png'],
                        ['name' => 'Kia', 'logo' => 'kia.png'],
                        ['name' => 'Daewoo', 'logo' => 'daewoo.png'],
                        ['name' => 'SsangYong', 'logo' => 'ssangyong.png'],
                        ['name' => 'Renault Samsung', 'logo' => 'renault-samsung.png'],
                        // Other brands
                        ['name' => 'Toyota', 'logo' => 'toyota.png'],
                        ['name' => 'Ford', 'logo' => 'ford.png'],
                        ['name' => 'Chevrolet', 'logo' => 'chevrolet.png'],
                        ['name' => 'Honda', 'logo' => 'honda.png'],
                        ['name' => 'Nissan', 'logo' => 'nissan.png'],
                        ['name' => 'Jeep', 'logo' => 'jeep.png'],
                        ['name' => 'Audi', 'logo' => 'audi.png'],
                        ['name' => 'BMW', 'logo' => 'bmw.png'],
                        ['name' => 'Mercedes-Benz', 'logo' => 'mercedes-benz.png'],
                        ['name' => 'Volkswagen', 'logo' => 'volkswagen.png'],
                        ['name' => 'Lexus', 'logo' => 'lexus.png'],
                        ['name' => 'Subaru', 'logo' => 'subaru.png'],
                    ];
                @endphp
                
                @foreach($brands as $brand)
                <a href="{{ route('listings.search', ['brand' => strtolower($brand['name'])]) }}" class="brand-card">
                    <div class="brand-logo-wrapper">
                        <img src="{{ asset('assets/images/brands/' . $brand['logo']) }}" alt="{{ $brand['name'] }} Logo">
                    </div>
                    <span class="brand-name">{{ $brand['name'] }}</span>
                </a>
                @endforeach
            </div>
            <a href="{{ route('brands.index') }}" class="see-more-brands">Shop all brands</a>
        </div>
    </section>

    <!-- SELL YOUR CAR SECTION (New Layout) -->
    <section class="sell-way-section" style="padding: 60px 0; background: #F5F5F5;">
        <div class="container" style="max-width: 1280px; margin: 0 auto; padding: 0 24px;">
            <h2 class="section-title" style="margin-bottom: 40px; font-size: 28px; font-weight: 800; color: #1a1a1a;">Sell your car your way</h2>
            
            <div class="sell-way-container">
                <!-- Left Side Options Container -->
                <div class="sell-options-wrapper">
                    
                    <!-- Option 1: Get an offer -->
                    <div class="sell-option">
                        <div style="flex-shrink: 0; width: 72px; height: 72px; display: flex; align-items: center; justify-content: center;">
                            <img src="{{ asset('assets/images/icon_calculator.png') }}" alt="Calculator" style="width: 72px; height: 72px;">
                        </div>
                        <div style="display: flex; flex-direction: column; align-items: flex-start;">
                            <h4 style="font-weight: 700; font-size: 16px; margin-bottom: 8px; color: #1a1a1a;">Get an offer</h4>
                            <p style="font-size: 14px; color: #4b5563; margin-bottom: 16px; line-height: 1.5;">Get a free instant cash offer and sell your car in as little as 24 hours.</p>
                            <a href="{{ route('listings.create') }}" style="display: inline-block; padding: 8px 20px; border: 1px solid #1a1a1a; border-radius: 20px; font-weight: 700; font-size: 14px; color: #1a1a1a; text-decoration: none; transition: background 0.2s;">Get your offer</a>
                        </div>
                    </div>

                    <!-- Divider -->
                    <div class="sell-divider">
                        <span class="sell-divider-text">OR</span>
                    </div>

                    <!-- Option 2: List for free -->
                    <div class="sell-option">
                        <div style="flex-shrink: 0; width: 72px; height: 72px; display: flex; align-items: center; justify-content: center;">
                            <img src="{{ asset('assets/images/icon_list.png') }}" alt="List" style="width: 72px; height: 72px;">
                        </div>
                        <div style="display: flex; flex-direction: column; align-items: flex-start;">
                            <h4 style="font-weight: 700; font-size: 16px; margin-bottom: 8px; color: #1a1a1a;">List for free</h4>
                            <p style="font-size: 14px; color: #4b5563; margin-bottom: 16px; line-height: 1.5;">Create a free listing and reach millions of car shoppers on azalcars.</p>
                            <a href="{{ route('listings.create') }}" style="display: inline-block; padding: 8px 20px; border: 1px solid #452276; border-radius: 20px; font-weight: 700; font-size: 14px; color: #452276; text-decoration: none; transition: background 0.2s;">List your car</a>
                        </div>
                    </div>

                </div>

                <!-- Right Side Illustration -->
                <div class="sell-illustration">
                     <img src="{{ asset('assets/images/sell_illustration.png') }}" alt="Sell your car" style="max-width: 100%; height: auto;">
                </div>
            </div>
        </div>
    </section>

    <!-- NEWS & REVIEWS (Redesigned) -->
    <section class="news-section" style="background: #ffffff; padding: 60px 0;">
        <div class="news-container" style="max-width: 1280px; margin: 0 auto; padding: 0 24px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
                <h2 class="section-title" style="font-size: 24px; font-weight: 700; color: #1a1a1a; margin: 0;">Latest news</h2>
                <a href="{{ route('news.index') }}" style="font-weight: 700; color: #452276; text-decoration: none;">See all news</a>
            </div>
            
            <div class="news-layout" style="display: grid; grid-template-columns: 1.2fr 0.8fr; gap: 40px; align-items: start;">
                <!-- Left: Featured Article -->
                @if(isset($latestNews) && $latestNews->count() > 0)
                    @php $featuredNews = $latestNews->first(); @endphp
                    <a href="{{ route('news.show', $featuredNews->slug) }}" class="featured-news" style="display: block; text-decoration: none; group;">
                        <div style="height: 360px; overflow: hidden; border-radius: 12px; margin-bottom: 20px;">
                            @php
                                $fNewsImg = $featuredNews->image;
                                if ($fNewsImg && !Str::startsWith($fNewsImg, 'http')) {
                                    $fNewsImg = Str::startsWith($fNewsImg, '/storage') ? asset($fNewsImg) : asset('storage/' . $fNewsImg);
                                }
                                $fNewsDisplay = $fNewsImg ?? 'https://images.unsplash.com/photo-1533473359331-0135ef1b58bf?auto=format&fit=crop&w=1200&q=80';
                            @endphp
                            <img src="{{ $fNewsDisplay }}" alt="Featured News" style="width: 100%; height: 100%; object-fit: cover; transition: transform 0.3s ease;">
                        </div>
                        <div>
                            <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 12px;">
                                <span style="color: #6041E0; font-size: 13px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;">{{ $featuredNews->category ?? 'News' }}</span>
                                <span style="color: #6b7280; font-size: 13px;">•</span>
                                <span style="color: #6b7280; font-size: 13px;">{{ $featuredNews->created_at->format('M j, Y') }}</span>
                            </div>
                            <h3 style="color: #1a1a1a; font-size: 28px; font-weight: 700; line-height: 1.3; margin-bottom: 12px;">{{ $featuredNews->title }}</h3>
                            <p style="color: #4b5563; font-size: 16px; line-height: 1.6; margin-bottom: 0;">{{ Str::limit($featuredNews->excerpt ?? $featuredNews->content, 150) }}</p>
                        </div>
                    </a>
                @else
                    <!-- Fallback static content -->
                    <a href="{{ route('news.index') }}" class="featured-news" style="display: block; text-decoration: none; group;">
                        <div style="height: 360px; overflow: hidden; border-radius: 12px; margin-bottom: 20px;">
                            <img src="https://images.unsplash.com/photo-1533473359331-0135ef1b58bf?auto=format&fit=crop&w=1200&q=80" alt="Featured News" style="width: 100%; height: 100%; object-fit: cover; transition: transform 0.3s ease;">
                        </div>
                        <div>
                            <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 12px;">
                                <span style="color: #6041E0; font-size: 13px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;">Expert Review</span>
                                <span style="color: #6b7280; font-size: 13px;">•</span>
                                <span style="color: #6b7280; font-size: 13px;">Jan 17, 2026</span>
                            </div>
                            <h3 style="color: #1a1a1a; font-size: 28px; font-weight: 700; line-height: 1.3; margin-bottom: 12px;">How Much Is the 2026 Ford Bronco Sport? Prices Start at $31,590</h3>
                            <p style="color: #4b5563; font-size: 16px; line-height: 1.6; margin-bottom: 0;">The refreshed 2026 Ford Bronco Sport gets a new Sasquatch off-road package, updated tech and interior tweaks. Here's what it costs.</p>
                        </div>
                    </a>
                @endif

                <!-- Right: Side Articles List -->
                <div class="side-news-list" style="display: flex; flex-direction: column; gap: 24px;">
                    @if(isset($latestNews) && $latestNews->count() > 1)
                        @php $sideNews = $latestNews->skip(1); @endphp
                        @foreach($sideNews->take(3) as $news)
                            <a href="{{ route('news.show', $news->slug) }}" class="side-news-item" style="display: flex; gap: 20px; text-decoration: none; align-items: center;">
                                <div style="flex-shrink: 0; width: 140px; height: 90px; border-radius: 8px; overflow: hidden;">
                                      @php
                                          $newsImg = $news->image;
                                          if ($newsImg && !Str::startsWith($newsImg, 'http')) {
                                              $newsImg = Str::startsWith($newsImg, '/storage') ? asset($newsImg) : asset('storage/' . $newsImg);
                                          }
                                          $newsDisplay = $newsImg ?? 'https://images.unsplash.com/photo-1606220838315-056192d5e927?auto=format&fit=crop&w=400&q=80';
                                      @endphp
                                      <img src="{{ $newsDisplay }}" alt="News" style="width: 100%; height: 100%; object-fit: cover;">
                                </div>
                                <div>
                                    <div style="color: #6041E0; font-size: 11px; font-weight: 700; text-transform: uppercase; margin-bottom: 4px;">{{ $news->category ?? 'News' }}</div>
                                    <h4 style="color: #1a1a1a; font-size: 16px; font-weight: 700; line-height: 1.4; margin: 0;">{{ $news->title }}</h4>
                                </div>
                            </a>
                        @endforeach
                    @else
                        <!-- Side 1 -->
                        <a href="{{ route('news.index') }}" class="side-news-item" style="display: flex; gap: 20px; text-decoration: none; align-items: center;">
                            <div style="flex-shrink: 0; width: 140px; height: 90px; border-radius: 8px; overflow: hidden;">
                                  <img src="https://images.azalcars.com/cldstatic/wp-content/uploads/mercedes-eq-eqe-500-4matic-sedan-2025-exterior-oem-01.jpg" alt="News" style="width: 100%; height: 100%; object-fit: cover;">
                            </div>
                            <div>
                                <div style="color: #6041E0; font-size: 11px; font-weight: 700; text-transform: uppercase; margin-bottom: 4px;">News</div>
                                <h4 style="color: #1a1a1a; font-size: 16px; font-weight: 700; line-height: 1.4; margin: 0;">Mercedes-Benz Restarts Production of EQE, EQS EVs After Pause</h4>
                            </div>
                        </a>

                        <!-- Side 2 -->
                        <a href="{{ route('news.index') }}" class="side-news-item" style="display: flex; gap: 20px; text-decoration: none; align-items: center;">
                            <div style="flex-shrink: 0; width: 140px; height: 90px; border-radius: 8px; overflow: hidden;">
                                  <img src="https://images.azalcars.com/cldstatic/wp-content/uploads/ram-2500-power-wagon-2027-05-exterior-profile.jpg?w=800" alt="News" style="width: 100%; height: 100%; object-fit: cover;">
                            </div>
                            <div>
                                <div style="color: #6041E0; font-size: 11px; font-weight: 700; text-transform: uppercase; margin-bottom: 4px;">Review</div>
                                <h4 style="color: #1a1a1a; font-size: 16px; font-weight: 700; line-height: 1.4; margin: 0;">2027 Ram 2500 Power Wagon: Doing What They Said Couldn't Be Done</h4>
                            </div>
                        </a>

                        <!-- Side 3 -->
                        <a href="{{ route('news.index') }}" class="side-news-item" style="display: flex; gap: 20px; text-decoration: none; align-items: center;">
                            <div style="flex-shrink: 0; width: 140px; height: 90px; border-radius: 8px; overflow: hidden;">
                                  <img src="https://images.unsplash.com/photo-1606220838315-056192d5e927?auto=format&fit=crop&w=400&q=80" alt="News" style="width: 100%; height: 100%; object-fit: cover;">
                            </div>
                            <div>
                                <div style="color: #6041E0; font-size: 11px; font-weight: 700; text-transform: uppercase; margin-bottom: 4px;">Analysis</div>
                                <h4 style="color: #1a1a1a; font-size: 16px; font-weight: 700; line-height: 1.4; margin: 0;">Here Are the 10 Cheapest Pickup Trucks You Can Buy Right Now</h4>
                            </div>
                        </a>
                    @endif


                </div>
            </div>
        </div>
    </section>

    <!-- POPULAR SEARCHES (Accordion) -->
    <section class="popular-searches-section" style="background: #F2F2F2; padding: 60px 0;">
        <div class="container" style="max-width: 1280px; margin: 0 auto; padding: 0 24px;">
            <h2 class="section-title" style="margin-bottom: 32px; font-size: 24px; font-weight: 700;">Popular searches</h2>
            
            <div class="accordion-list">
                @php
                    $popularSearches = \App\Helpers\Helpers::getPopularSearches();
                @endphp
                @foreach($popularSearches as $title => $links)
                <div class="accordion-item" style="border-bottom: 1px solid #ddd; padding: 20px 0; cursor: pointer;">
                    <div class="accordion-header" style="display: flex; justify-content: space-between; align-items: center;">
                        <span style="font-weight: 700; font-size: 16px;">{{ $title }}</span>
                        <i class="fas fa-chevron-down" style="color: #1a1a1a; transition: transform 0.3s;"></i>
                    </div>
                    <div class="accordion-content" style="display: none; padding-top: 16px; color: #555; font-size: 14px;">
                         @if(count($links) > 0)
                            <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 12px;">
                                @foreach($links as $link)
                                    <a href="{{ $link['url'] }}" style="color: #4b5563; text-decoration: none; hover:text-decoration: underline;">{{ $link['label'] }}</a>
                                @endforeach
                            </div>
                         @else
                            <p style="color: #9ca3af; font-style: italic;">No popular searches found in this category.</p>
                         @endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    @include('partials.footer')

    <script>
        // Tab switching functionality
        document.querySelectorAll('.search-tab').forEach(tab => {
            tab.addEventListener('click', function() {
                document.querySelectorAll('.search-tab').forEach(t => t.classList.remove('active'));
                this.classList.add('active');
            });
        });
        
        // Trending tabs switching
        document.querySelectorAll('.trending-tab').forEach(tab => {
            tab.addEventListener('click', function() {
                document.querySelectorAll('.trending-tab').forEach(t => t.classList.remove('active'));
                this.classList.add('active');
                
                // Show target grid
                const target = this.getAttribute('data-target');
                document.querySelectorAll('.trending-grid-wrapper').forEach(grid => {
                    grid.style.display = 'none';
                    grid.classList.remove('active');
                });
                
                const activeGrid = document.getElementById(target);
                if (activeGrid) {
                    activeGrid.style.display = 'block';
                    activeGrid.classList.add('active');
                }
            });
        });

        // Trending tabs horizontal scroll
        const trendingTabs = document.getElementById('trendingTabs');
        const scrollBtn = document.getElementById('scrollTabsBtn');
        
        if (scrollBtn && trendingTabs) {
            scrollBtn.addEventListener('click', () => {
                trendingTabs.scrollBy({ left: 200, behavior: 'smooth' });
            });
            
            trendingTabs.addEventListener('scroll', () => {
                const maxScroll = trendingTabs.scrollWidth - trendingTabs.clientWidth;
                if (trendingTabs.scrollLeft >= maxScroll - 5) {
                    scrollBtn.style.opacity = '0';
                    scrollBtn.style.pointerEvents = 'none';
                } else {
                    scrollBtn.style.opacity = '1';
                    scrollBtn.style.pointerEvents = 'all';
                }
            });
        }

        // Accordion functionality for Popular Searches
        document.querySelectorAll('.accordion-item').forEach(item => {
            item.addEventListener('click', () => {
                const content = item.querySelector('.accordion-content');
                const icon = item.querySelector('.fa-chevron-down');

                // Toggle current
                if (content.style.display === 'block') {
                    content.style.display = 'none';
                    icon.style.transform = 'rotate(0deg)';
                } else {
                    content.style.display = 'block';
                    icon.style.transform = 'rotate(180deg)';
                }
            });
        });

        // Dynamic search count update
        function updateSearchCount() {
            const form = document.querySelector('.main-search-form');
            if (!form) return;

            const formData = new FormData(form);
            const params = new URLSearchParams();

            // Convert FormData to URLSearchParams
            for (let [key, value] of formData.entries()) {
                if (value && value.trim() !== '') {
                    params.append(key, value);
                }
            }

            fetch('/api/search/count?' + params.toString())
                .then(response => response.json())
                .then(data => {
                    const button = document.querySelector('.search-btn-new');
                    if (button && data.count !== undefined) {
                        const count = data.count.toLocaleString();
                        button.textContent = `Show ${count} matches`;
                    }
                })
                .catch(error => {
                    console.error('Error fetching search count:', error);
                });
        }

        // Update count when inputs change
        document.querySelectorAll('.main-search-form input, .main-search-form select').forEach(input => {
            input.addEventListener('input', updateSearchCount);
            input.addEventListener('change', updateSearchCount);
        });

        // Dynamic model loading for home page search
        const homeMakeSelect = document.querySelector('.main-search-form select[name="make"]');
        const homeModelSelect = document.querySelector('.main-search-form select[name="model"]');

        if (homeMakeSelect && homeModelSelect) {
            homeMakeSelect.addEventListener('change', function() {
                const makeSlug = this.value;
                homeModelSelect.innerHTML = '<option value="">All Models</option>';

                if (makeSlug) {
                    fetch(`/api/models/${makeSlug}`)
                        .then(response => response.json())
                        .then(models => {
                            if (Array.isArray(models)) {
                                models.forEach(model => {
                                    const modelValue = model.slug || model.id;
                                    const option = document.createElement('option');
                                    option.value = modelValue;
                                    option.textContent = model.name;
                                    homeModelSelect.appendChild(option);
                                });
                            }
                        })
                        .catch(error => {
                            console.error('Error loading models:', error);
                        });
                }
            });
        }

        // Live Search Mock Logic (Integrating with the real API identified earlier)
        const mainSearchInput = document.querySelector('.main-search-input');
        const suggestionsDropdown = document.getElementById('searchSuggestions');
        let debounceTimer;

        if (mainSearchInput && suggestionsDropdown) {
            mainSearchInput.addEventListener('input', function() {
                const query = this.value.trim();
                
                clearTimeout(debounceTimer);
                
                if (query.length < 2) {
                    suggestionsDropdown.style.display = 'none';
                    return;
                }

                debounceTimer = setTimeout(() => {
                    suggestionsDropdown.innerHTML = '<div class="searching-loader"><i class="fas fa-spinner fa-spin"></i> Searching...</div>';
                    suggestionsDropdown.style.display = 'block';

                    fetch(`/api/search/live?q=${encodeURIComponent(query)}`)
                        .then(response => response.json())
                        .then(data => {
                            if (data.results && data.results.length > 0) {
                                let html = '';
                                data.results.forEach(item => {
                                    const image = item.main_image || '/assets/images/placeholder-car.png';
                                    html += `
                                        <a href="/listing/${item.slug}" class="search-suggestion-item">
                                            <img src="${image}" alt="${item.title}" class="suggestion-image" onerror="this.src='/assets/images/placeholder-car.png'">
                                            <div class="suggestion-info">
                                                <span class="suggestion-title">${item.title}</span>
                                                <span class="suggestion-price">$${Number(item.price).toLocaleString()}</span>
                                            </div>
                                        </a>
                                    `;
                                });
                                // Add "See all results" link
                                html += `
                                    <a href="/search?q=${encodeURIComponent(query)}" class="search-suggestion-item" style="text-align: center; justify-content: center; background: #F3F4F6; font-weight: 700;">
                                        See all results for "${query}"
                                    </a>
                                `;
                                suggestionsDropdown.innerHTML = html;
                            } else {
                                suggestionsDropdown.innerHTML = '<div class="no-results-suggestion">No cars found matching your search.</div>';
                            }
                        })
                        .catch(err => {
                            console.error('Search error:', err);
                            suggestionsDropdown.style.display = 'none';
                        });
                }, 300);
            });

            // Close suggested results when clicking outside
            document.addEventListener('click', function(e) {
                if (!mainSearchInput.contains(e.target) && !suggestionsDropdown.contains(e.target)) {
                    suggestionsDropdown.style.display = 'none';
                }
            });

            // Handle focused input
            mainSearchInput.addEventListener('focus', function() {
                if (this.value.trim().length >= 2 && suggestionsDropdown.children.length > 0) {
                    suggestionsDropdown.style.display = 'block';
                }
            });
            
            // Handle keyboard navigation
            mainSearchInput.addEventListener('keydown', function(e) {
                const items = suggestionsDropdown.querySelectorAll('.search-suggestion-item');
                let highlighted = suggestionsDropdown.querySelector('.search-suggestion-item.highlighted');
                let index = Array.from(items).indexOf(highlighted);

                if (e.key === 'ArrowDown') {
                    e.preventDefault();
                    if (index < items.length - 1) {
                        if (highlighted) highlighted.classList.remove('highlighted');
                        items[index + 1].classList.add('highlighted');
                        items[index + 1].scrollIntoView({ block: 'nearest' });
                    } else if (index === -1 && items.length > 0) {
                        items[0].classList.add('highlighted');
                    }
                } else if (e.key === 'ArrowUp') {
                    e.preventDefault();
                    if (index > 0) {
                        highlighted.classList.remove('highlighted');
                        items[index - 1].classList.add('highlighted');
                        items[index - 1].scrollIntoView({ block: 'nearest' });
                    }
                } else if (e.key === 'Enter') {
                    if (highlighted) {
                        e.preventDefault();
                        window.location.href = highlighted.getAttribute('href');
                    }
                }
            });
        }

        // Initial count update
        updateSearchCount();
    </script>
</body>
</html>
