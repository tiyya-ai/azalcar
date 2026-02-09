<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;

use App\Http\Controllers\FrontendController;
use App\Http\Controllers\StripeWebhookController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\LeadController;

Route::get('/login', function() {
    return view('auth.login');
})->name('login')->middleware('guest');
Route::middleware(['throttle.login'])->group(function () {
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
    Route::post('/register', [AuthController::class, 'register'])->name('register.post');
    Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])->name('password.email');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');
});

// API endpoints with rate limiting
Route::middleware(['throttle.api'])->group(function () {
    Route::get('/api/search/count', [FrontendController::class, 'getSearchCount'])->name('listings.search.count');
    Route::get('/api/models/{makeSlug}', [FrontendController::class, 'getModels']);
    Route::get('/api/korean-makes', [FrontendController::class, 'getKoreanMakes'])->name('api.korean-makes');
});
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Register Page
Route::get('/register', function() {
    return view('auth.register');
})->name('register')->middleware('guest');

// Password Reset Routes
Route::get('/forgot-password', function() {
    return view('auth.forgot-password');
})->name('password.request')->middleware('guest');

Route::get('/reset-password/{token}', function($token) {
    return view('auth.reset-password', ['token' => $token, 'request' => request()]);
})->name('password.reset')->middleware('guest');

Route::get('/', [FrontendController::class, 'index'])->name('home');
Route::get('/support', function() {
    return view('pages.support');
})->name('support');
Route::get('/about', function() {
    return view('pages.about');
})->name('pages.about');
Route::get('/careers', function() {
    return view('pages.careers');
})->name('pages.careers');
Route::get('/privacy', function() {
    return view('pages.privacy');
})->name('pages.privacy');
Route::get('/terms', function() {
    return view('pages.terms');
})->name('pages.terms');
Route::get('/search', [FrontendController::class, 'search'])->name('listings.search');
// Note: API count route is defined inside the throttled API group above. Do not duplicate.
// Stripe webhook endpoint (authoritative for payment events)
Route::post('/webhook/stripe', [StripeWebhookController::class, 'handle'])->name('webhook.stripe');
Route::get('/listing/{slug}', [FrontendController::class, 'showListing'])->name('listings.show');
Route::get('/api/models/{makeSlug}', [FrontendController::class, 'getModels']);
Route::get('/brands', [FrontendController::class, 'allBrands'])->name('brands.index');
Route::get('/currency/switch/{currency}', [FrontendController::class, 'switchCurrency'])->name('currency.switch');

// Lead Tracking
Route::post('/listing/track', [LeadController::class, 'track'])->middleware('throttle.leads')->name('listing.track');
Route::get('/listing/{listing}/reveal-phone', [LeadController::class, 'revealPhone'])->middleware('throttle.leads')->name('listing.reveal-phone');

Route::resource('news', \App\Http\Controllers\NewsController::class)->only(['index', 'show']);

Route::middleware(['auth', 'throttle.forms'])->group(function () {
    Route::get('/post-ad', [FrontendController::class, 'create'])->name('listings.create');
    Route::post('/post-ad', [FrontendController::class, 'store'])->name('listings.store');
    Route::get('/listing/{slug}/edit', [FrontendController::class, 'edit'])->name('listings.frontend.edit');
    Route::put('/listing/{id}', [FrontendController::class, 'update'])->name('listings.frontend.update');
    Route::delete('/listing/{id}', [FrontendController::class, 'destroy'])->name('listings.frontend.destroy');
    
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/dashboard/profile', [DashboardController::class, 'updateProfile'])->name('profile.update');
    
    // Promote Listing
    Route::get('/listing/{slug}/promote', [FrontendController::class, 'promote'])->name('listings.promote');
    Route::post('/listing/{slug}/promote', [FrontendController::class, 'processPromote'])->name('listings.promote.process');

    // Notifications
    Route::get('/notifications', [\App\Http\Controllers\NotificationController::class, 'index'])->name('notifications.index');
    Route::delete('/notifications/{id}', [\App\Http\Controllers\NotificationController::class, 'destroy'])->name('notifications.destroy');

    Route::get('/messages', [\App\Http\Controllers\MessageController::class, 'index'])->name('messages.index');
    Route::get('/messages/{conversation}', [\App\Http\Controllers\MessageController::class, 'show'])->name('messages.show');
    Route::post('/messages/{conversation}/reply', [\App\Http\Controllers\MessageController::class, 'reply'])->name('messages.reply');
    
    // Reviews
    Route::post('/reviews', [\App\Http\Controllers\ReviewController::class, 'store'])->name('reviews.store');
    Route::get('/seller/{seller}/reviews', [\App\Http\Controllers\ReviewController::class, 'sellerReviews'])->name('seller.reviews');
    
    Route::get('/favorites', [\App\Http\Controllers\FavoriteController::class, 'index'])->name('favorites.index');
    Route::post('/favorites/{listingId}/toggle', [\App\Http\Controllers\FavoriteController::class, 'toggle'])->name('favorites.toggle');
    
    Route::get('/wallet', [\App\Http\Controllers\WalletController::class, 'index'])->name('wallet.index');
    Route::post('/wallet/topup', [\App\Http\Controllers\WalletController::class, 'topUp'])->middleware('throttle.uploads')->name('wallet.topup');
    Route::get('/wallet/topup/check', [\App\Http\Controllers\WalletController::class, 'verifyTopUp'])->name('wallet.verify');
    
    // Reservation Routes
    Route::get('/listing/{slug}/reserve', [\App\Http\Controllers\ReservationController::class, 'create'])->name('reservations.create');
    Route::post('/listing/{slug}/reserve', [\App\Http\Controllers\ReservationController::class, 'store'])->middleware('throttle.forms')->name('reservations.store');
    Route::get('/reservations', [\App\Http\Controllers\ReservationController::class, 'index'])->name('reservations.index');
    Route::get('/reservations/{id}', [\App\Http\Controllers\ReservationController::class, 'show'])->name('reservations.show');
    Route::post('/reservations/{id}/extend', [\App\Http\Controllers\ReservationController::class, 'extend'])->name('reservations.extend');
    Route::post('/reservations/{id}/cancel', [\App\Http\Controllers\ReservationController::class, 'cancel'])->name('reservations.cancel');
    Route::post('/reservations/{id}/complete', [\App\Http\Controllers\ReservationController::class, 'complete'])->name('reservations.complete');
    
    // Settings Routes
    Route::get('/settings', [\App\Http\Controllers\User\SettingsController::class, 'index'])->name('settings.index');
    Route::post('/settings/password', [\App\Http\Controllers\User\SettingsController::class, 'updatePassword'])->name('settings.password');
    Route::post('/settings/destroy', [\App\Http\Controllers\User\SettingsController::class, 'destroy'])->name('settings.destroy');
});

