<?php

namespace App\Models;

use App\Interfaces\Models\NotificationInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Ramsey\Uuid\Uuid;

class Notification extends Model implements NotificationInterface
{
    use HasFactory;

    public function getGuid(): string
    {
        return $this->guid;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getData()
    {
        return json_decode($this->data, true);
    }

    public function getTo()
    {
        return $this->user;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function getFrom()
    {
        return $this->from;
    }

    public function getCompleted(): bool
    {
        return $this->completed;
    }

    public function setCompleted(bool $value): void
    {
        $this->completed = $value;
    }

    public function getText()
    {
        return $this->content;
    }

    public function getMissed(): bool
    {
        return $this->missed;
    }

    public function setMissed(bool $value): void
    {
        $this->missed = $value;
    }

    public static function create($type, $user, $title, $content, $data = null, $url = ''): void
    {
        $notification = new Notification();
        $notification->type = $type;
        $notification->user = $user;
        $notification->guid = Uuid::uuid4()->toString();
        $notification->title = $title;
        $notification->content = $content;
        $notification->data = json_encode($data);
        $notification->url = $url;
        $notification->from = Auth::id();
        $notification->save();
    }

    public static function exist($type, $user): bool
    {
        $date = (new \DateTime())->modify('- 1 week')->format('Y-m-d H:i:s');
        $record = self::query()
            ->where('type', '=', $type)
            ->where('user', '=', $user)
            ->where('completed', '=', false)
            ->where('created_at', '>', $date)
            ->first();
        return $record !== null;
    }

    public static function get(): Collection
    {
        $date = (new \DateTime())->modify('- 1 week')->format('Y-m-d H:i:s');
        $receiver = Auth::id();
        $query = self::query()
            ->where('user', '=', $receiver)
            ->where('created_at', '>=', $date)
            ->where('completed', '=', false)
            ->where('missed', '=', false);
        return $query->get();
    }

    public static function getByGuid($guid): NotificationInterface|null
    {
        $user = Auth::id();
        $notification = self::query()->where('user', '=', $user)->where('guid', '=', $guid)->first();
        if($notification instanceof NotificationInterface){
            return $notification;
        }
        return null;
    }

}
