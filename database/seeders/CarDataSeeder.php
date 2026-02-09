<?php

namespace Database\Seeders;

use App\Models\Make;
use App\Models\VehicleModel;
use App\Models\VehicleType;
use App\Models\Listing;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class CarDataSeeder extends Seeder
{
    public function run(): void
    {
        // Create admin user
        $admin = User::updateOrCreate(
            ['email' => 'admin@azalcars.com'],
            [
                'name' => 'Admin',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
            ]
        );

        // Create vendor users
        $vendors = [];
        $vendorNames = ['John Dealer', 'Maria Motors', 'AutoMax LLC', 'CarPro Sales', 'Elite Cars'];
        foreach ($vendorNames as $name) {
            $vendors[] = User::updateOrCreate(
                ['email' => Str::slug($name) . '@azalcars.com'],
                [
                    'name' => $name,
                    'password' => Hash::make('vendor123'),
                    'role' => 'vendor',
                ]
            );
        }

        // Create vehicle types
        $types = [
            'Sedan' => 'sedan',
            'SUV' => 'suv',
            'Hatchback' => 'hatchback',
            'Coupe' => 'coupe',
            'Convertible' => 'convertible',
            'Wagon' => 'wagon',
            'Pickup Truck' => 'pickup-truck',
            'Minivan' => 'minivan',
            'Sports Car' => 'sports-car',
        ];

        $vehicleTypes = [];
        foreach ($types as $name => $slug) {
            $vehicleTypes[$slug] = VehicleType::updateOrCreate(
                ['slug' => $slug],
                ['name' => $name]
            );
        }

        // Create car makes with their models
        $makesData = [
            'Toyota' => ['Camry', 'Corolla', 'RAV4', 'Highlander', 'Land Cruiser', 'Prius', 'Avalon'],
            'BMW' => ['3 Series', '5 Series', '7 Series', 'X3', 'X5', 'X7', 'M3', 'M5'],
            'Mercedes-Benz' => ['C-Class', 'E-Class', 'S-Class', 'GLE', 'GLS', 'G-Class', 'A-Class'],
            'Audi' => ['A3', 'A4', 'A6', 'A8', 'Q3', 'Q5', 'Q7', 'Q8', 'e-tron'],
            'Honda' => ['Civic', 'Accord', 'CR-V', 'Pilot', 'Odyssey', 'Fit'],
            'Ford' => ['F-150', 'Mustang', 'Explorer', 'Escape', 'Edge', 'Bronco'],
            'Chevrolet' => ['Silverado', 'Tahoe', 'Malibu', 'Equinox', 'Traverse'],
            'Nissan' => ['Altima', 'Maxima', 'Rogue', 'Pathfinder', 'Frontier', 'Titan'],
            'Hyundai' => ['Elantra', 'Sonata', 'Santa Fe', 'Tucson', 'Palisade', 'Kona'],
            'Kia' => ['Optima', 'Sorento', 'Sportage', 'Telluride', 'Soul', 'Rio'],
            'Volkswagen' => ['Jetta', 'Passat', 'Tiguan', 'Atlas', 'Golf', 'Arteon'],
            'Mazda' => ['Mazda3', 'Mazda6', 'CX-5', 'CX-9', 'MX-5 Miata'],
            'Subaru' => ['Outback', 'Forester', 'Impreza', 'Crosstrek', 'Ascent', 'WRX'],
            'Lexus' => ['ES', 'IS', 'LS', 'RX', 'NX', 'GX', 'LX'],
            'Porsche' => ['911', 'Cayenne', 'Macan', 'Panamera', 'Taycan'],
        ];

        $makes = [];
        $allModels = collect();

        foreach ($makesData as $makeName => $modelNames) {
            $make = Make::updateOrCreate(
                ['slug' => Str::slug($makeName)],
                ['name' => $makeName]
            );
            $makes[] = $make;

            foreach ($modelNames as $modelName) {
                $model = VehicleModel::updateOrCreate(
                    [
                        'slug' => Str::slug($modelName),
                        'make_id' => $make->id,
                    ],
                    ['name' => $modelName]
                );
                $allModels->push($model);
            }
        }

        // Cities with coordinates for map
        $cities = [
            'New York' => ['lat' => 40.7128, 'lng' => -74.0060],
            'Los Angeles' => ['lat' => 34.0522, 'lng' => -118.2437],
            'Chicago' => ['lat' => 41.8781, 'lng' => -87.6298],
            'Houston' => ['lat' => 29.7604, 'lng' => -95.3698],
            'Phoenix' => ['lat' => 33.4484, 'lng' => -112.0740],
            'Philadelphia' => ['lat' => 39.9526, 'lng' => -75.1652],
            'San Antonio' => ['lat' => 29.4241, 'lng' => -98.4936],
            'San Diego' => ['lat' => 32.7157, 'lng' => -117.1611],
            'Dallas' => ['lat' => 32.7767, 'lng' => -96.7970],
            'Miami' => ['lat' => 25.7617, 'lng' => -80.1918],
        ];

        $conditions = ['new', 'used'];
        $transmissions = ['automatic', 'manual'];
        $fuelTypes = ['gasoline', 'diesel', 'hybrid', 'electric'];
        $colors = ['Black', 'White', 'Silver', 'Gray', 'Red', 'Blue', 'Green', 'Brown'];

        $features = [
            'Leather Seats',
            'Sunroof',
            'Navigation System',
            'Backup Camera',
            'Bluetooth',
            'Heated Seats',
            'Apple CarPlay',
            'Android Auto',
            'Keyless Entry',
            'Remote Start',
            'Parking Sensors',
            'Adaptive Cruise Control',
            'Lane Departure Warning',
            'Blind Spot Monitor',
            'Premium Sound System',
            'Alloy Wheels',
            'Tinted Windows',
            'Third Row Seating',
        ];

        // Create 100 realistic listings
        for ($i = 0; $i < 100; $i++) {
            $make = $makes[array_rand($makes)];
            $makeModels = $allModels->where('make_id', $make->id)->values();
            
            if ($makeModels->isEmpty()) {
                continue; // Skip if no models for this make
            }
            
            $model = $makeModels[rand(0, $makeModels->count() - 1)];
            $type = $vehicleTypes[array_rand($vehicleTypes)];
            $vendor = $vendors[array_rand($vendors)];
            $city = array_rand($cities);
            $cityData = $cities[$city];

            $year = rand(2015, 2024);
            $mileage = rand(5000, 150000);
            $basePrice = rand(15000, 85000);
            $price = round($basePrice / 100) * 100; // Round to nearest 100

            $condition = $conditions[array_rand($conditions)];
            $transmission = $transmissions[array_rand($transmissions)];
            $fuelType = $fuelTypes[array_rand($fuelTypes)];
            $color = $colors[array_rand($colors)];

            // Select random features
            $selectedFeatures = array_rand(array_flip($features), rand(5, 12));
            if (!is_array($selectedFeatures)) {
                $selectedFeatures = [$selectedFeatures];
            }

            $title = "{$make->name} {$model->name} {$year}";
            $slug = Str::slug($title . '-' . uniqid());

            $description = "This {$year} {$make->name} {$model->name} is in {$condition} condition with {$mileage} miles. " .
                "Features include {$transmission} transmission, {$fuelType} engine, and {$color} exterior. " .
                "Well-maintained vehicle with clean title. " .
                "Located in {$city}. Contact seller for more details.";

            Listing::create([
                'user_id' => $vendor->id,
                'make_id' => $make->id,
                'vehicle_model_id' => $model->id,
                'vehicle_type_id' => $type->id,
                'title' => $title,
                'slug' => $slug,
                'description' => $description,
                'price' => $price,
                'year' => $year,
                'mileage' => $mileage,
                'fuel_type' => $fuelType,
                'transmission' => $transmission,
                'condition' => $condition,
                'color' => $color,
                'engine_size' => rand(14, 50) / 10 . 'L',
                'location' => $city,
                'latitude' => $cityData['lat'],
                'longitude' => $cityData['lng'],
                'status' => 'active',
                'is_featured' => rand(0, 100) < 20, // 20% chance of being featured
                'features' => $selectedFeatures,
                'created_at' => now()->subDays(rand(0, 30)),
            ]);
        }

        $this->command->info('✅ Car data seeded successfully!');
        $this->command->info('📊 Created:');
        $this->command->info('   - ' . count($makes) . ' makes');
        $this->command->info('   - ' . $allModels->count() . ' models');
        $this->command->info('   - ' . count($vehicleTypes) . ' vehicle types');
        $this->command->info('   - 100 realistic listings');
        $this->command->info('   - 1 admin + ' . count($vendors) . ' vendor users');
        $this->command->info('');
        $this->command->info('🔑 Login credentials:');
        $this->command->info('   Admin: admin@azalcars.com / admin123');
        $this->command->info('   Vendors: john-dealer@azalcars.com / vendor123 (etc.)');
    }
}
