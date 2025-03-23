<?php

namespace App\Http\Resources\Crew;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CrewMembersResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'user'=>[
                'id'=>$this['user']->id,
                'name'=>$this['user']->name,
                'surname'=>$this['user']->surname,
                'email'=>$this['user']->email,
                'birth'=>$this['user']->birth,
            ],
            'roles'=>$this['roles']
        ];
    }
}
