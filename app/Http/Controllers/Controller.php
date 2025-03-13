<?php

namespace App\Http\Controllers;

use App\Actions\Filters;
use App\Http\Resources\Crew\CrewItemResource;
use App\Http\Resources\Errors\ErrorItemResource;
use App\Models\Crew;
use App\Models\Error;
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

    public function filters(Request $request): array
    {
        return Filters::equalsByTypes();
    }

    public function choice(Request $request)
    {
        $value = $request->get('value');
        $type = $request->get('type');
        $field = $request->get('field');

        $list = [];
        if($type === 'error'){
            $list = Error::query()->select([$field])->where($field, 'ILIKE', $value.'%')->limit(20)->distinct()->orderBy($field)->get();

        }
        return [
            'list'=>$list->pluck($field)
        ];
    }


}
