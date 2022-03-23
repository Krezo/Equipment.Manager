<?php

use App\Http\Controllers\EquipmentController;
use App\Http\Controllers\EquipmentTypeController;
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

Route::get('equipment/s', [EquipmentController::class, 'search']);
Route::apiResource('equipment', EquipmentController::class);
Route::apiResource('equipment_type', EquipmentTypeController::class);


Route::fallback(function (Request $request) {
  return response()->json([
    'message' => 'Wrong api path'
  ], 404);
});
