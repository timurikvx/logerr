<?php

namespace App\Http\Controllers;

use App\Http\Resources\Notifications\NotificationResource;
use App\Interfaces\TeamProviderInterface;
use App\Interfaces\NotificationProviderInterface;
use Illuminate\Http\Request;

class NotificationController extends Controller
{

    private NotificationProviderInterface $notifications;

    public function __construct(NotificationProviderInterface $notifications)
    {
        parent::__construct();
        $this->notifications = $notifications;
    }

    public function get(Request $request): array
    {
        $list = $this->notifications->get();
        return ['list'=>NotificationResource::collection($list)->toArray($request)];
    }

    public function handle(Request $request): array
    {
        $guid = $request->get('guid');
        $notification = $this->notifications->getByGuid($guid);
        if(!is_null($notification)){
            $this->notifications->handle($notification);
        }
        return $this->get($request);
    }

    public function miss(Request $request): array
    {
        $guid = $request->get('guid');
        $notification = $this->notifications->getByGuid($guid);
        if(!is_null($notification)){
            $this->notifications->miss($notification);
        }
        //$this->notifications->miss($guid);
        return $this->get($request);
    }

    public function complete(Request $request): array
    {
        $guid = $request->get('guid');
        $notification = $this->notifications->getByGuid($guid);
        $this->notifications->complete($notification);
        return $this->get($request);
    }

}
