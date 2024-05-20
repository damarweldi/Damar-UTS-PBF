<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoriesController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;

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
Route::post('login', [AuthController::class, 'login']);
Route::post('register', [AuthController::class, 'register']);
// Route::middleware(['web'])->group(function () {
Route::get('oauth/register', [AuthController::class, 'redirectToGoogle']);
Route::get('oauth/google/callback', [AuthController::class, 'handleGoogleCallback']);
// });
// Route::middleware(['admin'])->group(function () {
Route::post('categories', [CategoriesController::class, 'create_category']);
Route::get('categories', [CategoriesController::class, 'get_category']);
Route::put('categories/{id}', [CategoriesController::class, 'update_category']);
Route::delete('categories/{id}', [CategoriesController::class, 'delete_category']);
// });
// Route::middleware(['auth.jwt'])->group(function () {
Route::get('products', [ProductController::class, 'index']);
Route::get('products/{id}', [ProductController::class, 'show']);
Route::post('products', [ProductController::class, 'addProduct']);
Route::put('products/{id}', [ProductController::class, 'updateProduct']);
Route::delete('products/{id}', [ProductController::class, 'deleteProduct']);
Route::delete('restore/{id}', [ProductController::class, 'restoreProduct']);
// });
