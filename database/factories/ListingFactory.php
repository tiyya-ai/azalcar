<?php

namespace Database\Factories;

use App\Models\Listing;
use App\Models\Make;
use App\Models\VehicleModel;
use App\Models\VehicleType;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Listing>
 */
class ListingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = fake()->sentence(3);
        $make = Make::factory()->create();
        $model = VehicleModel::where('make_id', $make->id)->first() ?? VehicleModel::factory()->create(['make_id' => $make->id]);
        $type = VehicleType::factory()->create();

        return [
            'user_id' => User::factory(),
            'title' => $title,
            'slug' => Str::slug($title) . '-' . fake()->unique()->randomNumber(),
            'description' => fake()->paragraph(),
            'make_id' => $make->id,
            'vehicle_model_id' => $model->id,
            'vehicle_type_id' => $type->id,
            'price' => fake()->numberBetween(1000, 100000),
            'year' => fake()->numberBetween(1990, 2023),
            'mileage' => fake()->numberBetween(0, 200000),
            'fuel_type' => fake()->randomElement(['Petrol', 'Diesel', 'Electric', 'Hybrid']),
            'transmission' => fake()->randomElement(['Manual', 'Automatic']),
            'condition' => fake()->randomElement(['New', 'Used', 'Certified Pre-Owned']),
            'color' => fake()->colorName(),
            'status' => 'active',
            'is_featured' => fake()->boolean(),
            'main_image' => fake()->imageUrl(),
        ];
    }
}