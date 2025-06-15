<?php

namespace App\Services;

use App\Interfaces\ListModelInterface;
use App\Interfaces\ListWriterProviderInterface;
use App\Interfaces\TeamProviderInterface;
use App\Models\Error;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Ramsey\Uuid\Uuid;

class ListWriterProvider implements ListWriterProviderInterface
{
    private TeamProviderInterface $teamProvider;
    private string $guid;
    private ListModelInterface $provider;

    public function __construct(TeamProviderInterface $teamProvider)
    {
        //$this->provider = new Error();
        $this->teamProvider = $teamProvider;
        $this->guid = Uuid::uuid4()->toString();
    }

    public function setProvider(ListModelInterface $provider): void
    {
        $this->provider = $provider;
    }

    public function getMessage(array $data, &$errors = []): array|null
    {
        $request = collect($data);

        $date = $request->get('date');
        $team_guid = $request->get('team');
        $team = $this->teamProvider->get($team_guid);
        if(is_null($team)){
            $errors = ['Команда не найдена'];
            return null;
        }

        $rules = $this->validateRules();
        $validator = Validator::make($request->all(), $rules);
        $errors = $validator->errors();
        if(count($errors->all()) > 0){
            $errors = $errors->all();
            return null;
        }

        if(empty($date)){
            $date = (new \DateTime())->modify('+3 hours')->format('Y-m-d H:i:s');
        }

        $user = $this->user();
        $data = $this->prepare($validator, $this->guid, $date);
        return [
            'user'=>$user,
            'data'=>$data,
            'team'=>$team->getID(),
            'provider'=>$this->provider::class
        ];
    }

    public function getGuid(): string
    {
        return $this->guid;
    }

    public function validateRules(): array
    {
        return [
            'team' => 'required|string',
            'name' => 'required|string|max:255',
            'date' => 'nullable|date',
            'category' => 'nullable|string|max:255',
            'sub_category' => 'nullable|string|max:255',
            'sender_guid' => 'nullable|string|max:255',
            'sender_name' => 'nullable|string|max:255',
            'code' => 'nullable|integer|min:0',
            'user' => 'nullable|string|max:255',
            'device' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'region' => 'nullable|string|max:255',
            'version' => 'nullable|string|max:255',
            'data' => 'nullable',
        ];
    }

    public function prepare($validator, $guid, $date): array
    {
        return [
            'guid'=>$guid,
            'name'=>$validator->getValue('name'),
            'date'=> $date,
            'category'=> $validator->getValue('category'),
            'sub_category'=> $validator->getValue('sub_category'),
            'sender_guid'=> $validator->getValue('sender_guid'),
            'sender_name'=> $validator->getValue('sender_name'),
            'code'=> $validator->getValue('code'),
            'user'=> $validator->getValue('user'),
            'device'=> $validator->getValue('device'),
            'city'=> $validator->getValue('city'),
            'region'=> $validator->getValue('region'),
            'version'=> $validator->getValue('version'),
            'data'=> $validator->getValue('data'),
        ];
    }

    public function channel(): string
    {
        return $this->provider->prefix();
    }

    public function user(): int
    {
        return Auth::id();
    }


}
