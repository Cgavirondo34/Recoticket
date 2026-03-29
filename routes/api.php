<?php

use App\Http\Controllers\Api\Finance\DashboardController;
use App\Http\Controllers\Api\Finance\ExpenseController;
use App\Http\Controllers\Api\Finance\SettlementController;
use App\Http\Controllers\Api\Field\ReservationController;
use App\Http\Controllers\Api\Gym\GymPaymentController;
use App\Http\Controllers\Api\Gym\GymWebhookController;
use App\Http\Controllers\Api\Gym\MemberController;
use App\Http\Controllers\Api\Gym\MembershipController;
use App\Http\Controllers\Api\Gym\RoutineController;
use App\Http\Controllers\Api\Notification\NotificationController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes — v1
|--------------------------------------------------------------------------
|
| All routes are prefixed with /api/v1 (see bootstrap/app.php routing config).
|
*/

// ─────────────────────────────────────────────────────────────────────────────
// Public webhooks (no auth — protected by signature verification)
// ─────────────────────────────────────────────────────────────────────────────
Route::post('/gym/webhooks/mercado-pago', [GymWebhookController::class, 'mercadoPago'])
    ->name('gym.webhook.mercadopago');

// ─────────────────────────────────────────────────────────────────────────────
// Authenticated API routes
// ─────────────────────────────────────────────────────────────────────────────
Route::middleware('auth')->group(function () {

    // ── Dashboard ──────────────────────────────────────────────────────────
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('api.dashboard');

    // ── Gym: Members ───────────────────────────────────────────────────────
    Route::prefix('gym')->name('gym.')->group(function () {
        Route::apiResource('members', MemberController::class);

        // Memberships
        Route::get('membership-plans', [MembershipController::class, 'plans'])->name('plans');
        Route::post('members/{member}/memberships', [MembershipController::class, 'assign'])->name('memberships.assign');
        Route::get('members/{member}/memberships', [MembershipController::class, 'history'])->name('memberships.history');
        Route::get('memberships/upcoming-expirations', [MembershipController::class, 'upcomingExpirations'])->name('memberships.expiring');

        // Payments
        Route::get('payments', [GymPaymentController::class, 'index'])->name('payments.index');
        Route::post('payments', [GymPaymentController::class, 'store'])->name('payments.store');
        Route::post('payments/{payment}/confirm', [GymPaymentController::class, 'confirm'])->name('payments.confirm');
        Route::get('members/{member}/payments', [GymPaymentController::class, 'memberPayments'])->name('members.payments');

        // Routines & trainer notes
        Route::get('members/{member}/routines', [RoutineController::class, 'index'])->name('routines.index');
        Route::post('members/{member}/routines', [RoutineController::class, 'store'])->name('routines.store');
        Route::get('routines/{routine}', [RoutineController::class, 'show'])->name('routines.show');
        Route::post('members/{member}/trainer-notes', [RoutineController::class, 'storeNote'])->name('trainer-notes.store');
    });

    // ── Field: Reservations ────────────────────────────────────────────────
    Route::prefix('field')->name('field.')->group(function () {
        Route::get('calendar', [ReservationController::class, 'calendar'])->name('calendar');
        Route::apiResource('reservations', ReservationController::class);
        Route::post('reservations/series', [ReservationController::class, 'storeSeries'])->name('reservations.series');
    });

    // ── Finance ────────────────────────────────────────────────────────────
    Route::prefix('finance')->name('finance.')->group(function () {
        // Expenses
        Route::get('expense-categories', [ExpenseController::class, 'categories'])->name('expense-categories');
        Route::apiResource('expenses', ExpenseController::class);

        // Settlement
        Route::get('settlements', [SettlementController::class, 'index'])->name('settlements.index');
        Route::post('settlements/calculate', [SettlementController::class, 'calculate'])->name('settlements.calculate');
        Route::get('settlements/{year}/{month}', [SettlementController::class, 'show'])->name('settlements.show');
        Route::post('settlements/{year}/{month}/close', [SettlementController::class, 'close'])->name('settlements.close');
    });

    // ── Notifications ──────────────────────────────────────────────────────
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('templates', [NotificationController::class, 'templates'])->name('templates');
        Route::put('templates/{template}', [NotificationController::class, 'updateTemplate'])->name('templates.update');
        Route::post('send/member/{member}', [NotificationController::class, 'sendToMember'])->name('send.member');
        Route::get('logs', [NotificationController::class, 'logs'])->name('logs');
    });
});
