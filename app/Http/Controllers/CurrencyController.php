<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Currency;
use Illuminate\Http\Request;

class CurrencyController extends Controller
{
    public function switch(Currency $currency)
    {
        // Ensure the currency is active
        if (!$currency->is_active) {
            return back()->with('error', 'This currency is not available.');
        }

        // Update user's currency preference
        auth()->user()->update(['currency_id' => $currency->id]);

        return back()->with('success', 'Currency changed to ' . $currency->name . ' (' . $currency->code . ')');
    }
}
