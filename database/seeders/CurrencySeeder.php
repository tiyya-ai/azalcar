<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CurrencySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $currencies = [
            [
                'code' => 'MAD',
                'name' => 'Moroccan Dirham',
                'symbol' => 'DH',
                'exchange_rate' => 10.120000,
                'is_active' => true
            ],
            [
                'code' => 'KRW',
                'name' => 'Korean Won',
                'symbol' => '₩',
                'exchange_rate' => 1350.000000,
                'is_active' => true
            ],
            [
                'code' => 'USD',
                'name' => 'US Dollar',
                'symbol' => '$',
                'exchange_rate' => 1.000000,
                'is_active' => true
            ],
            [
                'code' => 'EUR',
                'name' => 'Euro',
                'symbol' => '€',
                'exchange_rate' => 0.920000,
                'is_active' => true
            ],
            [
                'code' => 'GBP',
                'name' => 'British Pound',
                'symbol' => '£',
                'exchange_rate' => 0.790000,
                'is_active' => true
            ],
            [
                'code' => 'CAD',
                'name' => 'Canadian Dollar',
                'symbol' => 'C$',
                'exchange_rate' => 1.350000,
                'is_active' => true
            ],
            [
                'code' => 'AUD',
                'name' => 'Australian Dollar',
                'symbol' => 'A$',
                'exchange_rate' => 1.520000,
                'is_active' => true
            ],
            [
                'code' => 'JPY',
                'name' => 'Japanese Yen',
                'symbol' => '¥',
                'exchange_rate' => 150.000000,
                'is_active' => true
            ],
            [
                'code' => 'CHF',
                'name' => 'Swiss Franc',
                'symbol' => 'CHF',
                'exchange_rate' => 0.880000,
                'is_active' => true
            ],
            [
                'code' => 'CNY',
                'name' => 'Chinese Yuan',
                'symbol' => '¥',
                'exchange_rate' => 7.250000,
                'is_active' => true
            ],
            [
                'code' => 'INR',
                'name' => 'Indian Rupee',
                'symbol' => '₹',
                'exchange_rate' => 83.000000,
                'is_active' => true
            ],
            [
                'code' => 'BRL',
                'name' => 'Brazilian Real',
                'symbol' => 'R$',
                'exchange_rate' => 5.200000,
                'is_active' => true
            ]
        ];

        foreach ($currencies as $currency) {
            \App\Models\Currency::updateOrCreate(
                ['code' => $currency['code']],
                $currency
            );
        }
    }
}
