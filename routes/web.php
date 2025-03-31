<?php

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\Controller;
use App\Http\Controllers\CrewController;
use App\Http\Controllers\ErrorController;
use App\Http\Controllers\LogController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\UserOptionController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

//Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified'])->group(function () {
//    Route::get('/dashboard', function () {
//        return Inertia::render('Dashboard');
//    })->name('dashboard');
//});

Route::middleware(['auth'])->group(function(){

    //Pages
    Route::get('/dashboard', [Controller::class, 'dashboard'])->name('dashboard');

    Route::get('/errors', [ErrorController::class, 'errors'])->name('errors');
    Route::get('/errors/{team}', [ErrorController::class, 'getList']);

    Route::get('/teams', [CrewController::class, 'teams'])->name('teams');
    Route::get('/teams/{team}', [CrewController::class, 'team']);

    Route::get('/logs', [LogController::class, 'logs'])->name('logs');
    Route::get('/logs/{team}', [LogController::class, 'getList']);

    //Teams
    Route::post('/team/create', [CrewController::class, 'create']);
    Route::post('/team/list', [CrewController::class, 'list']);
    Route::post('/team/invite', [CrewController::class, 'invite']);
    Route::post('/team/save', [CrewController::class, 'save']);
    Route::post('/team/role/change', [CrewController::class, 'roleChange']);
    Route::post('/team/exclude', [CrewController::class, 'exclude']);

    //Choice
    Route::post('/choice', [Controller::class, 'choice']);

    //Notifications
    Route::post('/notifications/get', [NotificationController::class, 'get']);
    Route::post('/notifications/confirm', [NotificationController::class, 'confirm']);
    Route::post('/notifications/end', [NotificationController::class, 'end']);

    //Common
    Route::post('/filters/get', [Controller::class, 'filters']);
    Route::post('/option/set', [UserOptionController::class, 'set']);
    Route::post('/option/get', [UserOptionController::class, 'get']);

    //Errors
    Route::post('/error/options/set', [ErrorController::class, 'optionSet']);
    Route::post('/error/options/create', [ErrorController::class, 'optionCreate']);
    Route::post('/error/options/save', [ErrorController::class, 'optionSave']);
    Route::post('/error/options/clear', [ErrorController::class, 'optionClear']);
    Route::post('/error/options/change', [ErrorController::class, 'optionChange']);
    Route::post('/error/options/delete', [ErrorController::class, 'optionDelete']);
    Route::post('/error/filter', [ErrorController::class, 'filter']);
    Route::post('/error/page', [ErrorController::class, 'page']);

    //Logs
    Route::post('/log/options/set', [LogController::class, 'optionSet']);
    Route::post('/log/options/create', [LogController::class, 'optionCreate']);
    Route::post('/log/options/clear', [LogController::class, 'optionClear']);
    Route::post('/log/options/save', [LogController::class, 'optionSave']);
    Route::post('/log/options/change', [LogController::class, 'optionChange']);
    Route::post('/log/options/delete', [LogController::class, 'optionDelete']);
    Route::post('/log/filter', [LogController::class, 'filter']);
    Route::post('/log/page', [LogController::class, 'page']);


});
