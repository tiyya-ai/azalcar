@extends('layouts.app')

@section('title', 'Automotive News & Reviews - azalcars Style')

@section('content')
<div class="news-archive-page" style="background: #ffffff; padding-bottom: 80px;">
    <!-- Header -->
    <div class="news-header" style="background: #1a1a1a; color: white; padding: 60px 0;">
        <div class="container">
            <h1 style="font-size: 48px; font-weight: 800; margin-bottom: 16px;">News & Reviews</h1>
            <p style="font-size: 18px; color: #ccc; max-width: 600px;">
                The latest automotive news, expert reviews, maintainance tips, and industry updates.
            </p>
        </div>
    </div>

    <div class="container" style="margin-top: 60px;">
        <!-- Featured Article (First Item) -->
        @if($news->count() > 0)
            @php $featured = $news->first(); @endphp
            <div class="featured-article mb-16" style="margin-bottom: 60px;">
                <a href="{{ route('news.show', $featured->slug) }}" class="featured-link" style="text-decoration: none; color: inherit; display: grid; grid-template-columns: 1.5fr 1fr; gap: 40px; align-items: center;">
                    <div class="featured-img" style="border-radius: 16px; overflow: hidden; height: 400px;">
                        @php
                            $fImg = $featured->image;
                            if ($fImg && !Str::startsWith($fImg, 'http')) {
                                $fImg = Str::startsWith($fImg, '/storage') ? asset($fImg) : asset('storage/' . $fImg);
                            }
                            $fDisplay = $fImg ?? 'https://images.unsplash.com/photo-1533473359331-0135ef1b58bf?auto=format&fit=crop&w=1200&q=80';
                        @endphp
                        <img src="{{ $fDisplay }}" alt="{{ $featured->title }}" style="width: 100%; height: 100%; object-fit: cover; transition: transform 0.3s;">
                    </div>
                    <div class="featured-content">
                        <span class="category-tag" style="display: inline-block; background: #6041E0; color: white; font-weight: 700; font-size: 12px; text-transform: uppercase; padding: 6px 12px; border-radius: 4px; margin-bottom: 16px;">
                            {{ $featured->category }}
                        </span>
                        <h2 style="font-size: 42px; font-weight: 800; line-height: 1.1; margin-bottom: 16px; color: #1a1a1a;">
                            {{ $featured->title }}
                        </h2>
                        <p style="font-size: 18px; color: #555; line-height: 1.6; margin-bottom: 24px;">
                            {{Str::limit($featured->excerpt, 150)}}
                        </p>
                        <span style="font-weight: 700; color: #1a1a1a; display: flex; align-items: center; gap: 8px;">
                            Read full article <i class="fas fa-arrow-right"></i>
                        </span>
                    </div>
                </a>
            </div>
        @endif

        <!-- Recent Articles Grid -->
        <h3 style="font-size: 24px; font-weight: 700; border-bottom: 1px solid #eee; padding-bottom: 16px; margin-bottom: 32px;">
            Recent Stories
        </h3>

        <div class="news-grid" style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 32px;">
            @foreach($news->skip(1) as $item)
                <div class="news-card">
                    <a href="{{ route('news.show', $item->slug) }}" style="text-decoration: none; color: inherit; display: block;">
                        <div class="card-img" style="height: 220px; border-radius: 12px; overflow: hidden; margin-bottom: 16px;">
                            @php
                                $itemImg = $item->image;
                                if ($itemImg && !Str::startsWith($itemImg, 'http')) {
                                    $itemImg = Str::startsWith($itemImg, '/storage') ? asset($itemImg) : asset('storage/' . $itemImg);
                                }
                                $itemDisplay = $itemImg ?? 'https://images.unsplash.com/photo-1606220838315-056192d5e927?auto=format&fit=crop&w=400&q=80';
                            @endphp
                            <img src="{{ $itemDisplay }}" alt="{{ $item->title }}" style="width: 100%; height: 100%; object-fit: cover;">
                        </div>
                        <div class="card-meta" style="display: flex; gap: 10px; font-size: 12px; font-weight: 700; text-transform: uppercase; margin-bottom: 8px; color: #6041E0;">
                            <span>{{ $item->category }}</span>
                            <span style="color: #ccc;">•</span>
                            <span style="color: #888;">{{ $item->published_at ? $item->published_at->format('M d, Y') : 'Just now' }}</span>
                        </div>
                        <h3 style="font-size: 20px; font-weight: 700; line-height: 1.4; margin-bottom: 8px; color: #1a1a1a;">
                            {{ $item->title }}
                        </h3>
                        <p style="color: #666; font-size: 15px; line-height: 1.5;">
                            {{ Str::limit($item->excerpt, 100) }}
                        </p>
                    </a>
                </div>
            @endforeach
        </div>

        <div class="pagination-container" style="margin-top: 60px;">
            {{ $news->links() }}
        </div>
    </div>
</div>

<style>
    .featured-img:hover img {
        transform: scale(1.02);
    }
    .news-card:hover .card-img img {
        transform: scale(1.05);
        transition: transform 0.3s;
    }
    .news-card .card-img img {
        transition: transform 0.3s;
    }
    
    @media (max-width: 900px) {
        .featured-link {
            grid-template-columns: 1fr !important;
        }
        .featured-img {
            height: 300px !important;
        }
        .news-grid {
            grid-template-columns: repeat(2, 1fr) !important;
        }
    }
    @media (max-width: 600px) {
        .news-grid {
            grid-template-columns: 1fr !important;
        }
        .news-header {
            padding: 40px 0 !important;
        }
        .news-header h1 {
            font-size: 32px !important;
        }
        .featured-content h2 {
            font-size: 28px !important;
        }
        .pagination-container {
            margin-top: 30px !important;
        }
    }
</style>
@endsection
