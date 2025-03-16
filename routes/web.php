<?php

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\Controller;
use App\Http\Controllers\CrewController;
use App\Http\Controllers\ErrorController;

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
    Route::get('/{team}/errors', [ErrorController::class, 'errorsTeam']);

    //Teams
    Route::post('/team/create', [CrewController::class, 'create']);
    Route::post('/team/list', [CrewController::class, 'list']);

    //Choice
    Route::post('/choice', [Controller::class, 'choice']);

    //Common
    Route::post('/filters/get', [Controller::class, 'filters']);

    //Errors
    Route::post('/error/options/set', [ErrorController::class, 'optionSet']);
    Route::post('/error/options/save', [ErrorController::class, 'optionSave']);
    Route::post('/error/options/change', [ErrorController::class, 'optionChange']);
    Route::post('/error/options/delete', [ErrorController::class, 'optionDelete']);
    Route::post('/{team}/errors/filter', [ErrorController::class, 'filter']);

    //Logs



});
