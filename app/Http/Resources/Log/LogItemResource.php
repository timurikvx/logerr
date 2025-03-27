<?php

namespace App\Http\Resources\Log;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LogItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'name'=>$this->name,
            'team'=>$this->team,
            'date'=>$this->date,
            'guid'=>$this->guid,
            'category'=>$this->category,
            'sub_category'=>$this->sub_category,
            'sender'=>['name'=>$this->sender_name, 'guid'=>$this->sender_guid],
            'type'=>$this->type,
            'code'=>$this->code,
            'user'=>$this->user,
            'device'=>$this->device,
            'city'=>$this->city,
            'region'=>$this->region,
            'version'=>$this->version,
            'duration'=>$this->duration,
            'data'=>$this->getData($this->type, $this->data),
            'length'=>$this->len
        ];
    }

    private function getData($type, $data): mixed
    {
        if($type === 'json'){
            return json_decode($data, true);
        }
        return $data;
    }
}
