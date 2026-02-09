<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use App\Services\NHTSAapiService;
use App\Models\Make;
use App\Models\VehicleModel;

class ImportKoreanCars extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:import-korean-cars {--force : Force update existing records}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import Korean car makes and models from NHTSA API';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting import of Korean cars from NHTSA API...');

        $nhtsaService = new NHTSAapiService();
        $koreanData = $nhtsaService->getKoreanCarData();

        $this->info('Found ' . count($koreanData) . ' Korean makes');

        $force = $this->option('force');
        $makesImported = 0;
        $modelsImported = 0;

        foreach ($koreanData as $makeData) {
            $makeInfo = $makeData['make'];
            $models = $makeData['models'];

            // Create or update make
            $make = Make::updateOrCreate(
                ['name' => $makeInfo['name']],
                [
                    'slug' => $makeInfo['slug']
                ]
            );

            if ($make->wasRecentlyCreated || $force) {
                $makesImported++;
                $this->line("Imported make: {$make->name}");
            }

            // Import models for this make
            foreach ($models as $modelData) {
                $model = VehicleModel::updateOrCreate(
                    [
                        'make_id' => $make->id,
                        'name' => $modelData['name']
                    ],
                    ['slug' => Str::slug($modelData['name'])]
                );

                if ($model->wasRecentlyCreated) {
                    $modelsImported++;
                }
            }

            $this->line("Imported " . count($models) . " models for {$make->name}");
        }

        $this->info("Import completed!");
        $this->info("Makes imported/updated: {$makesImported}");
        $this->info("Models imported: {$modelsImported}");

        // Clear API cache to ensure fresh data
        $nhtsaService->clearCache();
        $this->info('API cache cleared');
    }
}
