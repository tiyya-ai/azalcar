<?php

namespace App\Http\Controllers;

use App\Models\Make;
use App\Models\Listing;
use App\Models\VehicleType;
use App\Services\NHTSAapiService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\StoreListingRequest;
use App\Models\Package;
use App\Models\User;
use App\Notifications\NewListingPending;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class FrontendController extends Controller
{
    use AuthorizesRequests;

    public function index()
    {
        // Defensive: if core tables aren't migrated in the test environment, return empty collections
        if (!Schema::hasTable('makes') || !Schema::hasTable('listings')) {
            $makes = collect();
            $featuredListings = collect();
            $latestListings = collect();
            $recentListings = collect();
            return view('welcome', compact('makes', 'featuredListings', 'latestListings', 'recentListings'));
        }

        $makes = Make::withCount('listings')->orderBy('name')->get();

        $featuredListings = Listing::with(['make', 'vehicleModel', 'vehicleType', 'user'])
            ->where('is_featured', true)
            ->where('status', 'active')
            ->latest()
            ->take(8)
            ->get();
            
        $latestListings = Listing::with(['make', 'vehicleModel', 'vehicleType', 'user'])
            ->where('status', 'active')
            ->latest()
            ->take(12)
            ->get();
        
        // Get recently viewed from session
        $recentIds = session()->get('recently_viewed', []);
        $recentListings = collect();
        
        if (!empty($recentIds)) {
            $listings = Listing::with(['make', 'vehicleModel', 'vehicleType', 'user'])
                ->whereIn('id', $recentIds)
                ->get();
            // Sort by the order in session to show most recent first
            $recentListings = $listings->sortBy(function ($model) use ($recentIds) {
                return array_search($model->id, $recentIds);
            });
        }

        return view('welcome', compact('makes', 'featuredListings', 'latestListings', 'recentListings'));
    }

    public function showListing($slug)
    {
        $listing = Listing::with(['make', 'vehicleModel', 'user'])->where('slug', $slug)->firstOrFail();
        
        // Add to recently viewed session
        $recentlyViewed = session()->get('recently_viewed', []);
        
        // Remove if exists to push to front
        if(($key = array_search($listing->id, $recentlyViewed)) !== false) {
            unset($recentlyViewed[$key]);
        }
        
        // Add to front
        array_unshift($recentlyViewed, $listing->id);
        
        // Keep only last 8
        $recentlyViewed = array_slice($recentlyViewed, 0, 8);
        
        session()->put('recently_viewed', $recentlyViewed);

        // Fetch similar listings (same make, different id)
        $similarListings = Listing::where('make_id', $listing->make_id)
            ->where('id', '!=', $listing->id)
            ->where('status', 'active')
            ->latest()
            ->take(8)
            ->get();
            
        // If not enough, fill with same vehicle type
        if ($similarListings->count() < 4) {
            $moreListings = Listing::where('vehicle_type_id', $listing->vehicle_type_id)
                ->where('id', '!=', $listing->id)
                ->whereNotIn('id', $similarListings->pluck('id'))
                ->where('status', 'active')
                ->latest()
                ->take(8 - $similarListings->count())
                ->get();
            $similarListings = $similarListings->concat($moreListings);
        }
        
        return view('listings.show', compact('listing', 'similarListings'));
    }

    public function search(Request $request)
    {
        $query = Listing::query()->where('status', 'active');

        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($sq) use ($q) {
                $sq->where('title', 'like', "%$q%")
                    ->orWhere('description', 'like', "%$q%");
            });
        }

        if ($request->filled('make')) {
            $query->whereHas('make', function ($q) use ($request) {
                $q->where('slug', $request->make);
            });
        }

        if ($request->filled('model')) {
            $query->whereHas('vehicleModel', function ($q) use ($request) {
                $q->where('slug', $request->model);
            });
        }

        if ($request->filled('type')) {
            $query->whereHas('vehicleType', function ($q) use ($request) {
                $q->where('slug', $request->type);
            });
        }

        if ($request->filled('condition') && $request->condition != 'all') {
            $query->where('condition', $request->condition);
        }

        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }

        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        if ($request->filled('min_year')) {
            $query->where('year', '>=', $request->min_year);
        }

        if ($request->filled('max_year')) {
            $query->where('year', '<=', $request->max_year);
        }

        if ($request->filled('year')) {
            $query->where('year', $request->year);
        }

        if ($request->filled('min_mileage')) {
            $query->where('mileage', '>=', $request->min_mileage);
        }

        if ($request->filled('max_mileage')) {
            $query->where('mileage', '<=', $request->max_mileage);
        }

        if ($request->has('transmission') && is_array($request->transmission)) {
            $query->whereIn('transmission', $request->transmission);
        }

        if ($request->has('fuel_type') && is_array($request->fuel_type)) {
            $query->whereIn('fuel_type', $request->fuel_type);
        }

        if ($request->has('drivetrain') && is_array($request->drivetrain)) {
            $query->whereIn('drivetrain', $request->drivetrain);
        }

        if ($request->has('color') && is_array($request->color)) {
            $query->whereIn('color', $request->color);
        }

        if ($request->filled('is_exportable')) {
            $query->where('is_exportable', true);
        }

        if ($request->has('body_type') && is_array($request->body_type)) {
            // Future enhancement
        }

        // If requesting JSON for map
        if ($request->wantsJson() || $request->has('format') && $request->format === 'json') {
            $listings = $query->with(['make', 'vehicleModel'])->get()->map(function($listing) {
                return [
                    'id' => $listing->id,
                    'title' => $listing->title,
                    'slug' => $listing->slug,
                    'price' => $listing->price,
                    'year' => $listing->year,
                    'location' => $listing->location,
                    'latitude' => $listing->latitude,
                    'longitude' => $listing->longitude,
                    'main_image' => $listing->main_image,
                    'make' => $listing->make->name,
                    'model' => $listing->vehicleModel->name,
                ];
            });
            return response()->json($listings);
        }

        $listings = $query->latest()->paginate(12);
        $makes = Make::all();
        $types = VehicleType::all();

        // Get available years from database
        $availableYears = Listing::select('year')
            ->where('status', 'active')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year')
            ->toArray();

        return view('listings.index', compact('listings', 'makes', 'types', 'availableYears'));
    }

    public function getKoreanMakes()
    {
        $nhtsaService = new NHTSAapiService();
        return response()->json($nhtsaService->getAllMakes());
    }

    public function getModels($identifier)
    {
        // First check if it's a Korean make from NHTSA API
        $nhtsaService = new NHTSAapiService();
        $koreanMakes = $nhtsaService->getAllMakes();

        $koreanMake = collect($koreanMakes)->first(function ($make) use ($identifier) {
            return $make['id'] == $identifier || $make['slug'] == $identifier || Str::slug($make['name']) == $identifier;
        });

        if ($koreanMake) {
            $models = $nhtsaService->getModelsForMake($koreanMake['name']);
            return response()->json($models);
        }

        // Fallback to local database for non-Korean makes
        $make = Make::where('id', $identifier)->orWhere('slug', $identifier)->first();
        if (!$make) {
            return response()->json([]);
        }
        return response()->json($make->models);
    }

    public function getSearchCount(Request $request)
    {
        $query = Listing::query()->where('status', 'active');

        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($sq) use ($q) {
                $sq->where('title', 'like', "%$q%")
                    ->orWhere('description', 'like', "%$q%");
            });
        }

        if ($request->filled('make')) {
            $query->whereHas('make', function ($q) use ($request) {
                $q->where('slug', $request->make);
            });
        }

        if ($request->filled('model')) {
            $query->whereHas('vehicleModel', function ($q) use ($request) {
                $q->where('slug', $request->model);
            });
        }

        if ($request->filled('type')) {
            $query->whereHas('vehicleType', function ($q) use ($request) {
                $q->where('slug', $request->type);
            });
        }

        if ($request->filled('condition') && $request->condition != 'all') {
            $query->where('condition', $request->condition);
        }

        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }

        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        if ($request->filled('min_year')) {
            $query->where('year', '>=', $request->min_year);
        }

        if ($request->filled('max_year')) {
            $query->where('year', '<=', $request->max_year);
        }

        if ($request->filled('year')) {
            $query->where('year', $request->year);
        }

        if ($request->filled('min_mileage')) {
            $query->where('mileage', '>=', $request->min_mileage);
        }

        if ($request->filled('max_mileage')) {
            $query->where('mileage', '<=', $request->max_mileage);
        }

        if ($request->has('transmission') && is_array($request->transmission)) {
            $query->whereIn('transmission', $request->transmission);
        }

        $count = $query->count();

        return response()->json(['count' => $count]);
    }

    public function create()
    {
        $makes = Make::all();
        $types = VehicleType::all();
        return view('listings.create', compact('makes', 'types'));
    }

    public function store(StoreListingRequest $request)
    {
        Log::info('Listing store method called', ['user_id' => auth()->id(), 'request_data' => $request->all()]);

        try {
            return DB::transaction(function () use ($request) {
                // Determine package - hardcoded to free until payment integration
                $package = Package::where('slug', 'free')->first() ?? Package::find(1);

        // Package enforcement checks
        // Image limit check (additional validation)
        if ($request->hasFile('gallery') && count($request->file('gallery')) > $package->limit_images) {
            return back()->withErrors(['gallery' => "Your package allows a maximum of {$package->limit_images} images."])->withInput();
        }

        // Featured status check
        if (!$package->is_featured && $request->has('is_featured') && $request->is_featured) {
            return back()->withErrors(['is_featured' => 'Your package does not allow featured listings.'])->withInput();
        }

        // Expiration check - ensure expired_at is within package duration
        $maxExpiry = now()->addDays($package->duration_days);
        if ($request->has('expired_at') && $request->expired_at > $maxExpiry) {
            return back()->withErrors(['expired_at' => 'Expiration date cannot exceed package duration.'])->withInput();
        }

        $data = $request->except('gallery');
        $data['user_id'] = auth()->id();
        $data['slug'] = Str::slug($request->title . '-' . uniqid());
        $data['status'] = 'pending'; // Moderation queue

        $data['package_id'] = $package->id;
        $data['expired_at'] = $request->expired_at ?? now()->addDays($package->duration_days);
        $data['is_featured'] = $package->is_featured ? ($request->is_featured ?? false) : false;

        if ($request->hasFile('gallery')) {
            $galleryPaths = [];
            foreach ($request->file('gallery') as $image) {
                $path = $image->store('listings/gallery', 'public');
                $galleryPaths[] = '/storage/' . $path;
            }
            $data['images'] = $galleryPaths;
        }

        if ($request->hasFile('main_image')) {
            $mime = $request->file('main_image')->getMimeType();
            if (!in_array($mime, ['image/jpeg', 'image/png', 'image/webp'])) {
                return back()->withErrors(['main_image' => 'Invalid image format. Allowed: jpeg, png, webp.'])->withInput();
            }

            $path = $request->file('main_image')->store('listings', 'public');
            $data['main_image'] = '/storage/' . $path;
        }

        $listing = Listing::create($data);

        // Send notification to all admins about new pending listing
        $admins = \App\Models\User::where('role', 'admin')->get();
        foreach ($admins as $admin) {
            $admin->notify(new \App\Notifications\NewListingPending($listing));
        }

        return redirect()->route('listings.show', $listing->slug)->with('success', 'Listing created successfully! Pending approval.');
            });
        } catch (\Exception $e) {
            Log::error('Listing creation failed', [
                'user_id' => auth()->id(),
                'error' => $e->getMessage(),
                'request_data' => $request->all()
            ]);

            return back()->withErrors(['error' => 'Failed to create listing. Please try again.'])->withInput();
        }
    }

    public function edit($slug)
    {
        $listing = Listing::where('slug', $slug)->firstOrFail();
        
        // Authorization Check
        Log::info('Authorization check for listing update', ['user_id' => auth()->id(), 'listing_id' => $listing->id, 'listing_user_id' => $listing->user_id]);
        $this->authorize('update', $listing);
        Log::info('Authorization passed');
        
        $makes = Make::all();
        $types = VehicleType::all();
        return view('listings.edit', compact('listing', 'makes', 'types'));
    }

    public function update(Request $request, $id)
    {
        Log::info('Listing update attempt', ['user_id' => auth()->id(), 'listing_id' => $id, 'request_data' => $request->all()]);

        $listing = Listing::findOrFail($id);

        // Authorization Check
        $this->authorize('update', $listing);

        $request->validate([
            'title' => 'required|string|max:255',
            'make_id' => 'required|exists:makes,id',
            'vehicle_model_id' => 'required|exists:vehicle_models,id',
            'vehicle_type_id' => 'required|exists:vehicle_types,id',
            'price' => 'required|numeric|min:0',
            'year' => 'required|integer|min:1900|max:' . (date('Y') + 1),
            'mileage' => 'required|integer|min:0',
            'fuel_type' => 'required|string',
            'transmission' => 'required|string',
            'location' => 'required|string',
            'description' => 'required|string|min:10',
            'condition' => 'required|in:new,used',
            'main_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'gallery' => 'nullable|array|max:15',
            'gallery.*' => 'image|mimes:jpeg,png,jpg,webp|max:5120',
            'features' => 'nullable|array',
            'engine_size' => 'nullable|string',
            'color' => 'nullable|string',
            'drivetrain' => 'nullable|string',
        ]);

        $data = $request->only([
            'title', 'condition', 'make_id', 'vehicle_model_id', 'vehicle_type_id',
            'price', 'year', 'mileage', 'fuel_type', 'transmission', 'location', 
            'description', 'features', 'engine_size', 'color', 'drivetrain'
        ]);

        if ($request->hasFile('main_image')) {
            // Delete old main image if it exists
            if ($listing->main_image) {
                $oldPath = str_replace('/storage/', '', $listing->main_image);
                Storage::disk('public')->delete($oldPath);
            }

            $path = $request->file('main_image')->store('listings', 'public');
            $data['main_image'] = '/storage/' . $path;
        }

        $galleryPaths = $listing->images ?? [];

        // Handle removed images
        if ($request->has('removed_images')) {
            $removedImages = (array) $request->removed_images;
            $galleryPaths = array_filter($galleryPaths, function($image) use ($removedImages) {
                // Ensure we compare the path correctly
                return !in_array($image, $removedImages);
            });
            $galleryPaths = array_values($galleryPaths); // Re-index
        }

        // Handle new gallery images
        if ($request->hasFile('gallery')) {
            foreach ($request->file('gallery') as $image) {
                $path = $image->store('listings/gallery', 'public');
                $galleryPaths[] = '/storage/' . $path;
            }
        }

        $data['images'] = $galleryPaths;

        $result = $listing->update($data);
        Log::info('Listing update result', ['listing_id' => $listing->id, 'update_result' => $result, 'updated_data' => $data]);

        return redirect()->route('listings.show', $listing->slug)->with('success', 'Listing updated successfully!');
    }

    public function destroy($id)
    {
        $listing = Listing::findOrFail($id);

        // Authorization Check
        $this->authorize('delete', $listing);

        // Delete associated images
        if ($listing->images) {
            foreach ($listing->images as $image) {
                Storage::disk('public')->delete(str_replace('/storage/', '', $image));
            }
        }
        if ($listing->main_image) {
            Storage::disk('public')->delete(str_replace('/storage/', '', $listing->main_image));
        }

        $listing->delete();

        return redirect()->route('dashboard')->with('success', 'Listing deleted successfully!');
    }

    public function allBrands()
    {
        $brandsPath = public_path('assets/images/brands');
        $files = scandir($brandsPath);
        $brands = [];

        foreach ($files as $file) {
            if ($file !== '.' && $file !== '..' && (str_ends_with($file, '.png') || str_ends_with($file, '.jpg') || str_ends_with($file, '.webp') || str_ends_with($file, '.svg'))) {
                $name = pathinfo($file, PATHINFO_FILENAME);
                $cleanName = ucwords(str_replace('-', ' ', $name));
                $brands[] = [
                    'name' => $cleanName,
                    'logo' => $file,
                    'slug' => $name
                ];
            }
        }

        usort($brands, function($a, $b) {
            return strcmp($a['name'], $b['name']);
        });

        return view('listings.brands', compact('brands'));
    }

    public function switchCurrency($currencyId)
    {
        $currency = \App\Models\Currency::findOrFail($currencyId);

        if (auth()->check()) {
            // Save to user profile if logged in
            auth()->user()->update(['currency_id' => $currency->id]);
        } else {
            // Save to session for guests
            session(['currency_id' => $currency->id]);
        }

        return redirect()->back()->with('success', 'Currency switched to ' . $currency->name);
    }

    public function promote($slug)
    {
        $listing = Listing::where('slug', $slug)->firstOrFail();
        $this->authorize('update', $listing);
        // Get packages that offer featuring or top ranking, excluding basic/free if any
        $packages = \App\Models\Package::where('price', '>', 0)->get(); 
        return view('listings.promote', compact('listing', 'packages'));
    }

    public function processPromote(Request $request, $slug)
    {
        $listing = Listing::where('slug', $slug)->firstOrFail();
        $this->authorize('update', $listing);

        $request->validate(['package_id' => 'required|exists:packages,id']);
        $package = \App\Models\Package::findOrFail($request->package_id);
        $user = auth()->user();

        // Deduct balance
        if (!$user->updateBalance($package->price, 'subtract')) {
            return redirect()->route('wallet.index')->with('error', 'Insufficient balance to purchase promotion. Please top up your wallet.');
        }

        // Record Transaction
        \App\Models\Transaction::create([
            'user_id' => $user->id,
            'amount' => $package->price,
            'type' => 'debit',
            'description' => "Promoted listing: {$listing->title} ({$package->name})",
            'payment_method' => 'wallet',
            'currency' => 'RUB',
            'status' => 'completed'
        ]);

        // Apply Promotion
        $updateData = [];
        if ($package->is_featured) {
            $updateData['is_featured'] = true;
            $updateData['featured_until'] = now()->addDays($package->duration_days);
        }
        
        $updateData['package_id'] = $package->id;

        $listing->update($updateData);

        $user->notify(new \App\Notifications\ListingPromoted($listing, $package));

        return redirect()->route('listings.show', $slug)->with('success', 'Listing promoted successfully! Your ad is now boosted.');
    }
}
