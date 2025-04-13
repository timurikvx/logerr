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
        return [
            'team'=>Crew::query()->select(['guid', 'name'])->first()->toArray(),
            'name'=> $this->name,
            'token'=>$this->token,
            'chat_id'=>$this->chat_id,
            'guid'=>$this->guid
        ];
    }
}
