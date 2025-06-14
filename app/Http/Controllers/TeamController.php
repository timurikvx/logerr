<?php

namespace App\Http\Controllers;

use App\Actions\PageOptions;
use App\Actions\Report;
use App\Http\Resources\Crew\CrewItemResource;
use App\Http\Resources\Crew\CrewMembersResource;
use App\Interfaces\TeamProviderInterface;
use App\Interfaces\UserProviderInterface;
use App\Models\Crew;
use App\Models\Notification;
use App\Models\User;
use App\Services\Teams\TeamValidator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;
use Inertia\Response;

class TeamController extends Controller
{

    private TeamProviderInterface $teamProvider;
    private UserProviderInterface $userProvider;

    public function __construct(TeamProviderInterface $teamProvider, UserProviderInterface $userProvider)
    {
        parent::__construct();
        $this->teamProvider = $teamProvider;
        $this->userProvider = $userProvider;
    }

    public function teams(Request $request): Response
    {
        $list = $this->teamProvider->list();
        $data = PageOptions::get();
        $data->put('title', 'Управление командами');
        $data->put('teams', CrewItemResource::collection($list)->toArray($request));
        return Inertia::render('Teams/Teams', $data);
    }

    public function team(Request $request, string $guid): Response
    {
        $data = PageOptions::get();
        $team = $this->teamProvider->current();
        $members = $this->teamProvider->members($team);

        $data->put('title', 'Команда '.$team->name);
        $data->put('team', (new CrewItemResource($team))->toArray($request));
        $data->put('roles', Crew::roles());
        $data->put('members', CrewMembersResource::collection($members)->toArray($request));
        $data->put('user', Auth::id());
        $data->put('title', 'Выбор команды ошибок');
        return Inertia::render('Teams/Team', $data);
    }

    public function create(Request $request): mixed
    {
        $rules = [
            'name'=>'required|string|max:255',
            //'guid'=>'nullable|string|min:20|alpha_dash:ascii'
        ];

        $validator = Validator::make($request->all(), $rules, [], ['name'=>'Имя', 'guid'=>'Идентификатор']);
        $errors = $validator->errors();
        if(count($errors->all()) > 0){
            return response(['errors'=>$errors->all()], '200');
        }

        $name = $validator->getValue('name');
        //$guid = $validator->getValue('guid');

        $data = $this->teamProvider->create($name);
        if(key_exists('error', $data)){
            return $data;
        }
        $list = $this->teamProvider->list();
        return ['list'=>CrewItemResource::collection($list)->toArray($request)];

    }

    public function list(Request $request): array
    {
        $list = $this->teamProvider->list();
        return ['list'=>CrewItemResource::collection($list)->toArray($request)];
    }

    public function change(Request $request): array
    {
        $guid = $request->get('team');
        $team = $this->teamProvider->change($guid);
        if(is_null($team)){
            return ['error'=>'Команда не найдена'];
        }

        $list = $this->teamProvider->list();
        $data_team = Report::getTop5TodayErrors($team->id);
        $report = ['data'=>$data_team->pluck('value', 'name'), 'guid'=>$team->guid, 'team'=>$team->name];
        $reports = ['today'=>$report];
        return [
            'reports'=>$reports,
            'list'=>CrewItemResource::collection($list)->toArray($request)
        ];

    }

    public function save(Request $request): array
    {
        $name = $request->get('name');
        $guid = $request->get('guid');
        $team = $this->teamProvider->get($guid);
        $this->teamProvider->rename($team, $name);
        return ['result'=>true];
    }

    public function invite(Request $request): array
    {
        $iam = Auth::user();
        $team_guid = $request->get('guid');
        $email = $request->get('email');

        $user = $this->userProvider->getByEmail($email);
        $team = $this->teamProvider->get($team_guid);
        $type = 'invite_to_team';

        $validator = new TeamValidator();
        $errors = $validator->validateInvite($team, $iam, $user, $type);
        if(count($errors) > 0){
            return ['errors'=>$errors];
        }

        $text = 'Вы приглашены в команду '.$team->name.' вступите или проигнорируйте уведомление!';
        Notification::create($type, $user->getID(), 'Приглашение в команду '.$team->name, $text, $team->toArray());
        return ['result'=>true];
    }

    public function roleChange(Request $request): array
    {
        $role = $request->get('role');
        $team_guid = $request->get('team');
        $email = $request->get('email');

        $team = $this->teamProvider->get($team_guid);
        $user = $this->userProvider->getByEmail($email);

        $result = $this->teamProvider->changeRole($team, $user, $role);
        return ['result'=>$result];
    }

    public function exclude(Request $request): array
    {
        $team_guid = $request->get('team');
        $email = $request->get('email');

        $team = $this->teamProvider->get($team_guid);
        $user = $this->userProvider->getByEmail($email);
        $members = $this->teamProvider->exclude($user, $team);
        return [
            'members'=>CrewMembersResource::collection($members)->toArray($request)
        ];
    }

}
