<?php

use App\Http\Controllers\GeodataController;
use App\Http\Controllers\InitializeAssistanceController;
use App\Http\Controllers\InitializeMemberController;
use App\Http\Controllers\LoginAdminController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\MunicipalityController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProfilesController;
use App\Http\Controllers\RegisterAdminController;
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
Route::get('/v1/uri/municipality', [MunicipalityController::class, 'index']);
Route::post('/v1/uri/register', [RegisterController::class, 'store']);
Route::post('/v1/uri/login', [LoginController::class, 'store']);
Route::post('/v1/uri/admin-register', [RegisterAdminController::class, 'store']);
Route::post('/v1/uri/admin-login', [LoginAdminController::class, 'store']);
Route::get('/v1/uri/geodata', [GeodataController::class, 'index']);
Route::get('/v1/uri/fetch-profiles', [ProfilesController::class, 'index']);

//* Protected Routes
Route::group(['middleware' => ['auth:sanctum', 'verified']], function () {
    Route::get('/v1/uri/initialize-assistance', [InitializeAssistanceController::class, 'index']);
    Route::get('/v1/uri/initialize-member', [InitializeMemberController::class, 'index']);

    Route::middleware(['throttle:uploads'])->group(function () {
        Route::post('/v1/uri/profile', [ProfileController::class, 'store']);

    });
});
