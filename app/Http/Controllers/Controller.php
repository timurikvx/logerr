<?php

namespace App\Http\Controllers;

use App\Actions\Report as Reporting;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    public function __construct()
    {

    }

    public function index(): mixed
    {
        if(Auth::check()){
            return redirect()->route('dashboard');
        }
        return Inertia::render('Welcome', [
            'title'=>'Главная Logerr',
        ]);
    }

    public function test(Request $request): void
    {
        //dump($this->service->roles());
        //dump($service->roles());
    }

}
