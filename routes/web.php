<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Controller;
use App\Http\Controllers\CrewController;
use App\Http\Controllers\ErrorController;
use App\Http\Controllers\LogController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\UserOptionController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TelegramChatController;
use App\Http\Controllers\Errors\ErrorController as NewErrorController;
use App\Http\Controllers\Logs\LogsController as NewLogsController;
use App\Http\Controllers\TeamController;
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

Route::get('/', [Controller::class, 'index'])->name('index');
//Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified'])->group(function () {
//    Route::get('/dashboard', function () {
//        return Inertia::render('Dashboard');
//    })->name('dashboard');
//});

Route::middleware(['auth'])->group(function(){

    ///////////////////////////////// GET //////////////////////////////////////
    ///
    //Pages
    Route::get('/dashboard', [DashboardController::class, 'dashboard'])->name('dashboard');

    //Route::get('/errors', [ErrorController::class, 'getList'])->name('errors');

    //Route::get('/errors/teams/select', [ErrorController::class, 'selectTeam'])->name('selectTeamError');

    //Route::get('/teams', [CrewController::class, 'teams'])->name('teams');
    //Route::get('/errors/teams/select', [CrewController::class, 'selectTeam'])->name('selectTeamLog');

    //Route::get('/teams/{team}', [CrewController::class, 'team']);

    //Route::get('/logs', [LogController::class, 'getList'])->name('logs');

    Route::get('/notifications', [NotificationController::class, 'notifications']);
    Route::get('/notifications/telegram', [NotificationController::class, 'telegram']);
    Route::get('/notifications/{item}', [NotificationController::class, 'notificationOption']);

    ///////////////////////////////// POST //////////////////////////////////////

    //Teams
    //Route::post('/team/change', [DashboardController::class, 'teamChange']);
    //Route::post('/team/create', [CrewController::class, 'create']);
    //Route::post('/team/list', [CrewController::class, 'list']);
    //Route::post('/team/invite', [CrewController::class, 'invite']);
    //Route::post('/team/save', [CrewController::class, 'save']);
    Route::post('/team/role/change', [CrewController::class, 'roleChange']);
    Route::post('/team/exclude', [CrewController::class, 'exclude']);

    //Choice
    Route::post('/choice', [DashboardController::class, 'choice']);

    //Notifications
    Route::post('/notifications/get', [NotificationController::class, 'get']);
    Route::post('/notifications/confirm', [NotificationController::class, 'confirm']);
    Route::post('/notifications/end', [NotificationController::class, 'end']);
    Route::post('/notifications/columns', [NotificationController::class, 'columns']);
    Route::post('/notifications/save', [NotificationController::class, 'save']);

    //Common
    Route::post('/filters/get', [DashboardController::class, 'filters']);
    Route::post('/option/set', [UserOptionController::class, 'set']);
    Route::post('/option/get', [UserOptionController::class, 'get']);

    //Errors
    //Route::post('/error/options/set', [ErrorController::class, 'optionSet']);
    //Route::post('/error/options/create', [ErrorController::class, 'optionCreate']);
    //Route::post('/error/options/save', [ErrorController::class, 'optionSave']);
    //Route::post('/error/options/clear', [ErrorController::class, 'optionClear']);
    //Route::post('/error/options/change', [ErrorController::class, 'optionChange']);
    //Route::post('/error/options/delete', [ErrorController::class, 'optionDelete']);

    //Route::post('/error/team/change', [ErrorController::class, 'teamChange']);
    //Route::post('/error/filter', [ErrorController::class, 'filter']);
    //Route::post('/error/page', [ErrorController::class, 'page']);

    //Logs
    //Route::post('/log/options/set', [LogController::class, 'optionSet']);
    //Route::post('/log/options/create', [LogController::class, 'optionCreate']);

    //Route::post('/log/options/clear', [LogController::class, 'optionClear']);
    //Route::post('/log/options/save', [LogController::class, 'optionSave']);
    //Route::post('/log/options/change', [LogController::class, 'optionChange']);
    //Route::post('/log/options/delete', [LogController::class, 'optionDelete']);

    //Route::post('/log/team/change', [LogController::class, 'teamChange']);
    //Route::post('/log/filter', [LogController::class, 'filter']);
    //Route::post('/log/page', [LogController::class, 'page']);

    Route::post('/telegram/chat/save', [TelegramChatController::class, 'save']);
    Route::post('/telegram/chat/remove', [TelegramChatController::class, 'remove']);
    Route::post('/telegram/chat/teams/get', [TelegramChatController::class, 'getFromTeams']);
    Route::post('/telegram/chat/teams/copy', [TelegramChatController::class, 'copyTeams']);



    //New

    //GET
    Route::get('/errors', [NewErrorController::class, 'errors'])->name('errors');
    Route::get('/logs', [NewLogsController::class, 'logs'])->name('logs');

    Route::get('/teams', [TeamController::class, 'teams'])->name('teams');
    Route::get('/teams/{team}', [TeamController::class, 'team'])->name('team');

    //POST
    Route::post('/team/create', [TeamController::class, 'create']);
    Route::post('/team/list', [TeamController::class, 'list']);
    Route::post('/team/change', [TeamController::class, 'change']);
    Route::post('/team/save', [TeamController::class, 'save']);
    Route::post('/team/invite', [TeamController::class, 'invite']);

    Route::post('/error/filter', [NewErrorController::class, 'filter']);
    Route::post('/log/filter', [NewLogsController::class, 'filter']);

    Route::post('/error/options/create', [NewErrorController::class, 'createSetting']);
    Route::post('/error/options/change', [NewErrorController::class, 'changeSetting']);
    Route::post('/error/options/delete', [NewErrorController::class, 'removeSetting']);
    Route::post('/error/options/save', [NewErrorController::class, 'saveSetting']);
    Route::post('/error/options/set', [NewErrorController::class, 'setPreferences']);
    Route::post('/error/options/clear', [NewErrorController::class, 'clearPreferences']);
    Route::post('/error/page', [NewErrorController::class, 'page']);

    Route::post('/log/options/create', [NewLogsController::class, 'createSetting']);
    Route::post('/log/options/change', [NewLogsController::class, 'changeSetting']);
    Route::post('/log/options/delete', [NewLogsController::class, 'removeSetting']);
    Route::post('/log/options/save', [NewLogsController::class, 'saveSetting']);
    Route::post('/log/page', [NewLogsController::class, 'page']);

    Route::post('/log/options/set', [NewLogsController::class, 'setPreferences']);
    Route::post('/log/options/clear', [NewLogsController::class, 'clearPreferences']);

    Route::post('/error/team/change', [NewErrorController::class, 'changeTeam']);
    Route::post('/log/team/change', [NewLogsController::class, 'changeTeam']);

});
