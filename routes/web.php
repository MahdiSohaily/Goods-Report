<?php

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\GoodController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\RateController;
use App\Http\Controllers\RelationController;
use App\Http\Controllers\PriceController;
use Inertia\Inertia;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return Inertia::render('Auth/Login', [
        'canLogin' => Route::has('login'),
        'canRegister' => false,
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {

    // Search Page related routs
    Route::controller(SearchController::class)->group(function () {
        Route::get('/dashboard', 'index')->name('dashboard'); // Dashboard route lead us to the search page
        Route::post('/search', 'search')->name('search'); // Route to manage searched path
        Route::get('/mobis/{mobis}', 'mobis')->name('mobis'); // Route to check if a good has mobis
    });

    // Good Page related routs
    Route::controller(GoodController::class)->group(function () {
        Route::get('/goods', 'index')->name('goods'); // goods route lead us to the goods page
        Route::post('/goods/search', 'search')->name('goods.search'); // goods route lead us to the goods page
        Route::post('/goods/page', 'page')->name('goods.page'); // goods route lead us to the goods page
        Route::get('/goods/create', 'create')->name('goods.create'); // Route to show the form for creating a new good in the system
        Route::post('/goods', 'store')->name('goods.store'); // Route to create new good in the system
        Route::get('/goods/{goods}/edit', 'edit')->name('goods.edit'); // Route to show the selected good for update
        Route::put('/goods/{goods}', 'update')->name('goods.update'); // Route to update an existing good
        Route::delete('/goods/{goods}', 'delete')->name('goods.delete'); // Route to delete a specific good
    });

    // Rate Page related routs
    Route::controller(RateController::class)->group(function () {
        Route::get('/rates', 'index')->name('rates'); // rates route lead us to the rates page
        Route::get('/rates/create', 'create')->name('rates.create'); // Route to show the form for creating a new rate in the system
        Route::post('/rates', 'store')->name('rates.store'); // Route to create new good in the system
        Route::post('/rate/toggle/selected', 'toggleSelected')->name('rates.toggle'); // Route to create new good in the system
        Route::get('/rates/{rates}/edit', 'edit')->name('rates.edit'); // Route to show the selected good for update
        Route::put('/rates/{rates}', 'update')->name('rates.update'); // Route to update an existing good
        Route::delete('/rates/{rates}', 'delete')->name('rates.delete'); // Route to delete a specific good
    });

    // Rate Page related routs
    Route::controller(RelationController::class)->group(function () {
        Route::get('/relations', 'index')->name('relations'); // relations route lead us to the relations page
        Route::post('/relations/search', 'search')->name('relations.search'); // Route to create new good in the system
        Route::post('/relations/load', 'load')->name('relations.load'); // Route to create new good in the system
        Route::post('/relations', 'store')->name('relations.store'); // Route to create new good in the system
        Route::post('/relations/pattern', 'pattern')->name('relations.pattern'); // Route to create new good in the system
        Route::put('/relations', 'update')->name('relations.update'); // Route to create new good in the system
    });

    // Price Page related routs
    Route::controller(PriceController::class)->group(function () {
        Route::get('/price', 'index')->name('price.give'); // price route lead us to the price page
        Route::get('/price/load', 'load')->name('price.load'); // Route to create new good in the system
        Route::post('/price', 'store')->name('price.store'); // Route to create new good in the system
        Route::get('/test', 'test')->name('test_rates'); // Route to create new good in the system

        Route::post('/price/info', 'info')->name('price.info'); // Route to create new good in the system
        Route::post('/price/relations', 'relations')->name('price.relations'); // Route to create new good in the system
        Route::post('/price/customer/name', 'getCustomerName')->name('price.getCustomerName'); // Route to create new good in the system
        Route::post('/price/ask', 'askPrice')->name('price.askPrice'); // Route to create new good in the system


        Route::post('/price/setup', 'pricesetup')->name('price.setup'); // Route to create new good in the system
    });

    // Notification related routs
    Route::controller(NotificationController::class)->group(function () {
        Route::get('/notification', 'index')->name('notification.give'); // notification route lead us to the notification page
        Route::get('/notification/get', 'getNotification')->name('notification.get'); // notification route lead us to the notification page
        Route::post('/notification/clear', 'clearNotification')->name('notification.clear'); // notification route lead us to the notification page
        Route::post('/notification/read', 'readNotification')->name('notification.read'); // notification route lead us to the notification page
    });
});
