<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class VinDecoderService
{
    private const NHTSA_API_URL = 'https://vpic.nhtsa.dot.gov/api/vehicles/DecodeVin';

    /**
     * Decode VIN using NHTSA vPIC API
     *
     * @param string $vin
     * @return array
     */
    public function decode(string $vin): array
    {
        try {
            $response = Http::timeout(10)->get(self::NHTSA_API_URL, [
                'vin' => $vin,
                'format' => 'json',
            ]);

            if (!$response->successful()) {
                Log::warning('NHTSA API request failed', ['vin' => $vin, 'status' => $response->status()]);
                return ['success' => false, 'message' => 'Failed to decode VIN'];
            }

            $data = $response->json();

            if ($data['Count'] === 0) {
                return ['success' => false, 'message' => 'Invalid VIN'];
            }

            return $this->parseNhtsaResponse($data['Results']);
        } catch (\Exception $e) {
            Log::error('VIN decoder error', ['vin' => $vin, 'error' => $e->getMessage()]);
            return ['success' => false, 'message' => 'Error decoding VIN'];
        }
    }

    /**
     * Parse NHTSA API response and extract relevant fields
     *
     * @param array $results
     * @return array
     */
    private function parseNhtsaResponse(array $results): array
    {
        $data = [];

        foreach ($results as $result) {
            $variable = $result['Variable'] ?? '';
            $value = $result['Value'] ?? '';

            switch ($variable) {
                case 'Model Year':
                    $data['year'] = (int) $value;
                    break;
                case 'Make':
                    $data['make'] = $value;
                    break;
                case 'Model':
                    $data['model'] = $value;
                    break;
                case 'Engine Displacement (L)':
                    $data['engine_size'] = $value . 'L';
                    break;
                case 'Transmission Style':
                    $data['transmission'] = $this->normalizeTransmission($value);
                    break;
                case 'Fuel Type - Primary':
                    $data['fuel_type'] = $this->normalizeFuelType($value);
                    break;
                case 'Drive Type':
                    $data['drivetrain'] = $this->normalizeDrivetrain($value);
                    break;
                case 'Body Class':
                    $data['body_type'] = $value;
                    break;
                case 'Number of Doors':
                    $data['doors'] = (int) $value;
                    break;
            }
        }

        if (empty($data['year']) || empty($data['make']) || empty($data['model'])) {
            return ['success' => false, 'message' => 'Incomplete VIN data'];
        }

        return [
            'success' => true,
            'data' => $data,
        ];
    }

    /**
     * Normalize transmission value
     *
     * @param string $value
     * @return string
     */
    private function normalizeTransmission(string $value): string
    {
        $value = strtolower($value);

        if (str_contains($value, 'automatic') || str_contains($value, 'auto')) {
            return 'Automatic';
        }
        if (str_contains($value, 'manual')) {
            return 'Manual';
        }
        if (str_contains($value, 'cvt')) {
            return 'CVT';
        }

        return 'Automatic'; // Default
    }

    /**
     * Normalize fuel type value
     *
     * @param string $value
     * @return string
     */
    private function normalizeFuelType(string $value): string
    {
        $value = strtolower($value);

        if (str_contains($value, 'gasoline') || str_contains($value, 'petrol')) {
            return 'Petrol';
        }
        if (str_contains($value, 'diesel')) {
            return 'Diesel';
        }
        if (str_contains($value, 'electric')) {
            return 'Electric';
        }
        if (str_contains($value, 'hybrid')) {
            return 'Hybrid';
        }

        return 'Petrol'; // Default
    }

    /**
     * Normalize drivetrain value
     *
     * @param string $value
     * @return string
     */
    private function normalizeDrivetrain(string $value): string
    {
        $value = strtoupper($value);

        if (str_contains($value, 'FWD')) {
            return 'FWD';
        }
        if (str_contains($value, 'RWD')) {
            return 'RWD';
        }
        if (str_contains($value, 'AWD')) {
            return 'AWD';
        }
        if (str_contains($value, '4WD')) {
            return '4WD';
        }

        return 'FWD'; // Default
    }
}
