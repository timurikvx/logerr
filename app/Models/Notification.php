<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Notification extends Model
{
    use HasFactory;

    public static function create($type, $title, $content, $data = null, $url = ''): void
    {
        $notification = new Notification();
        $notification->type = $type;
        $notification->title = $title;
        $notification->content = $content;
        $notification->data = $data;
        $notification->url = $url;
        $notification->from = Auth::id();
        $notification->save();
    }


}
