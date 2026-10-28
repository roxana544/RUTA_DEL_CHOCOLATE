<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\Account;
use App\Http\Controllers\EventController;
use App\Http\Controllers\FactoryController;
use App\Http\Controllers\Manage;
use App\Http\Controllers\MemoryController;
use App\Http\Controllers\PanelController;
use App\Http\Controllers\ParticipationController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PassportController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\TourController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');
Route::get('/idioma/{locale}', function (string $locale) {
    abort_unless(in_array($locale, \App\Http\Middleware\SetLocale::LOCALES, true), 404);
    session(['locale' => $locale]);

    return back();
})->name('locale');

Route::get('/fabricas', [FactoryController::class, 'index'])->name('factories.index');
Route::get('/fabricas/{factory}', [FactoryController::class, 'show'])->name('factories.show');

Route::middleware('guest')->group(function () {
    Route::get('/registro', [Account\AuthController::class, 'showRegister'])->name('register');
    Route::post('/registro', [Account\AuthController::class, 'register'])->middleware('throttle:10,1');
    Route::get('/ingresar', [Account\AuthController::class, 'showLogin'])->name('login');
    Route::post('/ingresar', [Account\AuthController::class, 'login'])->middleware('throttle:10,1');
});
Route::middleware('auth')->group(function () {
    Route::post('/salir', [Account\AuthController::class, 'logout'])->name('logout');
    Route::get('/panel', PanelController::class)->name('panel');
    Route::post('/eventos/{event}/inscribirse', [EventController::class, 'enroll'])->name('events.enroll');
    Route::delete('/eventos/{event}/inscripcion', [EventController::class, 'cancel'])->name('events.cancel');
    Route::get('/eventos/{event}/participar', [ParticipationController::class, 'show'])->name('events.participate');
    Route::post('/eventos/{event}/actividades/{activity}', [ParticipationController::class, 'complete'])->name('events.complete');
    Route::get('/mi-ruta', [PassportController::class, 'index'])->name('passport.index');
    Route::post('/mi-ruta/{factory}', [PassportController::class, 'store'])->name('passport.store');
});

Route::get('/eventos', [EventController::class, 'index'])->name('events.index');
Route::get('/eventos/{event}', [EventController::class, 'show'])->whereNumber('event')->name('events.show');
Route::get('/recuerdos/{memory}', [MemoryController::class, 'show'])->name('memories.show');

Route::get('/tours', [TourController::class, 'index'])->name('tours.index');
Route::get('/tours/{tour}', [TourController::class, 'show'])->name('tours.show');
Route::get('/tours/{tour}/disponibilidad', [TourController::class, 'availability'])->name('tours.availability');
Route::post('/tours/{tour}/reservar', [BookingController::class, 'store'])->name('bookings.store');
Route::get('/reservas/{booking}', [BookingController::class, 'show'])->name('bookings.show');

Route::get('/tienda', ShopController::class)->name('shop');
Route::get('/carrito', [CartController::class, 'index'])->name('cart.index');
Route::post('/carrito/{product}', [CartController::class, 'add'])->name('cart.add');
Route::patch('/carrito/{product}', [CartController::class, 'update'])->name('cart.update');
Route::delete('/carrito/{product}', [CartController::class, 'remove'])->name('cart.remove');
Route::post('/pedido', [OrderController::class, 'store'])->name('orders.store');
Route::get('/pedidos/{order}', [OrderController::class, 'show'])->name('orders.show');

Route::prefix('gestion')->name('manage.')->middleware(['auth', 'manager'])->group(function () {
    Route::get('/', [Manage\EventController::class, 'dashboard'])->name('dashboard');
    Route::get('eventos', [Manage\EventController::class, 'index'])->name('events.index');
    Route::get('mis-eventos', [Manage\EventController::class, 'index'])->name('mine');
    Route::get('eventos/crear', [Manage\EventController::class, 'create'])->name('events.create');
    Route::post('eventos', [Manage\EventController::class, 'store'])->name('events.store');
    Route::get('eventos/{event}', [Manage\EventController::class, 'show'])->name('events.show');
    Route::get('eventos/{event}/editar', [Manage\EventController::class, 'edit'])->name('events.edit');
    Route::put('eventos/{event}', [Manage\EventController::class, 'update'])->name('events.update');
    Route::post('eventos/{event}/iniciar', [Manage\EventController::class, 'start'])->name('events.start');
    Route::post('eventos/{event}/finalizar', [Manage\EventController::class, 'finish'])->name('events.finish');
    Route::post('eventos/{event}/cancelar', [Manage\EventController::class, 'cancel'])->name('events.cancel');
    Route::resource('actividades', Manage\ActivityController::class)->parameters(['actividades' => 'activity'])->names('activities')->except('show');
});

Route::prefix('admin')->name('admin.')->group(function () {
    Route::middleware('guest')->group(function () {
        Route::get('login', [Admin\AuthController::class, 'create'])->name('login');
        Route::post('login', [Admin\AuthController::class, 'store'])->middleware('throttle:10,1');
    });

    Route::middleware(['auth', 'admin'])->group(function () {
        Route::post('logout', [Admin\AuthController::class, 'destroy'])->name('logout');
        Route::get('/', Admin\DashboardController::class)->name('dashboard');
        Route::resource('factories', Admin\FactoryController::class)->except('show');
        Route::resource('stores', Admin\StoreController::class)->except('show');
        Route::resource('tours', Admin\TourController::class)->except('show');
        Route::resource('products', Admin\ProductController::class)->except('show');
        Route::get('users', [Admin\UserController::class, 'index'])->name('users.index');
        Route::patch('users/{user}', [Admin\UserController::class, 'update'])->name('users.update');
        Route::get('checkpoints', Admin\CheckpointController::class.'@index')->name('checkpoints.index');
        Route::get('bookings', [Admin\BookingController::class, 'index'])->name('bookings.index');
        Route::patch('bookings/{booking}', [Admin\BookingController::class, 'update'])->name('bookings.update');
        Route::get('orders', [Admin\OrderController::class, 'index'])->name('orders.index');
        Route::get('orders/{order}', [Admin\OrderController::class, 'show'])->name('orders.show');
        Route::patch('orders/{order}', [Admin\OrderController::class, 'update'])->name('orders.update');
    });
});
