<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\v1\ServiceController;
use App\Http\Controllers\Api\Auth\AuthController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::group([
    'as' => 'api.',
    'namespace' => 'Api\v1'
], function (){

    Route::post('login', [AuthController::class, 'login'])->name('login');

    /**
     * Authenticated Routes
     *
     **/
    Route::group([
        'middleware' => 'auth:sanctum'
    ], function () {

        Route::post('subscription', [ServiceController::class, 'subscription']);
        Route::get('subscription-status/{subscriberId}', [ServiceController::class, 'subscriptionStatus']);
        Route::post('unsubscription', [ServiceController::class, 'unSubscription']);
        Route::get('saved-card-list/{subscriberId}', [ServiceController::class, 'savedCardList']);

    });
});

