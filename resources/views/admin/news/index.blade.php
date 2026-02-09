@extends('layouts/contentNavbarLayout')

@section('title', 'News Management')

@section('content')
<h4 class="py-3 mb-4">
  <span class="text-muted fw-light">Content /</span> News Articles
</h4>

@if(session('success'))
<div class="alert alert-success alert-dismissible" role="alert">
  {{ session('success') }}
  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

<div class="card">
  <div class="card-header d-flex justify-content-between align-items-center">
    <h5 class="mb-0">All News Articles</h5>
    <a href="{{ route('admin.news.create') }}" class="btn btn-primary">
      <i class="ti tabler-plus me-1"></i> Add News Article
    </a>
  </div>

  <div class="table-responsive text-nowrap">
    <table class="table">
      <thead>
        <tr>
          <th>Image</th>
          <th>Title</th>
          <th>Category</th>
          <th>Published</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody class="table-border-bottom-0">
        @forelse($news as $article)
        <tr>
          <td>
            @if($article->image)
              <img src="{{ asset('storage/' . $article->image) }}" alt="{{ $article->title }}" 
                   class="rounded" style="width: 60px; height: 60px; object-fit: cover;">
            @else
              <div class="bg-label-secondary rounded d-flex align-items-center justify-content-center" 
                   style="width: 60px; height: 60px;">
                <i class="ti tabler-photo ti-lg"></i>
              </div>
            @endif
          </td>
          <td>
            <strong>{{ $article->title }}</strong>
            <br>
            <small class="text-muted">{{ Str::limit($article->excerpt, 50) }}</small>
          </td>
          <td>
            <span class="badge bg-label-primary">{{ $article->category }}</span>
          </td>
          <td>
            @if($article->published_at)
              <small>{{ $article->published_at->format('M d, Y') }}</small>
            @else
              <span class="badge bg-label-warning">Draft</span>
            @endif
          </td>
          <td>
            <div class="dropdown">
              <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                <i class="ti tabler-dots-vertical"></i>
              </button>
              <div class="dropdown-menu">
                <a class="dropdown-item" href="{{ route('admin.news.show', $article) }}">
                  <i class="ti tabler-eye me-1"></i> View
                </a>
                <a class="dropdown-item" href="{{ route('admin.news.edit', $article) }}">
                  <i class="ti tabler-edit me-1"></i> Edit
                </a>
                <form action="{{ route('admin.news.destroy', $article) }}" method="POST" class="d-inline">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="dropdown-item text-danger"
                          onclick="return confirm('Are you sure you want to delete this article?')">
                    <i class="ti tabler-trash me-1"></i> Delete
                  </button>
                </form>
              </div>
            </div>
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="5" class="text-center py-5">
            <i class="ti tabler-news ti-3x text-muted mb-3 d-block"></i>
            <p class="text-muted">No news articles found. Create your first article!</p>
            <a href="{{ route('admin.news.create') }}" class="btn btn-primary btn-sm">
              <i class="ti tabler-plus me-1"></i> Add News Article
            </a>
          </td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  @if($news->hasPages())
  <div class="card-footer">
    {{ $news->links() }}
  </div>
  @endif
</div>
@endsection
