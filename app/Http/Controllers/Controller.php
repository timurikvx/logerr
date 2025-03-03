<?php

namespace App\Http\Controllers;

use App\Http\Resources\Crew\CrewItemResource;
use App\Models\Crew;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Inertia\Inertia;
use Inertia\Response;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    public function dashboard(Request $request): Response
    {
        return Inertia::render('Dashboard');
    }

    public function errors(Request $request): Response
    {
        return Inertia::render('Errors');
    }

}
