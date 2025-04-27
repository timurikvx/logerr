<?php

namespace App\Http\Controllers;

use App\Actions\Report as Reporting;
use App\Models\Crew;
use App\Models\NotificationMessage;
use App\Models\NotificationsOption;
use App\Models\Option;
//use Illuminate\Http\Request;
use App\Models\TelegramChat;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Inertia\Inertia;
use Inertia\Response;
use Ramsey\Uuid\Uuid;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    public function index(): mixed
    {
        if(Auth::check()){
            return redirect()->route('dashboard');
        }
        return Inertia::render('Welcome', [
            'title'=>'Главная Logerr',
        ]);
    }

    public function test(): void
    {

    }

}
