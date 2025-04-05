<?php

namespace App\Http\Controllers;


use App\Actions\Filters;
use App\Actions\PageOptions;
use App\Actions\Report;
use App\Models\Error;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    public function index()
    {
        if(Auth::check()){
            return redirect()->route('dashboard');
        }
        return Inertia::render('Welcome', [
            'title'=>'Главная Logerr',
        ]);
    }

    public function dashboard(Request $request): Response
    {
        $data = PageOptions::get();
        $data->put('title', 'Панель управления');
        return Inertia::render('Dashboard', $data);
    }

    public function filters(Request $request): array
    {
        return Filters::equalsByTypes();
    }

    public function choice(Request $request): array
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

    public function test(Request $request): array
    {

        return ['result'=>true];
    }

}
