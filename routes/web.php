<?php

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\Controller;
use App\Http\Controllers\CrewController;

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

    Route::get('/dashboard', [Controller::class, 'dashboard'])->name('dashboard');
    Route::get('/errors', [Controller::class, 'errors'])->name('errors');
    Route::get('/{team}/errors', [Controller::class, 'errorsTeam']);

    Route::post('/team/create', [CrewController::class, 'create']);
    Route::post('/team/list', [CrewController::class, 'list']);

});
