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
        $date = (new \DateTime());
        Report::countErrorsByWeek($date, 'errors_week');

//        $date = (new \DateTime())->modify('-1 day');
//        Report::countErrorsByDay($date, 'errors_yesterday');
//
//        $date = (new \DateTime())->modify('-2 day');
//        Report::countErrorsByDay($date, 'errors_2days');
//
//        $date = (new \DateTime())->modify('-3 day');
//        Report::countErrorsByDay($date, 'errors_3days');
//
//        $date = (new \DateTime())->modify('-4 day');
//        Report::countErrorsByDay($date, 'errors_4days');
//
//        $date = (new \DateTime())->modify('-5 day');
//        Report::countErrorsByDay($date, 'errors_5days');
//
//        $date = (new \DateTime())->modify('-6 day');
//        Report::countErrorsByDay($date, 'errors_6days');
//
//        $date = (new \DateTime())->modify('-7 day');
//        Report::countErrorsByDay($date, 'errors_7days');

        return ['saad'=>'asdas'];
    }

}
