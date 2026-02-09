<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Listing;
use Illuminate\Support\Facades\File;

class GenerateSitemap extends Command
{
    protected $signature = 'sitemap:generate';
    protected $description = 'Generate the sitemap.xml file manually without dependencies';

    public function handle()
    {
        $path = public_path('sitemap.xml');
        $this->info('Generating sitemap...');

        $content = '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
        $content .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . PHP_EOL;

        // Static pages
        $content .= $this->urlItem(route('home'), '1.0', 'daily');
        $content .= $this->urlItem(route('listings.search'), '0.9', 'daily');
        $content .= $this->urlItem(route('login'), '0.8', 'monthly');
        $content .= $this->urlItem(route('register'), '0.8', 'monthly');

        // Dynamic Listings
        $count = 0;
        Listing::where('status', 'active')->chunk(100, function ($listings) use (&$content, &$count) {
            foreach ($listings as $listing) {
                 $content .= $this->urlItem(route('listings.show', $listing->slug), '0.8', 'weekly');
                 $count++;
            }
        });

        $content .= '</urlset>';

        File::put($path, $content);
        $this->info("✅ Sitemap generated successfully with {$count} listings.");
    }

    private function urlItem($loc, $priority, $freq)
    {
        return "\t<url>\n\t\t<loc>{$loc}</loc>\n\t\t<lastmod>" . now()->toAtomString() . "</lastmod>\n\t\t<changefreq>{$freq}</changefreq>\n\t\t<priority>{$priority}</priority>\n\t</url>\n";
    }
}
