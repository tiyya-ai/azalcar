@extends('layouts/contentNavbarLayout')

@section('title', 'View Article: ' . $news->title)

@section('page-style')
<style>
    .article-header {
        position: relative;
        background-color: #f8f9fa;
        min-height: 200px;
        border-radius: 0.5rem;
        overflow: hidden;
    }
    .article-header-bg {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
        opacity: 0.1;
    }
    .article-content {
        font-size: 1.1rem;
        line-height: 1.8;
        color: #566a7f;
        word-break: break-word;
        overflow-wrap: break-word;
        word-wrap: break-word;
    }
    .article-content table {
        width: 100%;
        border-collapse: collapse;
        margin: 20px 0;
    }
    .article-content td, .article-content th {
        border: 1px solid #ddd;
        padding: 8px;
    }
    .status-badge {
        position: absolute;
        top: 1rem;
        right: 1rem;
    }
</style>
@endsection

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row g-4 mb-4">
        <div class="col-sm-6 col-xl-3">
            <h4 class="fw-bold py-3 mb-4">
                <span class="text-muted fw-light">Management / News /</span> View Article
            </h4>
        </div>
        <div class="col-sm-6 col-xl-9 text-sm-end">
             <a href="{{ route('admin.news.edit', $news) }}" class="btn btn-primary me-2">
                <i class="bx bx-pencil me-1"></i> Edit Article
            </a>
            <a href="{{ route('admin.news.index') }}" class="btn btn-secondary">
                <i class="bx bx-arrow-back me-1"></i> Back to List
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Main Content -->
        <div class="col-xl-8 col-lg-7">
            <div class="card mb-4">
                <div class="card-header article-header d-flex flex-column justify-content-center align-items-center text-center p-5">
                    
                    @if($news->image)
                        <img src="{{ asset('storage/' . $news->image) }}" class="article-header-bg" alt="Background">
                    @endif
                    
                    <div style="position: relative; z-index: 1;">
                        <span class="badge bg-primary mb-3">{{ $news->category }}</span>
                        <h2 class="fw-bold mb-2 text-dark">{{ $news->title }}</h2>
                        <div class="text-muted">
                            <i class="bx bx-calendar me-1"></i> {{ $news->published_at ? $news->published_at->format('F d, Y') : 'Draft' }}
                            <span class="mx-2">|</span>
                            <i class="bx bx-user me-1"></i> Admin
                        </div>
                    </div>
                </div>
                
                <div class="card-body mt-4">
                    @if($news->excerpt)
                        <div class="alert alert-light border border-primary mb-4 p-4">
                            <h6 class="text-primary fw-bold mb-2"><i class="bx bx-info-circle me-1"></i> Summary</h6>
                            <p class="mb-0 fst-italic text-dark">{{ $news->excerpt }}</p>
                        </div>
                    @endif
                    
                    <div class="article-content">
                        {!! $news->content !!}
                    </div>
                </div>
            </div>
            
            <!-- Live Preview Button -->
            <div class="text-center mb-4">
                <a href="{{ route('news.show', $news->slug) }}" target="_blank" class="btn btn-label-success">
                    <i class="bx bx-link-external me-1"></i> View Live on Website
                </a>
            </div>
        </div>
        
        <!-- Sidebar -->
        <div class="col-xl-4 col-lg-5">
             <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title mb-4">Article Status</h5>
                    
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="fw-semibold">Current State</span>
                        @if($news->status === 'published')
                            <span class="badge bg-success">PUBLISHED</span>
                        @elseif($news->status === 'archived')
                            <span class="badge bg-warning">ARCHIVED</span>
                        @else
                            <span class="badge bg-secondary">DRAFT</span>
                        @endif
                    </div>
                    
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="fw-semibold">Visibility</span>
                        <span class="text-muted"><i class="bx bx-world me-1"></i> Public</span>
                    </div>
                    
                     <div class="d-flex justify-content-between align-items-center">
                        <span class="fw-semibold">Last Updated</span>
                        <span class="text-muted">{{ $news->updated_at->diffForHumans() }}</span>
                    </div>
                    
                    <hr class="my-4">
                    
                    <div class="d-grid gap-2">
                        <form action="{{ route('admin.news.destroy', $news) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this article?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger w-100">
                                <i class="bx bx-trash me-1"></i> Delete Article
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            
            <!-- Cover Image Preview -->
            <div class="card mb-4">
                <div class="card-header pb-2">
                    <h5 class="card-title">Cover Image</h5>
                </div>
                <div class="card-body">
                     @if($news->image)
                        <img src="{{ asset('storage/' . $news->image) }}" class="img-fluid rounded border" alt="Cover Image">
                    @else
                        <div class="bg-lighter rounded p-4 text-center text-muted border border-dashed">
                            <i class="bx bx-image-alt fs-1 mb-2"></i>
                            <p class="mb-0">No cover image set.</p>
                        </div>
                    @endif
                </div>
            </div>

             <!-- SEO Preview (Mockup) -->
             <div class="card mb-4">
                <div class="card-header pb-2">
                    <h5 class="card-title">Search Preview</h5>
                </div>
                <div class="card-body">
                    <div class="mb-1">
                        <small class="text-success">{{ url('news/' . $news->slug) }}</small>
                    </div>
                    <a href="#" class="d-block h5 text-primary text-decoration-none mb-1">{{ $news->title }}</a>
                    <p class="text-muted small mb-0">
                        {{ Str::limit($news->excerpt ?? strip_tags($news->content), 150) }}
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
