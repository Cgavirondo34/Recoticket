<?php
use App\Http\Controllers\Admin;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Buyer;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\EventController;
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
