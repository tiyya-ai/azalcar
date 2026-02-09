<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Listing;
use App\Models\Make;
use App\Models\VehicleModel;
use App\Models\VehicleType;
use Illuminate\Support\Facades\Storage;

class ListingsController extends Controller
{
    public function index(Request $request)
    {
        $query = Listing::with(['make', 'vehicleModel', 'user'])->latest();

        // Search Filter (ID or Title)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('id', $search)
                  ->orWhere('title', 'like', "%{$search}%");
            });
        }

        // Status Filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Make Filter
        if ($request->filled('make_id')) {
            $query->where('make_id', $request->make_id);
        }

        $listings = $query->paginate(25)->appends($request->all());
        $makes = Make::orderBy('name')->get();

        return view('admin.listings.index', compact('listings', 'makes'));
    }

    public function create(Request $request)
    {
        $makes = Make::all();
        $types = VehicleType::all();
        $target_user_id = $request->query('user_id');
        return view('admin.listings.create', compact('makes', 'types', 'target_user_id'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'make_id' => 'required|exists:makes,id',
            'vehicle_model_id' => 'required|exists:vehicle_models,id',
            'vehicle_type_id' => 'required|exists:vehicle_types,id',
            'price' => 'required|numeric|min:0',
            'year' => 'required|integer|min:1900|max:' . (date('Y') + 1),
            'mileage' => 'required|integer|min:0',
            'condition' => 'required|in:new,used',
            'transmission' => 'required|in:manual,automatic',
            'fuel_type' => 'required|string',
            'description' => 'required|string',
            'location' => 'required|string',
            'main_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:10240',
            'gallery.*' => 'image|mimes:jpeg,png,jpg,webp|max:10240',
            'user_id' => 'nullable|exists:users,id'
        ]);

        try {
            $listingService = app(\App\Services\ListingService::class);
            $imageService = app(\App\Services\ImageService::class);
            
            // Get or create default package
            $package = \App\Models\Package::where('slug', 'free')->first() 
                ?? \App\Models\Package::where('price', 0)->first()
                ?? \App\Models\Package::first();

            if (!$package) {
                return back()->withErrors(['error' => 'No package available. Please create a package first.'])->withInput();
            }

            // Determine user (admin can create for other users)
            $userId = $request->user_id ?? auth()->id();
            $user = \App\Models\User::findOrFail($userId);

            // Create listing
            $listing = $listingService->createListing($user, $request->all(), $package);
            
            // Admin-created listings are auto-approved
            $listingService->approveListing($listing);

            return redirect()->route('admin.listings.index')
                ->with('success', 'Listing created and approved successfully');
                
        } catch (\Exception $e) {
            \Log::error('Admin listing creation failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return back()->withErrors(['error' => 'Failed to create listing: ' . $e->getMessage()])->withInput();
        }
    }

    public function edit(Listing $listing)
    {
        $makes = Make::all();
        $types = VehicleType::all();
        $models = $listing->make ? $listing->make->models : collect();
        return view('admin.listings.edit', compact('listing', 'makes', 'types', 'models'));
    }

    public function update(Request $request, Listing $listing)
    {
        \Log::info('=== ADMIN LISTING UPDATE STARTED ===', [
            'listing_id' => $listing->id,
            'timestamp' => now(),
        ]);

        \Log::info('Request files info', [
            'has_main_image_file' => $request->hasFile('main_image'),
            'has_delete_main_image' => $request->has('delete_main_image'),
            'delete_main_image_value' => $request->input('delete_main_image'),
            'all_files_count' => count($request->allFiles()),
            'all_files' => array_keys($request->allFiles()),
        ]);

        if ($request->hasFile('main_image')) {
            $file = $request->file('main_image');
            \Log::info('Main image file details', [
                'name' => $file->getClientOriginalName(),
                'size' => $file->getSize(),
                'mime' => $file->getMimeType(),
                'is_valid' => $file->isValid(),
            ]);
        }

        // Validate incoming data
        $request->validate([
            'title' => 'required|string|max:255',
            'make_id' => 'required|exists:makes,id',
            'vehicle_model_id' => 'required|exists:vehicle_models,id',
            'vehicle_type_id' => 'required|exists:vehicle_types,id',
            'price' => 'required|numeric|min:0',
            'year' => 'required|integer|min:1900|max:' . (date('Y') + 2),
            'mileage' => 'nullable|integer|min:0',
            'condition' => 'nullable|in:new,used',
            'transmission' => 'nullable|string',
            'fuel_type' => 'nullable|string',
            'description' => 'nullable|string',
            'location' => 'nullable|string',
            'main_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:10240',
            'gallery' => 'nullable|array|max:20',
            'gallery.*' => 'image|mimes:jpeg,png,jpg,webp|max:10240'
        ]);

        \Log::info('Validation passed');

        $data = $request->only([
            'title', 'make_id', 'vehicle_model_id', 'vehicle_type_id', 'price',
            'year', 'mileage', 'condition', 'transmission', 'fuel_type',
            'description', 'location', 'vin', 'engine_size', 'color', 'doors',
            'seats', 'features', 'status', 'drivetrain', 'video_url', 'v360_url'
        ]);

        \Log::info('Data extracted from request', [
            'data_keys' => array_keys($data),
            'title' => $data['title'] ?? null,
            'video_url' => $data['video_url'] ?? null,
            'v360_url' => $data['v360_url'] ?? null,
        ]);

        if ($request->has('features')) {
            $data['features'] = json_encode($request->features);
        }

        // Handle main image upload/deletion
        if ($request->hasFile('main_image')) {
            \Log::info('>>> PROCESSING MAIN IMAGE UPLOAD <<<');
            // Delete old image if it exists
            if ($listing->main_image) {
                $oldPath = str_replace('/storage/', '', $listing->main_image);
                \Log::info('Deleting old image', ['old_path' => $oldPath]);
                Storage::disk('public')->delete($oldPath);
            }
            // Upload new image
            try {
                $path = $request->file('main_image')->store('listings', 'public');
                $data['main_image'] = '/storage/' . $path;
                \Log::info('✓ Main image uploaded successfully', [
                    'stored_path' => $path,
                    'full_path' => $data['main_image'],
                ]);
            } catch (\Exception $e) {
                \Log::error('✗ Main image upload failed', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);
                throw $e;
            }
        } elseif ($request->has('delete_main_image') && $request->delete_main_image == '1') {
            \Log::info('>>> PROCESSING MAIN IMAGE DELETION <<<');
            // Delete image if explicitly requested
            if ($listing->main_image) {
                $oldPath = str_replace('/storage/', '', $listing->main_image);
                \Log::info('Deleting main image', ['path' => $oldPath]);
                Storage::disk('public')->delete($oldPath);
            }
            $data['main_image'] = null;
        } else {
            \Log::info('No main image upload or deletion requested');
        }

        $currentImages = $listing->images ?? [];
        if ($request->has('delete_gallery_images') && $request->delete_gallery_images) {
            $deletePaths = explode(',', $request->delete_gallery_images);
            foreach ($deletePaths as $imagePath) {
                $imagePath = trim($imagePath);
                if ($imagePath) {
                    // Delete the physical file
                    $cleanPath = str_replace('/storage/', '', $imagePath);
                    Storage::disk('public')->delete($cleanPath);

                    // Remove from current images array - ensure exact match
                    $currentImages = array_filter($currentImages, function($img) use ($imagePath) {
                        return trim($img) !== trim($imagePath);
                    });
                }
            }
            // Re-index the array
            $currentImages = array_values($currentImages);
        }

        if ($request->hasFile('gallery')) {
            foreach ($request->file('gallery') as $file) {
                $path = $file->store('listings/gallery', 'public');
                $currentImages[] = '/storage/' . $path;
            }
        }
        $data['images'] = array_values($currentImages);

        \Log::info('Final data to be saved', [
            'data_keys' => array_keys($data),
            'main_image_in_data' => isset($data['main_image']),
            'main_image_value' => $data['main_image'] ?? 'NOT SET',
            'images_count' => count($data['images'] ?? []),
        ]);

        try {
            $result = $listing->update($data);
            \Log::info('✓ Listing updated successfully', [
                'listing_id' => $listing->id,
                'update_result' => $result,
                'updated_main_image' => $listing->main_image,
            ]);
        } catch (\Exception $e) {
            \Log::error('✗ Listing update failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }

        return redirect()->back()->with('success', 'Listing updated successfully');
    }

    public function approve(Listing $listing)
    {
        try {
            $listingService = app(\App\Services\ListingService::class);
            $listingService->approveListing($listing);

            return back()->with('success', 'Listing approved successfully');
        } catch (\Exception $e) {
            \Log::error('Failed to approve listing', [
                'listing_id' => $listing->id,
                'error' => $e->getMessage(),
            ]);

            return back()->withErrors(['error' => 'Failed to approve listing: ' . $e->getMessage()]);
        }
    }

    public function reject(Listing $listing)
    {
        try {
            $listingService = app(\App\Services\ListingService::class);
            $listingService->rejectListing($listing, request()->input('reason'));

            return back()->with('success', 'Listing rejected successfully');
        } catch (\Exception $e) {
            \Log::error('Failed to reject listing', [
                'listing_id' => $listing->id,
                'error' => $e->getMessage(),
            ]);

            return back()->withErrors(['error' => 'Failed to reject listing: ' . $e->getMessage()]);
        }
    }

    public function destroy(Listing $listing)
    {
        $listing->delete();
        return redirect()->route('admin.listings.index')->with('success', 'Listing deleted successfully');
    }

    public function bulkDestroy(Request $request)
    {
        $ids = $request->ids;
        if (empty($ids)) {
            return back()->with('error', 'No items selected');
        }

        $listings = Listing::whereIn('id', $ids)->get();
        foreach ($listings as $listing) {
            $listing->delete();
        }

        return back()->with('success', 'Selected listings deleted successfully');
    }
}