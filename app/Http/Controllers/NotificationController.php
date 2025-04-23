<?php

namespace App\Http\Controllers;

use App\Http\Resources\Notifications\NotificationResource;
use App\Http\Resources\Telegram\TelegramChatResource;
use App\Models\Crew;
use App\Models\Notification;
use App\Models\NotificationsFields;
use App\Models\NotificationsOption;
use App\Models\TelegramChat;
use App\Models\UserOption;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class NotificationController extends Controller
{

    public function get(Request $request)
    {
        $list = Notification::get();
        return [
            'list'=>NotificationResource::collection($list)->toArray($request)
        ];
    }

    public function confirm(Request $request): array
    {
        $guid = $request->get('guid');
        $notification = Notification::getByGuid($guid);
        if(is_null($notification)){
            return [];
        }
        $type = $notification->type;
        if($type === 'invite_to_team'){
            $data = json_decode($notification->data, true);
            $team = $data['id'];
            Crew::addToTeam(Auth::id(), $team);
        }
        $notification->completed = true;
        $notification->save();
        return $this->get($request);
    }

    public function end(Request $request): array
    {
        $guid = $request->get('guid');
        $notification = Notification::getByGuid($guid);
        $notification->completed = true;
        $notification->save();
        return $this->get($request);
    }

    public function notifications(Request $request): Response
    {
        $team = UserOption::get('current_team');
        $chats = TelegramChat::getChats($team);
        $data = [
            'chats'=>(TelegramChatResource::collection($chats))->toArray($request),
            'notifications'=>[]
        ];
        return Inertia::render('Notifications/Main', $data);
    }

    public function telegram(Request $request)
    {

    }

    public function columns(Request $request): array
    {
        //$type = $request->get('type');
        $list = new ListController();
        $columns = collect($list->columns())->map(function ($item){
            return ['name'=>$item['name'], 'value'=>$item['column']];
        });
        return [
            'columns'=>$columns->toArray()
        ];
    }

    function save(Request $request): array
    {
        $item = collect($request->get('notification'));
        $chat = TelegramChat::getByGuid($item['chat']['guid']);

        $notification = new NotificationsOption();
        $notification->name = $item['name'];
        $notification->type = $request->get('type');
        $notification->chat = $chat->id;
        $notification->minutes = max(0, intval($item['minutes']));
        $notification->count = max(0, intval($item['count']));
        $notification->every = max(0, intval($item['every']));
        $notification->save();

        $fields = $request->get('fields', []);
        foreach ($fields as $field){
            $option_field = new NotificationsFields();
            $option_field->option = $notification->id;
            $option_field->field = $field['field']['value'];
            $option_field->value = $field['value'];
            $option_field->save();
        }
        return $request->all();
    }

}
