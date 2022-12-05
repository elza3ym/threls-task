<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Authentication Routes
Route::group(['prefix' => 'auth'], function () {
    Route::get('login', function () {
        return "Please Login First";
    })->name('login');
    Route::post('login', [AuthController::class, 'login']);
    Route::post('register', [AuthController::class, 'register']);
    Route::post('logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
});

// Authenticated Routes
Route::group(['middleware' => 'auth:sanctum'], function () {
    // Product CRUD Routes
   Route::group(['prefix' => 'products'], function () {
       Route::get('/', [ProductController::class, 'index']);
       Route::get('/{product}/show', [ProductController::class, 'show']);
       Route::post('/import', [ProductController::class, 'import']);
       Route::post('/update-cart', [ProductController::class, 'updateCart']);
   });

   Route::post('/order', [OrderController::class, 'placeOrder']);
});
