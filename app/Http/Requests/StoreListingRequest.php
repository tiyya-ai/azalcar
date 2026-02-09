<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreListingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        $package = \App\Models\Package::where('slug', 'free')->first() ?? \App\Models\Package::find(1);

        return [
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
            'main_image' => 'required|image|mimes:jpeg,png,jpg,webp|max:5120|dimensions:min_width=200,min_height=200,max_width=4096,max_height=4096',
            'gallery' => 'nullable|array|max:' . $package->limit_images,
            'gallery.*' => 'image|mimes:jpeg,png,jpg,webp|max:5120|dimensions:min_width=200,min_height=200,max_width=4096,max_height=4096',
            'description' => 'required|string|min:10',
            'features' => 'nullable|array',
        ];
    }

    public function messages(): array
    {
        $package = \App\Models\Package::where('slug', 'free')->first() ?? \App\Models\Package::find(1);

        return [
            'main_image.required' => 'A main image is required for your listing.',
            'gallery.max' => "Your package allows a maximum of {$package->limit_images} images.",
            'main_image.dimensions' => 'Main image must be between 200x200 and 4096x4096 pixels.',
            'gallery.*.dimensions' => 'Gallery images must be between 200x200 and 4096x4096 pixels.',
        ];
    }
}