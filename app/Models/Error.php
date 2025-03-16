<?php

namespace App\Models;

use App\Actions\Filters;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Ramsey\Uuid\Uuid;

class Error extends Model
{
    use HasFactory;

    public $timestamps = false;

    public static function writeFromText($text): void
    {
        if(empty($text)){
            return;
        }
        try{
            $data = json_decode($text, true);
        }catch (\Throwable $e){
            return;
        }
        $error = $data['error'];
        $user = $data['user'];
        self::write($error, $user);
    }

    public static function write($data, $user = null): void
    {
        if(!Auth::check() && $user != null){
            Auth::login(User::find($user));
        }

        $fields = collect($data);
        $text = $fields->get('text');
        if(is_array($text)){
            $type = 'json';
            $error_text = json_encode($text);
        }else{
            $type = 'text';
            $error_text = $text;
        }
        $date = $fields->get('date');
        if(empty($date)){
            $date = (new \DateTime())->format('Y-m-d H:i:s');
        }

        $team = Crew::getByGuid($fields->get('team'));
        $guid = $fields->get('guid', '');
        if(empty($guid)){
            $guid = Uuid::uuid4()->toString();
        }

        $error = new Error();
        $error->team = $team->id;
        $error->name = $fields->get('name');
        $error->date = $date;
        $error->guid = $guid;
        $error->category = $fields->get('category', '');
        $error->sub_category = $fields->get('sub_category', '');
        $error->sender_guid = $fields->get('sender_guid', '');
        $error->sender_name = $fields->get('sender_name', '');
        $error->type = $type;
        $error->code = $fields->get('code', 0);
        $error->user = $fields->get('user', '');
        $error->device = $fields->get('device', '');
        $error->city = $fields->get('city', '');
        $error->region = $fields->get('region', '');
        $error->version = $fields->get('version', '');
        $error->data = $error_text;
        $error->duration = $fields->get('duration', 0);
        $error->save();
    }

    public static function getErrors($team, $filters = [], $sort = []): mixed
    {
        $crew = Crew::getByGuid($team);
        if(is_null($crew)){
            return null;
        }
        $query = self::query();
        $query->where('team', $crew->id);
        Filters::setFilters($query, $filters);
        Filters::setSort($query, $sort);
        return $query;
    }

}
