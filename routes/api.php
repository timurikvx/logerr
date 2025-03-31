<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ErrorController;
use App\Http\Controllers\LogController;
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

Route::middleware('auth:sanctum')->group(function (){

    Route::post('/add/error', [ErrorController::class, 'apiAdd']);
    Route::post('/add/log', [LogController::class, 'apiAdd']);
    Route::post('/error/page', [ErrorController::class, 'page']);
    //Route::post('/read', [ErrorController::class, 'read']);
});



