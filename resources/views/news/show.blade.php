@extends('layouts.app')

@section('title', $news->title . ' - News')

@section('content')
<div class="news-single-page" style="background: white; padding-bottom: 80px;">
    
    <!-- Hero Header for Article -->
    <div class="article-hero" style="position: relative; height: 500px; margin-bottom: 64px;">
        <div style="position: absolute; inset: 0;">
            @php
                $imagePath = $news->image;
                if ($imagePath && !Str::startsWith($imagePath, 'http')) {
                    $imagePath = Str::startsWith($imagePath, '/storage') ? asset($imagePath) : asset('storage/' . $imagePath);
                }
                $displayImage = $imagePath ?? 'https://images.unsplash.com/photo-1533473359331-0135ef1b58bf?auto=format&fit=crop&w=1200&q=80';
            @endphp
            <img src="{{ $displayImage }}" alt="{{ $news->title }}" style="width: 100%; height: 100%; object-fit: cover;">
            <div style="position: absolute; inset: 0; background: linear-gradient(to bottom, rgba(0,0,0,0.2), rgba(0,0,0,0.8));"></div>
        </div>
        <div class="container" style="position: relative; height: 100%; display: flex; align-items: flex-end; padding-bottom: 60px;">
            <div style="max-width: 900px;">
                <span class="category-badge" style="background: #6041E0; color: white; padding: 6px 12px; border-radius: 4px; font-weight: 700; text-transform: uppercase; font-size: 14px; margin-bottom: 24px; display: inline-block;">
                    {{ $news->category }}
                </span>
                <h1 style="color: white; font-size: 48px; font-weight: 800; line-height: 1.1; margin-bottom: 16px; text-shadow: 0 4px 12px rgba(0,0,0,0.3);">
                    {{ $news->title }}
                </h1>
                <div class="meta" style="color: white; font-size: 16px; font-weight: 500; opacity: 0.9;">
                    <span>Published on {{ $news->published_at ? $news->published_at->format('F d, Y') : 'Recently' }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="container news-content-layout" style="display: grid; grid-template-columns: 1fr 340px; gap: 64px;">
        
        <!-- Main Content -->
        <article class="news-body">
            <p class="lead-text" style="font-size: 20px; line-height: 1.6; color: #333; margin-bottom: 40px; font-weight: 500;">
                {{ $news->excerpt }}
            </p>
            
            <div class="content-text article-content" style="font-size: 18px; line-height: 1.8; color: #1a1a1a;">
                {!! $news->content !!}
            </div>

            <!-- Share / Tags (Optional placeholder) -->
            <div style="margin-top: 60px; padding-top: 32px; border-top: 1px solid #eee;">
                <span style="font-weight: 700;">Share this:</span>
                <div style="display: flex; gap: 12px; margin-top: 12px;">
                    <button style="background: #3b5998; color: white; border: none; width: 40px; height: 40px; border-radius: 50%;"><i class="fab fa-facebook-f"></i></button>
                    <button style="background: #1da1f2; color: white; border: none; width: 40px; height: 40px; border-radius: 50%;"><i class="fab fa-twitter"></i></button>
                    <button style="background: #0077b5; color: white; border: none; width: 40px; height: 40px; border-radius: 50%;"><i class="fab fa-linkedin-in"></i></button>
                </div>
            </div>
        </article>

        <!-- Sidebar -->
        <aside class="news-sidebar">
            <h3 style="font-size: 20px; font-weight: 700; margin-bottom: 24px; padding-bottom: 12px; border-bottom: 2px solid #1a1a1a;">
                Related News
            </h3>
            
            <div class="sidebar-list" style="display: flex; flex-direction: column; gap: 24px;">
                @foreach($relatedNews as $related)
                <a href="{{ route('news.show', $related->slug) }}" class="related-item" style="text-decoration: none; color: inherit; display: flex; gap: 16px; align-items: start;">
                    <div style="width: 100px; height: 75px; flex-shrink: 0; background: #eee; border-radius: 8px; overflow: hidden;">
                        @php
                            $relImagePath = $related->image;
                            if ($relImagePath && !Str::startsWith($relImagePath, 'http')) {
                                $relImagePath = Str::startsWith($relImagePath, '/storage') ? asset($relImagePath) : asset('storage/' . $relImagePath);
                            }
                            $relDisplayImage = $relImagePath ?? 'https://images.unsplash.com/photo-1606220838315-056192d5e927?auto=format&fit=crop&w=400&q=80';
                        @endphp
                        <img src="{{ $relDisplayImage }}" style="width: 100%; height: 100%; object-fit: cover;">
                    </div>
                    <div>
                        <span style="font-size: 11px; font-weight: 700; color: #6041E0; text-transform: uppercase;">{{ $related->category }}</span>
                        <h4 style="font-size: 15px; font-weight: 600; line-height: 1.4; margin-top: 4px; color: #1a1a1a;">
                            {{ $related->title }}
                        </h4>
                    </div>
                </a>
                @endforeach
            </div>

            <!-- Ad / Promo Placeholder -->
            <div style="margin-top: 40px; background: #f5f5f5; padding: 32px; border-radius: 12px; text-align: center;">
                <h4 style="font-weight: 800; margin-bottom: 12px;">Selling your car?</h4>
                <p style="font-size: 14px; margin-bottom: 20px;">Get a real offer in minutes, or list for free.</p>
                <a href="#" style="display: inline-block; background: #1a1a1a; color: white; padding: 12px 24px; border-radius: 24px; font-weight: 700; text-decoration: none;">Get Started</a>
            </div>
        </aside>

    </div>
</div>

<style>
    .article-content {
        word-break: break-word;
        overflow-wrap: break-word;
        word-wrap: break-word;
    }

    .content-text p {
        margin-bottom: 24px;
    }
    
    .article-content table {
        width: 100%;
        margin: 32px 0;
        border-collapse: collapse;
        font-size: 16px;
    }
    
    .article-content table th, 
    .article-content table td {
        padding: 12px 16px;
        border: 1px solid #eee;
        text-align: left;
    }
    
    .article-content table th {
        background: #f8f9fa;
        font-weight: 700;
        color: #1a1a1a;
    }
    
    .article-content img {
        max-width: 100%;
        height: auto;
        border-radius: 12px;
        margin: 32px 0;
    }
    
    @media (max-width: 900px) {
        .news-content-layout {
            grid-template-columns: 1fr !important;
            gap: 40px !important;
        }
        .article-hero {
            height: 350px !important;
            margin-bottom: 30px !important;
        }
        .article-hero h1 {
            font-size: 28px !important;
        }
        .article-hero .container {
            padding-bottom: 30px !important;
        }
    }
</style>
@endsection
