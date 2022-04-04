<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\FacturasController;


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


Route::group(['middleware' => 'api', 'prefix' => 'auth'], function ($router) {
    Route::post('login', [AuthController::class, 'login']);
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('refresh', [AuthController::class, 'refresh']);
    Route::post('me', [AuthController::class, 'me']);
    
    Route::post('register', [AuthController::class, 'register']);
});

Route::group(['middleware' => 'api', 'prefix' => 'facturacion'], function ($router) {
    Route::get('listaFacturas', [FacturasController::class, 'listaFacturas']);
    Route::post('registrar',    [FacturasController::class, 'registrar']);
    Route::post('actualizar',   [FacturasController::class, 'actualizar']);
    Route::delete('eliminar',   [FacturasController::class, 'eliminar']);

});



