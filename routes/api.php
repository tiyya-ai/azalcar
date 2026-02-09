<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StripeWebhookController;

// Stripe webhook endpoint (stateless API route - no CSRF)
Route::post('/webhook/stripe', [StripeWebhookController::class, 'handle'])->name('api.webhook.stripe');
