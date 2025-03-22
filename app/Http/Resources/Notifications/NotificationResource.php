<?php

namespace App\Http\Resources\Notifications;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NotificationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $confirm = ($this->type === 'invite_to_team');
        return [
            'guid'=>$this->guid,
            'title'=>$this->title,
            'content'=>$this->content,
            'missed'=>$this->missed,
            'completed'=>$this->completed,
            'type'=>$this->type,
            'date'=>$this->created_at,
            'confirm'=>$confirm
        ];
    }
}
