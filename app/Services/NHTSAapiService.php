<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class NHTSAapiService
{
    protected $baseUrl = 'https://vpic.nhtsa.dot.gov/api/vehicles';

    /**
     * Korean car manufacturers
     */
    protected $koreanMakes = [
        'HYUNDAI',
        'KIA',
        'GENESIS',
        'SSANGYONG',
        'DAEWOO',
        'RENAULT SAMSUNG'
    ];

    /**
     * Get all car makes
     */
    public function getAllMakes()
    {
        return Cache::remember('nhtsa_all_makes', 3600, function () {
            try {
                \Log::info('Calling NHTSA API for makes');
                $response = Http::timeout(30)->withoutVerifying()->get("{$this->baseUrl}/getallmakes?format=json");
                \Log::info('NHTSA API response status: ' . $response->status());

                if ($response->successful()) {
                    $data = $response->json();
                    \Log::info('NHTSA API response data count: ' . count($data['Results'] ?? []));

                    $allMakes = collect($data['Results'] ?? []);
                    \Log::info('Total makes from API: ' . $allMakes->count());

                    $koreanMakesFiltered = $allMakes->filter(function ($make) {
                        $makeName = strtoupper($make['Make_Name'] ?? '');
                        \Log::info('Checking make: ' . $makeName);
                        return in_array($makeName, $this->koreanMakes);
                    });

                    \Log::info('Korean makes found: ' . $koreanMakesFiltered->count());

                    $result = $koreanMakesFiltered->map(function ($make) {
                        return [
                            'id' => $make['Make_ID'] ?? null,
                            'name' => $make['Make_Name'] ?? '',
                            'slug' => Str::slug($make['Make_Name'] ?? '')
                        ];
                    })->values()->toArray();

                    \Log::info('Final result count: ' . count($result));
                    return $result;
                }

                \Log::error('NHTSA API failed for makes', ['status' => $response->status(), 'body' => $response->body()]);
                return [];
            } catch (\Exception $e) {
                \Log::error('NHTSA API exception for makes', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
                return [];
            }
        });
    }

    /**
     * Get models for a specific make
     */
    public function getModelsForMake($makeName)
    {
        $cacheKey = 'nhtsa_models_' . Str::slug($makeName);

        return Cache::remember($cacheKey, 3600, function () use ($makeName) {
            try {
                $response = Http::timeout(30)->withoutVerifying()->get("{$this->baseUrl}/GetModelsForMake/" . urlencode($makeName) . "?format=json");

                if ($response->successful()) {
                    $data = $response->json();
                    return collect($data['Results'] ?? [])
                        ->map(function ($model) {
                            $name = $model['Model_Name'] ?? '';
                            return [
                                'id' => $model['Model_ID'] ?? null,
                                'name' => $name,
                                'slug' => Str::slug($name),
                                'make_name' => $model['Make_Name'] ?? ''
                            ];
                        })
                        ->values()
                        ->toArray();
                }

                Log::error('NHTSA API failed for models', [
                    'make' => $makeName,
                    'response' => $response->body()
                ]);
                return [];
            } catch (\Exception $e) {
                Log::error('NHTSA API exception for models', [
                    'make' => $makeName,
                    'error' => $e->getMessage()
                ]);
                return [];
            }
        });
    }

    /**
     * Get all Korean car data (makes and models)
     */
    public function getKoreanCarData()
    {
        $makes = $this->getAllMakes();
        $result = [];

        foreach ($makes as $make) {
            $models = $this->getModelsForMake($make['name']);
            $result[] = [
                'make' => $make,
                'models' => $models
            ];
        }

        return $result;
    }

    /**
     * Clear cache
     */
    public function clearCache()
    {
        Cache::forget('nhtsa_all_makes');

        // Clear all model caches
        $makes = $this->getAllMakes();
        foreach ($makes as $make) {
            Cache::forget('nhtsa_models_' . $make['slug']);
        }
    }
}