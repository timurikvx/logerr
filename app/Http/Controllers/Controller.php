<?php

namespace App\Http\Controllers;

use App\Models\Option;
//use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;
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
        $option = Option::query()->where('guid', '=', '4f326142-bb90-4859-9e28-c808f04cebac')
//            ->where('user', '=', 1)
//            ->where('team', '=', 2)
//            ->where('name', '=', 'Пипирка')
//            ->where('category', '=', 'category 1')
            ->first();
//            ->update(['data'=>'2']);
        $option->data = json_encode(['ssss', '13']);
        $option->save();

//        $option = new Option();
//        $option->user = 1;
//        $option->team = 2;
//        $option->name = 'Пипирка';
//        $option->guid = Uuid::uuid4()->toString();
//        $option->category = 'category 1';
//        $option->save();

    }

}
