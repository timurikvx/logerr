<?php

namespace App\Http\Controllers;

use App\Models\UserOption;
use Illuminate\Http\Request;

class UserOptionController extends Controller
{
    public function set(Request $request): array
    {
        $name = $request->get('name');
        $value = $request->get('value');
        UserOption::set($name, 0, $value);
        return ['result'=>true];
    }

    public function get(Request $request): array
    {
        $name = $request->get('name');
        $value = UserOption::get($name, 0);
        return [
            'value'=>$value
        ];
    }

}
