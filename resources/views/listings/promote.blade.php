@extends('layouts.app')

@section('title', 'Promote Listing')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h2 class="mb-4 fw-bold">Promote Your Ad</h2>
            
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body d-flex align-items-center">
                    @if(!empty($listing->images))
                        <img src="{{ $listing->images[0] }}" alt="" class="rounded me-3" width="100" height="75" style="object-fit: cover;">
                    @else
                        <div class="bg-light rounded me-3 d-flex align-items-center justify-content-center" style="width: 100px; height: 75px;">
                            <i class="fa fa-car text-muted"></i>
                        </div>
                    @endif
                    <div>
                        <h5 class="mb-1">{{ $listing->title }}</h5>
                        <p class="text-muted mb-0">{!! \App\Helpers\Helpers::formatPrice($listing->price) !!}</p>
                    </div>
                </div>
            </div>

            <h4 class="mb-3">Select a Package</h4>
            
            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            <div class="row g-3">
                @forelse($packages as $package)
                <div class="col-md-6">
                    <div class="card h-100 border-{{ $package->is_featured ? 'primary' : 'secondary' }} shadow-{{ $package->is_featured ? 'sm' : 'none' }}">
                        <div class="card-body">
                            @if($package->is_featured)
                                <div class="badge bg-primary mb-2">Recommended</div>
                            @endif
                            <h5 class="card-title fw-bold">{{ $package->name }}</h5>
                            <h3 class="card-text text-primary">₽ {{ number_format($package->price) }}</h3>
                            <p class="text-muted">{{ $package->duration_days }} Days Duration</p>
                            
                            <ul class="list-unstyled mt-3 mb-4">
                                @if($package->is_featured)
                                <li class="mb-2"><i class="fa fa-check text-success me-2"></i> Featured Badge</li>
                                <li class="mb-2"><i class="fa fa-check text-success me-2"></i> Homepage Placement</li>
                                @endif
                                @if($package->is_top)
                                <li class="mb-2"><i class="fa fa-check text-success me-2"></i> Top Search Ranking</li>
                                @endif
                                <li class="mb-2"><i class="fa fa-check text-success me-2"></i> {{ $package->limit_images }} Photos Allowed</li>
                            </ul>

                            <form action="{{ route('listings.promote.process', $listing->slug) }}" method="POST">
                                @csrf
                                <input type="hidden" name="package_id" value="{{ $package->id }}">
                                <button type="submit" class="btn btn-{{ $package->is_featured ? 'primary' : 'outline-primary' }} w-100" 
                                    {{ auth()->user()->balance < $package->price ? 'disabled' : '' }}>
                                    Purchase Package
                                </button>
                            </form>
                            @if(auth()->user()->balance < $package->price)
                                <div class="mt-2 text-center">
                                    <small class="text-danger">Insufficient balance. <a href="{{ route('wallet.index') }}">Top up</a></small>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-12">
                    <div class="alert alert-info">No promotion packages available at the moment.</div>
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
