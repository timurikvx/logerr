<?php

namespace App\Http\Controllers;

use App\Http\Resources\Notifications\NotificationOptionResource;
use App\Http\Resources\Telegram\TelegramChatResource;
use App\Interfaces\TeamProviderInterface;
use App\Interfaces\TelegramChatProviderInterface;
use App\Interfaces\UserNotificationProviderInterface;
use App\Models\NotificationsOption;
use App\Models\TelegramChat;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Ramsey\Uuid\Guid\Guid;

class UserNotificationController extends Controller
{

    private TeamProviderInterface $teamProvider;
    private TelegramChatProviderInterface $telegramChat;
    private UserNotificationProviderInterface $userNotificationProvider;

    public function __construct(TelegramChatProviderInterface $telegramChat, TeamProviderInterface $teamProvider, UserNotificationProviderInterface $userNotificationProvider)
    {
        parent::__construct();
        $this->telegramChat = $telegramChat;
        $this->teamProvider = $teamProvider;
        $this->userNotificationProvider = $userNotificationProvider;

    }

    public function notifications(Request $request): Response|RedirectResponse
    {
        $team = $this->teamProvider->current();
        if($team == null){
            return redirect()->route('teams');
        }

        $options = NotificationsOption::getOptions($team->getID(), 'errors');
        $chats = TelegramChat::getChats($team->getID());
        $data = [
            'chats'=>(TelegramChatResource::collection($chats))->toArray($request),
            'options'=>NotificationOptionResource::collection($options)->toArray($request)
        ];
        return Inertia::render('Notifications/Main', $data);
    }

    public function save(Request $request): array
    {
        $team = $this->teamProvider->current();

        $item = $request->get('notification');
        $fields = $request->get('fields', []);

        $chatData = collect($item['chat']);
        $chat = $this->telegramChat->getByGuid($chatData->get('guid'));
        $guid = Guid::uuid4()->toString();
        $type = $item['type']['value'];
        $name = $item['name'];
        $minutes = max(0, intval($item['minutes']));
        $count = max(0, intval($item['count']));
        $every = max(0, intval($item['every']));

        $guid_notification = key_exists('guid', $item)? $item['guid']: null;

        if(!empty($guid_notification)){
            $notification = $this->userNotificationProvider->update($team, $guid_notification, $name, $chat->id, $minutes, $count, $every);
        }else{
            $notification = $this->userNotificationProvider->create($team, $guid, $type, $name, $chat->id, $minutes, $count, $every);
        }

        $this->userNotificationProvider->clearFields($notification);
        foreach ($fields as $field){
            $this->userNotificationProvider->writeField($notification, $field['field']['value'], $field['value']);
        }

        $options = $this->userNotificationProvider->list($team, $type);
        return [
            'options'=>NotificationOptionResource::collection($options)->toArray($request)
        ];
    }

    public function columns(Request $request)
    {

    }

}
