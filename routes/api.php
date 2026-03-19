<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;

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

# ! Rutas Products
# TODO: Llamar a la clase ProductController
Route::get('products/getAllProducts');

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
