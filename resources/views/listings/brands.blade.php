<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Car Brands - azalcars</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
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



        .container {
            max-width: 1440px; /* Wider container for 9 items */
            margin: 0 auto;
            padding: 48px 24px;
        }
        
        h1 {
            font-family: 'DM Sans', sans-serif;
            font-size: 32px;
            font-weight: 700;
            color: #1A1D1C;
            margin-bottom: 12px;
        }

        .subtitle {
            font-size: 16px;
            color: #5E5E5E;
            margin-bottom: 48px;
        }

        .brands-grid {
            display: grid;
            grid-template-columns: repeat(9, 1fr); /* 9 logos inline */
            gap: 16px;
        }

        .brand-card {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 20px 10px;
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            text-decoration: none;
            transition: all 0.2s;
        }

        .brand-card:hover {
            border-color: #6041E0;
            box-shadow: 0 4px 12px rgba(96, 65, 224, 0.08);
        }

        .brand-logo-wrapper {
            width: 50px;
            height: 50px;
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
            font-size: 13px;
            font-weight: 700;
            color: #1A1D1C;
            text-align: center;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            width: 100%;
        }

        .back-link {
            display: inline-flex;
            align-items: center;
            color: #1A1D1C;
            text-decoration: none;
            font-weight: 700;
            font-size: 14px;
            margin-bottom: 24px;
        }

        .back-link i {
            margin-right: 8px;
        }

        .back-link:hover {
            text-decoration: underline;
        }

        @media (max-width: 1200px) {
            .brands-grid { grid-template-columns: repeat(6, 1fr); }
        }
        @media (max-width: 900px) {
            .brands-grid { grid-template-columns: repeat(4, 1fr); }
        }
        @media (max-width: 600px) {
            .brands-grid { grid-template-columns: repeat(2, 1fr); }
        }
    </style>
</head>
<body>
    @include('partials.header')
    
    <div class="container">
        <a href="{{ route('home') }}" class="back-link">
            <i class="fas fa-arrow-left"></i> Back to Home
        </a>
        
        <h1>All Car Brands</h1>
        <p class="subtitle">Browse automotive manufacturers and find your next vehicle.</p>
        
        <div class="brands-grid">
            @foreach($brands as $brand)
            <a href="{{ route('listings.search', ['brand' => $brand['slug']]) }}" class="brand-card">
                <div class="brand-logo-wrapper">
                    <img src="{{ asset('assets/images/brands/' . $brand['logo']) }}" alt="{{ $brand['name'] }} Logo">
                </div>
                <span class="brand-name">{{ $brand['name'] }}</span>
            </a>
            @endforeach
        </div>
    </div>

    @include('partials.footer')
</body>
</html>
