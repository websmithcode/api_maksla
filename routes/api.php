<?php

use App\Http\Controllers\SignUpForMaintenanceAPIController;
use App\Http\Controllers\WorksAPIController;
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
Route::middleware('auth.KeyBasedAccess')->post('/upload', function (Request $request) {
				return 'ok';
});

Route::get('/get-works', [WorksAPIController::class, 'getWorks']);
Route::get('/sign-up-for-maintenance', [SignUpForMaintenanceAPIController::class, 'signUp']);
