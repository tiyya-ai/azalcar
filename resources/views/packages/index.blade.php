@extends('layouts.app')

@section('title', 'Pricing Packages - azal Cars')

@section('content')
<div class="bg-gray-50 py-16 sm:py-24">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center">
            <h2 class="text-base font-semibold text-[#6041E0] tracking-wide uppercase">Pricing</h2>
            <p class="mt-1 text-4xl font-extrabold text-gray-900 sm:text-5xl sm:tracking-tight lg:text-6xl">
                Choose the right plan for you.
            </p>
            <p class="max-w-xl mx-auto mt-5 text-xl text-gray-500">
                Start selling today with our affordable packages. Whether you're a private seller or a dealer, we have you covered.
            </p>
        </div>

        <div class="mt-16 space-y-12 lg:space-y-0 lg:grid lg:grid-cols-{{ max(1, min(3, count($packages))) }} lg:gap-x-8 justify-center">
            @foreach($packages as $package)
                <div class="relative p-8 bg-white border border-gray-200 rounded-2xl shadow-sm flex flex-col {{ $package->is_featured ? 'ring-2 ring-[#6041E0]' : '' }}">
                    <div class="flex-1">
                        @if($package->is_featured)
                            <p class="absolute top-0 -translate-y-1/2 bg-[#6041E0] text-white px-3 py-0.5 text-sm font-semibold tracking-wide rounded-full shadow-md">
                                Most Popular
                            </p>
                        @endif
                        <h3 class="text-xl font-semibold text-gray-900">{{ $package->name }}</h3>
                        <p class="mt-4 flex items-baseline text-gray-900">
                            <span class="text-5xl font-extrabold tracking-tight">{!! \App\Helpers\Helpers::formatPrice($package->price) !!}</span>
                            <span class="ml-1 text-xl font-semibold text-gray-500">/ ad</span>
                        </p>
                        <p class="mt-6 text-gray-500">{{ $package->description }}</p>

                        <!-- Feature List -->
                        <ul role="list" class="mt-6 space-y-4 text-gray-500">
                            <li class="flex items-start">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-check text-green-500"></i>
                                </div>
                                <p class="ml-3 text-base font-medium">Active for {{ $package->duration_days }} days</p>
                            </li>
                            <li class="flex items-start">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-check text-green-500"></i>
                                </div>
                                <p class="ml-3 text-base font-medium">Up to {{ $package->limit_images }} photos</p>
                            </li>
                            @if($package->is_featured)
                            <li class="flex items-start">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-check text-green-500"></i>
                                </div>
                                <p class="ml-3 text-base font-medium">Featured for {{ $package->max_featured_days }} days</p>
                            </li>
                            @endif
                            @if($package->is_top)
                            <li class="flex items-start">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-check text-green-500"></i>
                                </div>
                                <p class="ml-3 text-base font-medium">Bumped to top of search</p>
                            </li>
                            @endif
                        </ul>
                    </div>

                    <a href="{{ route('listings.create', ['package' => $package->id]) }}" class="mt-8 block w-full py-3 px-6 border border-transparent rounded-md text-center font-medium {{ $package->is_featured ? 'bg-[#6041E0] text-white hover:bg-[#4c30c4]' : 'bg-[#6041E0]/10 text-[#6041E0] hover:bg-[#6041E0]/20' }}">
                        Get started
                    </a>
                </div>
            @endforeach
        </div>
        
        @if($packages->isEmpty())
            <div class="text-center py-10">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-100 mb-4">
                    <i class="fas fa-box-open text-gray-400 text-2xl"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900">No packages available</h3>
                <p class="mt-1 text-gray-500">Please check back later or contact support.</p>
            </div>
        @endif
    </div>
</div>
@endsection
