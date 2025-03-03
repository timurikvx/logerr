<?php

namespace App\Http\Controllers;

use App\Models\Crew;
use App\Models\CrewMembers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CrewController extends Controller
{
    public function create(Request $request)
    {
        $rules = [
            'name'=>'required|string|max:255',
            'url'=>'required|string|max:255|alpha_dash:ascii'
        ];

        $validator = Validator::make($request->all(), $rules);
        $errors = $validator->errors();
        if(count($errors->all()) > 0){
            return response(['errors'=>$errors->all()], '200');
        }

        $name = $validator->getValue('name');
        $url = $validator->getValue('url');

        //Crew::create($name, $url);
        return Crew::list();
    }

    public function list(Request $request)
    {
        return Crew::list();
    }

}
