<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
//use App\Http\Controllers\ErrorController;
//use App\Http\Controllers\LogController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\API\v1\ApiErrorController;
use App\Http\Controllers\API\v1\ApiLogController;

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

Route::post('/test', [Controller::class, 'test']);

//Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//    return $request->user();
//});

Route::middleware('auth:sanctum')->group(function (){

//    Route::post('/v1/add/error', [ErrorController::class, 'apiAdd']);
//    Route::post('/v1/add/log', [LogController::class, 'apiAdd']);

    Route::post('/v1/add/error', [ApiErrorController::class, 'add']);
    Route::post('/v1/add/log', [ApiLogController::class, 'add']);

    //Route::post('/error/page', [ErrorController::class, 'page']);
    //Route::post('/read', [ErrorController::class, 'read']);

});



