<?php

use App\Http\Controllers\AssistanceController;
use App\Http\Controllers\InitializeAssistanceController;
use App\Http\Controllers\InitializeMemberController;
use App\Http\Controllers\LoginAdminController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\MunicipalityController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProfilesController;
use App\Http\Controllers\RegisterAdminController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\SearchMemberController;
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

//* Public Routes
Route::get("/v1/uri/municipality", [MunicipalityController::class, "index"]);
Route::get("/v1/uri/barangay", [MunicipalityController::class, "barangay"]);
Route::post("/v1/uri/register", [RegisterController::class, "store"]);
Route::post("/v1/uri/login", [LoginController::class, "store"])->name("login");
Route::post("/v1/uri/admin-register", [RegisterAdminController::class, "store"]);
Route::post("/v1/uri/admin-login", [LoginAdminController::class, "store"]);
//Route::get('/v1/uri/geodata', [GeodataController::class, 'index']);
//Route::get('/v1/uri/fetch-profiles', [ProfilesController::class, 'index']);
Route::get("/v1/uri/fetch-images", [ProfilesController::class, "fetchProfileImages"]);
Route::get("/v1/uri/fetch-assistance-by-date", [AssistanceController::class, "fetchByDateRange"]);

//* Protected Routes
Route::group(["middleware" => ["auth:sanctum", "verified"]], function () {
    Route::get("/v1/uri/initialize-assistance", [InitializeAssistanceController::class, "index"]);
    Route::get("/v1/uri/initialize-member", [InitializeMemberController::class, "index"]);
    Route::get("/v1/uri/validate-profile", [AssistanceController::class, "validateProfile"]);
    Route::get("/v1/uri/validate-profile-personal", [
        AssistanceController::class,
        "validateProfilePersonal",
    ]);
    Route::post("/v1/uri/release-assistance", [AssistanceController::class, "save"]);

    Route::get("/v1/uri/search-member", [SearchMemberController::class, "index"]);

    Route::get("/v1/uri/profiles", [ProfileController::class, "index"]);

    Route::middleware(["throttle:uploads"])->group(function () {
        Route::post("/v1/uri/profile", [ProfileController::class, "store"]);
    });
});
