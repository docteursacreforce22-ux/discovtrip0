<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\HomeController;
use App\Http\Controllers\Web\OfferController;
use App\Http\Controllers\Web\BookingController;
use App\Http\Controllers\Web\AccountController;
use App\Http\Controllers\Web\DestinationsController;
use App\Http\Controllers\Web\CountryController;
use App\Http\Controllers\Web\CityController;
use App\Http\Controllers\Web\AboutController;
use App\Http\Controllers\Web\ContactController;
use App\Http\Controllers\Web\ChatbotController;
use App\Http\Controllers\Web\AuthController;
use App\Http\Controllers\Web\WishlistController;
use App\Http\Middleware\EnsureUserIsNotBanned;
use App\Http\Controllers\Web\SitemapController;
use App\Http\Controllers\Web\PaymentController;
use App\Http\Controllers\Web\CguController;
use App\Http\Controllers\Web\PrivacyController;
use App\Http\Controllers\Web\CancellationController;
use App\Http\Controllers\Web\FaqController;
use App\Http\Controllers\Web\BlogController;

// ══════════════════════════════════════════════════════
// ROUTES PUBLIQUES
// ══════════════════════════════════════════════════════

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/search', [HomeController::class, 'search'])->name('search');
Route::post('/newsletter/subscribe', [HomeController::class, 'subscribeNewsletter'])
     ->name('newsletter.subscribe')
     ->middleware('throttle:5,1');

// Destinations & offres
Route::get('/destinations',                        [DestinationsController::class, 'index'])->name('destinations');
Route::get('/destinations/country/{slug}',         [CountryController::class, 'show'])->name('destinations.country');
Route::get('/destinations/{slug}',                 [CityController::class, 'show'])->name('destinations.city');
Route::get('/offers',                 [OfferController::class, 'index'])->name('offers.index');
Route::get('/offers/{slug}',          [OfferController::class, 'show'])->name('offers.show');
Route::get('/offers/{slug}/reserver', [BookingController::class, 'create'])->name('bookings.create');

// Pages statiques
Route::get('/about',   [AboutController::class, 'show'])->name('about');
Route::get('/contact', [ContactController::class, 'show'])->name('contact');
Route::post('/contact', [ContactController::class, 'send'])
     ->name('contact.send')
     ->middleware('throttle:5,1');

// ── Pages légales ─────────────────────────────────────
// CORRECTION : le groupe Route::prefix('legal') a été supprimé.
// Il créait 3 doublons de noms de routes (privacy, cancellation, faq).
// Les anciennes URLs /legal/* sont redirigées en 301 pour préserver le SEO.
Route::redirect('/legal/terms',        '/conditions-utilisation', 301);
Route::redirect('/legal/privacy',      '/confidentialite', 301);
Route::redirect('/legal/cookies',      '/confidentialite', 301);
Route::redirect('/legal/cancellation', '/annulation-gratuite', 301);

Route::get('/conditions-utilisation', [CguController::class, 'show'])->name('cgu');
Route::get('/confidentialite',        [PrivacyController::class, 'show'])->name('privacy');
Route::get('/annulation-gratuite',    [CancellationController::class, 'show'])->name('cancellation');

// ── FAQ — CORRECTION : Route::view() doublon supprimé ─
// FaqController::show() est la version correcte (données dynamiques).
Route::get('/faq', [FaqController::class, 'show'])->name('faq');

// ── Blog ───────────────────────────────────────────────
Route::get('/blog',        [BlogController::class, 'index'])->name('blog.index');
Route::get('/blog/{slug}', [BlogController::class, 'show'])->name('blog.show');

// ══════════════════════════════════════════════════════
// RÉSERVATIONS — PUBLIQUES
// ══════════════════════════════════════════════════════

// CORRECTION : throttle:10,1 ajouté — protège contre les fausses réservations
Route::post('/bookings', [BookingController::class, 'store'])
     ->name('bookings.store')
     ->middleware('throttle:10,1');

// Suivi + PDF — le contrôleur gère lui-même la vérification d'accès
Route::get('/bookings/{reference}',     [BookingController::class, 'show'])->name('bookings.show');
Route::get('/bookings/{reference}/pdf', [BookingController::class, 'pdf'])->name('bookings.pdf');

