<?php
use App\Http\Controllers\Admin;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Buyer;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\Gym;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Organizer;
use App\Http\Controllers\PaymentController;
use Illuminate\Support\Facades\Route;

// Public
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/events/{slug}', [EventController::class, 'show'])->name('events.show');

// Auth routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);
});
Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

// Checkout
Route::middleware('auth')->group(function () {
    Route::get('/events/{slug}/checkout', [CheckoutController::class, 'show'])->name('checkout.show');
    Route::post('/events/{slug}/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
});

// Payment
Route::middleware('auth')->group(function () {
    Route::get('/payment/checkout/{order}', [PaymentController::class, 'checkout'])->name('payment.checkout');
    Route::get('/payment/success', [PaymentController::class, 'success'])->name('payment.success');
    Route::get('/payment/failure', [PaymentController::class, 'failure'])->name('payment.failure');
});
Route::post('/payment/webhook', [PaymentController::class, 'webhook'])->name('payment.webhook');

// Buyer
Route::middleware(['auth'])->prefix('buyer')->name('buyer.')->group(function () {
    Route::get('/dashboard', [Buyer\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/orders', [Buyer\OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [Buyer\OrderController::class, 'show'])->name('orders.show');
    Route::get('/tickets', [Buyer\TicketController::class, 'index'])->name('tickets.index');
    Route::get('/tickets/{ticket}', [Buyer\TicketController::class, 'show'])->name('tickets.show');
});

// Organizer
Route::middleware(['auth', 'role:organizer'])->prefix('organizer')->name('organizer.')->group(function () {
    Route::get('/dashboard', [Organizer\DashboardController::class, 'index'])->name('dashboard');
    Route::resource('/events', Organizer\EventController::class);
    Route::resource('/events.ticket-types', Organizer\TicketTypeController::class)->shallow();
    Route::get('/scan', [Organizer\ScanController::class, 'index'])->name('scan');
    Route::post('/scan', [Organizer\ScanController::class, 'scan'])->name('scan.post');
});

// Admin
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [Admin\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/users', [Admin\UserController::class, 'index'])->name('users.index');
    Route::post('/users/{user}/role', [Admin\UserController::class, 'updateRole'])->name('users.role');
    Route::get('/organizers', [Admin\OrganizerController::class, 'index'])->name('organizers.index');
    Route::post('/organizers/{organizer}/verify', [Admin\OrganizerController::class, 'verify'])->name('organizers.verify');
    Route::get('/events', [Admin\EventController::class, 'index'])->name('events.index');
    Route::post('/events/{event}/publish', [Admin\EventController::class, 'publish'])->name('events.publish');
});

// ── Gym & Football Field Management ──────────────────────────────────────────
Route::middleware(['auth', 'role:admin'])->prefix('gym')->name('gym.')->group(function () {

    // Dashboard
    Route::get('/dashboard', [Gym\DashboardController::class, 'index'])->name('dashboard');

    // Members
    Route::resource('members', Gym\MemberController::class);

    // Membership Plans
    Route::resource('plans', Gym\MembershipPlanController::class)->except(['show']);

    // Payments
    Route::get('/payments', [Gym\GymPaymentController::class, 'index'])->name('payments.index');
    Route::get('/payments/create', [Gym\GymPaymentController::class, 'create'])->name('payments.create');
    Route::post('/payments', [Gym\GymPaymentController::class, 'store'])->name('payments.store');
    Route::get('/payments/{payment}', [Gym\GymPaymentController::class, 'show'])->name('payments.show');
    Route::get('/payments/{payment}/mercadopago', [Gym\GymPaymentController::class, 'mercadoPagoLink'])->name('payments.mercadopago');

    // Routines
    Route::resource('routines', Gym\RoutineController::class);
    Route::post('routines/{routine}/assign', [Gym\RoutineController::class, 'assign'])->name('routines.assign');

    // Reservations (football field)
    Route::get('/reservations', [Gym\ReservationController::class, 'index'])->name('reservations.index');
    Route::get('/reservations/create', [Gym\ReservationController::class, 'create'])->name('reservations.create');
    Route::post('/reservations', [Gym\ReservationController::class, 'store'])->name('reservations.store');
    Route::get('/reservations/{reservation}', [Gym\ReservationController::class, 'show'])->name('reservations.show');
    Route::get('/reservations/{reservation}/edit', [Gym\ReservationController::class, 'edit'])->name('reservations.edit');
    Route::put('/reservations/{reservation}', [Gym\ReservationController::class, 'update'])->name('reservations.update');
    Route::delete('/reservations/{reservation}', [Gym\ReservationController::class, 'destroy'])->name('reservations.destroy');
    Route::post('/reservations/{reservation}/payment', [Gym\ReservationController::class, 'registerPayment'])->name('reservations.payment');

    // Expenses
    Route::resource('expenses', Gym\ExpenseController::class)->except(['show']);

    // Financial Settlement
    Route::get('/settlement', [Gym\SettlementController::class, 'index'])->name('settlement.index');
    Route::get('/settlement/{year}/{month}', [Gym\SettlementController::class, 'show'])->name('settlement.show');
    Route::post('/settlement/generate', [Gym\SettlementController::class, 'generate'])->name('settlement.generate');
    Route::post('/settlement/{year}/{month}/confirm', [Gym\SettlementController::class, 'confirm'])->name('settlement.confirm');

    // Settings
    Route::get('/settings', [Gym\SettingsController::class, 'index'])->name('settings.index');
    Route::post('/settings/plans', [Gym\SettingsController::class, 'storePlan'])->name('settings.plans.store');
    Route::post('/settings/slots', [Gym\SettingsController::class, 'storeSlot'])->name('settings.slots.store');
    Route::put('/settings/slots/{slot}', [Gym\SettingsController::class, 'updateSlot'])->name('settings.slots.update');
    Route::post('/settings/partners', [Gym\SettingsController::class, 'storePartner'])->name('settings.partners.store');
    Route::put('/settings/partners/{partner}', [Gym\SettingsController::class, 'updatePartner'])->name('settings.partners.update');
    Route::post('/settings/templates', [Gym\SettingsController::class, 'storeTemplate'])->name('settings.templates.store');
    Route::put('/settings/templates/{template}', [Gym\SettingsController::class, 'updateTemplate'])->name('settings.templates.update');
    Route::post('/settings/categories', [Gym\SettingsController::class, 'storeCategory'])->name('settings.categories.store');
    Route::post('/settings/trainers', [Gym\SettingsController::class, 'storeTrainer'])->name('settings.trainers.store');
});

// Mercado Pago webhook (no auth required — verified by MP signature)
Route::post('/gym/payments/webhook', [Gym\GymPaymentController::class, 'webhook'])->name('gym.payments.webhook');
