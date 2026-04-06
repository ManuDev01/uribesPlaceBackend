<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\StoreController;

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

# ! Rutas Users
Route::get('users/getAllUsers', [UserController::class, 'getAll']);
Route::post('users/registerUser', [UserController::class, 'registerUser']);

# ! Rutas Store
Route::get('stores/getAllStores', [StoreController::class, 'index']);
Route::post('stores/create', [StoreController::class, 'store']);
Route::get('stores/owner/{idOwner}', [StoreController::class, 'showByOwner']);
Route::patch('stores/update/{id}', [StoreController::class, 'update']);
Route::patch('stores/deactivate/{id}', [StoreController::class, 'destroy']);

# ! Rutas Products
Route::get('products/getAllProducts', [ProductController::class, 'getAll']);
Route::post('products/create', [ProductController::class, 'store']);
Route::get('products/show/{id}', [ProductController::class, 'show']);
Route::patch('products/update/{id}', [ProductController::class, 'update']);
Route::patch('products/destroy/{id}', [ProductController::class, 'destroy']);

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
