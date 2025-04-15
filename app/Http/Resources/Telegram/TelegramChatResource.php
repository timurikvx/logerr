<?php

namespace App\Http\Resources\Telegram;

use App\Models\Crew;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TelegramChatResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $team = Crew::query()->select(['guid', 'name'])->first();
        return [
            'team'=>(is_null($team))? ['guid'=>'', 'name'=>'']: $team->toArray(),
            'name'=> $this->name,
            'token'=>$this->token,
            'chat_id'=>$this->chat_id,
            'guid'=>$this->guid
        ];
    }
}
