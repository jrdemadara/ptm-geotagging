<?php

use App\Http\Controllers\InitializeController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\MunicipalityController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RegisterController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

//* Public Routes
Route::post('/v1/uri/register', [RegisterController::class, 'store']);
Route::post('/v1/uri/login', [LoginController::class, 'store']);

//* Protected Routes
Route::group(['middleware' => ['auth:sanctum', 'verified']], function () {
    Route::get('/v1/uri/municipality', [MunicipalityController::class, 'index']);
    Route::get('/v1/uri/initialize', [InitializeController::class, 'index']);
    Route::post('/v1/uri/profile', [ProfileController::class, 'store']);

});
