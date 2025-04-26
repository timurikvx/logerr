<?php

namespace App\Http\Resources\Notifications;

use App\Http\Controllers\ListController;
use App\Http\Resources\Telegram\TelegramChatResource;
use App\Models\NotificationsFields;
use App\Models\TelegramChat;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NotificationOptionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $type = $this->type;
        $controller = new ListController();
        $columns = collect($controller->columns())->pluck('name','column');

        $chat = TelegramChat::find($this->chat);
        $fields_list = NotificationsFields::query()->select(['field', 'value'])->where('option', '=', $this->id)->get()->toArray();
        $fields = collect([]);
        foreach ($fields_list as $field){
            $fields->push([
                'field'=>['name'=>$columns->get($field['field']), 'value'=>$field['field']],
                'value'=>$field['value']
            ]);
        }
//        dump($fields);
//        $fields = $fields->sortBy([
//            fn (array $a, array $b) => $a['field']['name'] <=> $b['field']['name']
//        ]);

        $types = ['errors'=>'Ошибки', 'logs'=>'Логи'];
        return [
            'name'=>$this->name,
            'guid'=>$this->guid,
            'type'=>['name'=>$types[$type], 'value'=>$type],
            'chat'=>(new TelegramChatResource($chat))->toArray($request),
            'minutes'=>$this->minutes,
            'count'=>$this->count,
            'every'=>$this->every,
            'fields'=>$fields->toArray()
        ];
    }
}
