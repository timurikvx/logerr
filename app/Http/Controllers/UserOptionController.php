<?php

namespace App\Http\Controllers;

use App\Interfaces\IUserSettingsService;
use App\Models\UserOption;
use Illuminate\Http\Request;

class UserOptionController extends Controller
{

    private IUserSettingsService $userSettings;

    public function __construct(IUserSettingsService $userSettings)
    {
        parent::__construct();
        $this->userSettings = $userSettings;
    }

    public function set(Request $request): array
    {
        $name = $request->get('name');
        $value = $request->get('value');
        $this->userSettings->set($name, 0, $value);
        //UserOption::set($name, 0, $value);
        return ['result'=>true];
    }

    public function get(Request $request): array
    {
        $name = $request->get('name');
        //$value = UserOption::get($name, 0);
        $value = $this->userSettings->get($name, 0);
        return [
            'value'=>$value
        ];
    }

}
