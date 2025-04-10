<?php

namespace App\Http\Controllers;

use App\Http\Resources\Notifications\NotificationResource;
use App\Models\Crew;
use App\Models\Notification;
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
        return Inertia::render('Notifications/Main');
    }

    public function telegram(Request $request)
    {

    }


}
