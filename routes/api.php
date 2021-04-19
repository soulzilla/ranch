<?php

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('v1/yards', [\App\Http\Controllers\Api\V1\YardsController::class, 'index']);

Route::post('v1/sheep', [\App\Http\Controllers\Api\V1\SheepController::class, 'store']);
Route::put('v1/sheep', [\App\Http\Controllers\Api\V1\SheepController::class, 'update']);
Route::delete('v1/sheep', [\App\Http\Controllers\Api\V1\SheepController::class, 'destroy']);

Route::get('v1/history', [\App\Http\Controllers\Api\V1\HistoryController::class, 'index']);