Route::middleware(['auth', 'throttle.messages'])->group(function () {
    Route::post('/messages', [\App\Http\Controllers\MessageController::class, 'store'])->name('messages.store');
});

Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('login', [\App\Http\Controllers\Admin\AuthController::class, 'showLogin'])->name('login');
    Route::post('login', [\App\Http\Controllers\Admin\AuthController::class, 'login'])->middleware('throttle.admin_login');
    Route::post('logout', [\App\Http\Controllers\Admin\AuthController::class, 'logout'])->name('logout');

    Route::middleware('admin')->group(function () {
        // 2FA enrollment routes for admins
        Route::get('2fa/setup', [\App\Http\Controllers\Admin\TwoFactorController::class, 'show'])->name('2fa.setup');
        Route::post('2fa/enable', [\App\Http\Controllers\Admin\TwoFactorController::class, 'enable'])->name('2fa.enable');
        Route::post('2fa/disable', [\App\Http\Controllers\Admin\TwoFactorController::class, 'disable'])->name('2fa.disable');
        Route::get('/', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');
        Route::resource('makes', \App\Http\Controllers\Admin\MakeController::class);
        Route::resource('vehicle_types', \App\Http\Controllers\Admin\VehicleTypeController::class);
        Route::resource('packages', \App\Http\Controllers\Admin\PackageController::class);
        Route::resource('vehicle_models', \App\Http\Controllers\Admin\VehicleModelController::class);
        Route::resource('seo', \App\Http\Controllers\Admin\SeoController::class);
        Route::resource('listings', \App\Http\Controllers\Admin\ListingsController::class);
        Route::post('listings/bulk-delete', [\App\Http\Controllers\Admin\ListingsController::class, 'bulkDestroy'])->name('listings.bulk-delete');
        Route::post('listings/{listing}/approve', [\App\Http\Controllers\Admin\ListingsController::class, 'approve'])->name('listings.approve');
        Route::post('listings/{listing}/reject', [\App\Http\Controllers\Admin\ListingsController::class, 'reject'])->name('listings.reject');

        // Users Management
        Route::resource('users', \App\Http\Controllers\Admin\UserController::class);
        Route::post('users/{user}/approve-seller', [\App\Http\Controllers\Admin\UserController::class, 'approveSeller'])->name('users.approve-seller');
        Route::post('users/{user}/reject-seller', [\App\Http\Controllers\Admin\UserController::class, 'rejectSeller'])->name('users.reject-seller');
        Route::post('users/{user}/ban', [\App\Http\Controllers\Admin\UserController::class, 'ban'])->name('users.ban');

        // Transactions
        Route::resource('transactions', \App\Http\Controllers\Admin\TransactionController::class)->only(['index', 'show']);

        // Commissions
        Route::resource('commissions', \App\Http\Controllers\Admin\CommissionController::class)->only(['index', 'show']);
        Route::post('commissions/{commission}/mark-paid', [\App\Http\Controllers\Admin\CommissionController::class, 'markAsPaid'])->name('commissions.mark-paid');
        Route::post('commissions/{commission}/waive', [\App\Http\Controllers\Admin\CommissionController::class, 'waive'])->name('commissions.waive');

        // Reservations
        Route::resource('reservations', \App\Http\Controllers\Admin\ReservationController::class)->only(['index', 'show']);
        Route::post('reservations/{reservation}/expire', [\App\Http\Controllers\Admin\ReservationController::class, 'markAsExpired'])->name('reservations.expire');
        Route::post('reservations/{reservation}/cancel', [\App\Http\Controllers\Admin\ReservationController::class, 'cancel'])->name('reservations.cancel');

        // Messages
        Route::resource('messages', \App\Http\Controllers\Admin\MessageController::class)
             ->only(['index', 'show', 'store', 'destroy'])
             ->parameters(['messages' => 'conversation']);
        Route::get('messages/search-users', [\App\Http\Controllers\Admin\MessageController::class, 'searchUsers'])->name('messages.search-users');
        Route::post('messages/{conversation}/reply', [\App\Http\Controllers\Admin\MessageController::class, 'reply'])->name('messages.reply');

        // Notifications
        Route::resource('notifications', \App\Http\Controllers\Admin\NotificationController::class)->only(['index', 'destroy']);
        Route::post('notifications/read-all', [\App\Http\Controllers\Admin\NotificationController::class, 'readAll'])->name('notifications.readAll');

        // News Management
        Route::resource('news', \App\Http\Controllers\Admin\NewsController::class);

        // System Settings - Main
        Route::get('settings', [\App\Http\Controllers\Admin\SettingsController::class, 'index'])->name('settings.index');
        Route::post('settings/clear-cache', [\App\Http\Controllers\Admin\SettingsController::class, 'clearCache'])->name('settings.clear-cache');
        Route::get('settings/system-info', [\App\Http\Controllers\Admin\SettingsController::class, 'systemInfo'])->name('settings.system-info');

        // Settings Sub-pages
        Route::get('settings/general', [\App\Http\Controllers\Admin\GeneralSettingsController::class, 'index'])->name('settings.general');
        Route::post('settings/general', [\App\Http\Controllers\Admin\GeneralSettingsController::class, 'update'])->name('settings.general.update');

        // Payment Settings
        Route::get('settings/payments', [\App\Http\Controllers\Admin\SettingsController::class, 'payments'])->name('settings.payments');
        Route::post('settings/payments', [\App\Http\Controllers\Admin\SettingsController::class, 'updatePayments'])->name('settings.payments.update');

        Route::get('settings/listings', [\App\Http\Controllers\Admin\ListingSettingsController::class, 'index'])->name('settings.listings');
        Route::post('settings/listings', [\App\Http\Controllers\Admin\ListingSettingsController::class, 'update'])->name('settings.listings.update');

        Route::get('settings/email', [\App\Http\Controllers\Admin\EmailSettingsController::class, 'index'])->name('settings.email');
        Route::post('settings/email', [\App\Http\Controllers\Admin\EmailSettingsController::class, 'update'])->name('settings.email.update');
        Route::post('settings/email/test', [\App\Http\Controllers\Admin\EmailSettingsController::class, 'testEmail'])->name('settings.email.test');

        Route::get('settings/analytics', [\App\Http\Controllers\Admin\AnalyticsSettingsController::class, 'index'])->name('settings.analytics');
        Route::post('settings/analytics', [\App\Http\Controllers\Admin\AnalyticsSettingsController::class, 'update'])->name('settings.analytics.update');

        Route::get('settings/backup', [\App\Http\Controllers\Admin\BackupSettingsController::class, 'index'])->name('settings.backup');
        Route::post('settings/backup', [\App\Http\Controllers\Admin\BackupSettingsController::class, 'update'])->name('settings.backup.update');

        // Backup Management
        Route::get('backups', [\App\Http\Controllers\Admin\BackupController::class, 'index'])->name('backups.index');
        Route::post('backups', [\App\Http\Controllers\Admin\BackupController::class, 'create'])->name('backups.create');
        Route::get('backups/{filename}/download', [\App\Http\Controllers\Admin\BackupController::class, 'download'])->name('backups.download');
        Route::delete('backups/{filename}', [\App\Http\Controllers\Admin\BackupController::class, 'destroy'])->name('backups.destroy');

        // Monitoring and Abuse Detection
        Route::get('monitoring', [\App\Http\Controllers\Admin\MonitoringController::class, 'index'])->name('monitoring.index');
        Route::get('monitoring/suspicious', [\App\Http\Controllers\Admin\MonitoringController::class, 'suspiciousActivity'])->name('monitoring.suspicious');
        Route::get('monitoring/spikes', [\App\Http\Controllers\Admin\MonitoringController::class, 'leadSpikes'])->name('monitoring.spikes');
        Route::post('monitoring/ban/{user}', [\App\Http\Controllers\Admin\MonitoringController::class, 'banUser'])->name('monitoring.ban');
    });
});

// require __DIR__ . '/vuexy_routes.php'; // Commented out to avoid conflicts