// Réservations — auth requis
Route::middleware(['auth', EnsureUserIsNotBanned::class])
     ->prefix('bookings')
     ->name('bookings.')
     ->group(function () {
         Route::get('/mes-reservations',     [BookingController::class, 'index'])->name('index');
         Route::patch('/{reference}/cancel', [BookingController::class, 'cancel'])->name('cancel');
     });

// ══════════════════════════════════════════════════════
// PAIEMENT
// ══════════════════════════════════════════════════════

Route::prefix('payment')->name('payment.')->group(function () {
    // Page récapitulatif / sélection paiement
    Route::get('/{reference}', [PaymentController::class, 'show'])->name('show');

    // KKiaPay — callback navigateur après paiement
    Route::get('/{reference}/kkiapay/callback', [PaymentController::class, 'callbackKkiapay'])
         ->name('kkiapay.callback');

    // Paiement sur place — confirmation sans paiement en ligne
    Route::post('/{reference}/on-site', [PaymentController::class, 'confirmOnSite'])
         ->name('on_site.confirm');
});

// Webhook KKiaPay — notification serveur asynchrone (exclu CSRF)
Route::post('/webhooks/kkiapay', [PaymentController::class, 'webhookKkiapay'])
     ->name('webhooks.kkiapay');

// ══════════════════════════════════════════════════════
// CHATBOT & WEBHOOKS
// ══════════════════════════════════════════════════════

Route::post('/chatbot', [ChatbotController::class, 'chat'])
     ->name('chatbot.chat')
     ->middleware('throttle:30,1');



// ══════════════════════════════════════════════════════
// AUTH (visiteurs non connectés seulement)
// ══════════════════════════════════════════════════════

Route::middleware('guest')->group(function () {
    Route::get('/connexion',    [AuthController::class, 'showLogin'])->name('login');
    Route::post('/connexion',   [AuthController::class, 'login'])->name('login.store');
    Route::get('/inscription',  [AuthController::class, 'showRegister'])->name('register');
    Route::post('/inscription', [AuthController::class, 'register'])->name('register.store');

    Route::get('/password/reset',         [AuthController::class, 'showForgotPasswordForm'])->name('password.request');
    Route::post('/password/email',        [AuthController::class, 'sendResetLinkEmail'])->name('password.email');
    Route::get('/password/reset/{token}', [AuthController::class, 'showResetPasswordForm'])->name('password.reset');
    Route::post('/password/reset',        [AuthController::class, 'resetPassword'])->name('password.reset.update');
});

Route::post('/deconnexion', [AuthController::class, 'logout'])->name('logout');

// ══════════════════════════════════════════════════════
// ESPACE CLIENT (auth requis)
// ══════════════════════════════════════════════════════

Route::middleware(['auth', EnsureUserIsNotBanned::class])
     ->prefix('account')
     ->name('account.')
     ->group(function () {
         Route::get('/',         [AccountController::class, 'dashboard'])->name('dashboard');
         Route::get('/bookings', [AccountController::class, 'bookings'])->name('bookings');
         Route::get('/wishlist', [AccountController::class, 'wishlist'])->name('wishlist');
         Route::get('/profile',  [AccountController::class, 'profile'])->name('profile');
         Route::put('/profile',  [AccountController::class, 'updateProfile'])
              ->name('profile.update')
              ->middleware('throttle:20,1');
         Route::get('/security', [AccountController::class, 'security'])->name('security');
         Route::put('/password', [AccountController::class, 'updatePassword'])
              ->name('password.update')
              ->middleware('throttle:5,1');
         Route::delete('/delete', [AccountController::class, 'deleteAccount'])
              ->name('delete')
              ->middleware('throttle:3,1');
     });


// ══════════════════════════════════════════════════════
// WISHLIST
// ══════════════════════════════════════════════════════

Route::middleware(['auth', EnsureUserIsNotBanned::class])
     ->post('/wishlist/toggle', [WishlistController::class, 'toggle'])
     ->name('wishlist.toggle');

Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');