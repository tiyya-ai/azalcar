<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\News;
use Illuminate\Support\Str;

class NewsSeeder extends Seeder
{
    public function run()
    {
        // Clear existing news to avoid duplicates
        News::truncate();

        // Featured
        News::create([
            'title' => 'How Much Is the 2026 Ford Bronco Sport? Prices Start at $31,590',
            'slug' => '2026-ford-bronco-sport-prices',
            'image' => 'https://images.unsplash.com/photo-1533473359331-0135ef1b58bf?auto=format&fit=crop&w=1200&q=80',
            'category' => 'Expert Review',
            'excerpt' => 'The refreshed 2026 Ford Bronco Sport gets a new Sasquatch off-road package, updated tech and interior tweaks. Here is what it costs.',
            'content' => "The 2026 Ford Bronco Sport has arrived with a host of updates, making it an even more compelling option in the compact SUV segment. \n\nKey among the changes is the new Sasquatch package, which brings serious off-road capability to the baby Bronco. This includes larger tires, a lifted suspension, and enhanced underbody protection. \n\nInside, the Bronco Sport receives a larger touchscreen infotainment system, running the latest version of Ford's Sync software. Materials have also been upgraded throughout the cabin, giving it a more premium feel.\n\nPricing starts at $31,590 for the base model, with the fully loaded Badlands edition topping out over $45,000.",
            'published_at' => now()->subDays(2),
        ]);

        // List Items
        News::create([
            'title' => 'Mercedes-Benz Restarts Production of EQE, EQS EVs After Pause',
            'slug' => 'mercedes-benz-restarts-ev-production',
            'image' => 'https://images.unsplash.com/photo-1617788138017-80ad40651399?auto=format&fit=crop&w=400&q=80',
            'category' => 'News',
            'excerpt' => 'After a brief hiatus to address supply chain issues, Mercedes-Benz is back on track with its electric vehicle production.',
            'content' => "Mercedes-Benz has resumed production of its EQE and EQS electric sedans. The stoppage was due to a shortage of critical components, specifically related to the battery management systems. \n\nThe company states that full production capacity should be reached within the next few weeks, alleviating the backlog of orders.",
            'published_at' => now()->subDays(5),
        ]);

        News::create([
            'title' => '2027 Ram 2500 Power Wagon: Doing What They Said Could Not Be Done',
            'slug' => '2027-ram-2500-power-wagon-review',
            'image' => 'https://images.unsplash.com/photo-1550355291-6436323e9b76?auto=format&fit=crop&w=400&q=80',
            'category' => 'Review',
            'excerpt' => 'Use a heavy-duty truck for daily driving? The new Power Wagon makes a compelling case for itself.',
            'content' => "The Ram 2500 Power Wagon has always been a beast off-road, but the 2027 model aims to conquer the pavement as well. With a refined coil-spring rear suspension and a luxurious interior that rivals luxury sedans, it's a truck that can truly do it all.",
            'published_at' => now()->subDays(3),
        ]);

        News::create([
            'title' => 'Here Are the 10 Cheapest Pickup Trucks You Can Buy Right Now',
            'slug' => 'cheapest-pickup-trucks-2026',
            'image' => 'https://images.unsplash.com/photo-1583121274602-3e2820c698d9?auto=format&fit=crop&w=400&q=80',
            'category' => 'Analysis',
            'excerpt' => 'Truck prices are high, but bargains still exist. We rank the most affordable options on the market.',
            'content' => "Finding a cheap truck is getting harder, but models like the Ford Maverick and Hyundai Santa Cruz still offer great value. In this list, we break down the top 10 most affordable pickups effectively available on dealer lots today.",
             'published_at' => now()->subDays(1),
        ]);
        
        News::create([
            'title' => 'Test Drive: The All-New Electric Explorer',
            'slug' => 'electric-explorer-test-drive',
            'image' => 'https://images.unsplash.com/photo-1533473359331-0135ef1b58bf?auto=format&fit=crop&w=400&q=80',
            'category' => 'Review',
            'excerpt' => 'Ford goes bold with the new Electric Explorer. Does it have the range to compete?',
            'content' => "We took the new Electric Explorer for a 500-mile road trip to see how it handles real-world conditions. The results were surprising. \n\nRange anxiety was minimal thanks to fast charging speeds, but the interior ergonomics left something to be desired.",
             'published_at' => now()->subDays(10),
        ]);
    }
}
