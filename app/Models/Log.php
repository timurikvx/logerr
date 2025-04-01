<?php

namespace App\Models;

use App\Actions\Filters;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Ramsey\Uuid\Uuid;

class Log extends Error
{
    use HasFactory;

    public $timestamps = false;
    public $incrementing = false;

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
        $text = $fields->get('data');
        if(is_array($text)){
            $type = 'json';
            $log_text = json_encode($text);
        }else{
            $type = 'text';
            $log_text = $text;
        }
        $date = $fields->get('date');
        if(empty($date)){
            $date = (new \DateTime())->modify('+3 hours')->format('Y-m-d H:i:s');
        }

        $team = Crew::getByGuid($fields->get('team'));
        if($team == null){
            return;
        }

        $query = $fields->get('query');
        if(is_array($query)){
            $type_query = 'json';
            $log_query = json_encode($query);
        }else{
            $type_query = 'text';
            $log_query = $query;
        }

        $text3 = $fields->get('response');
        if(is_array($text3)){
            $type3 = 'json';
            $log_text3 = json_encode($text3);
        }else{
            $type3 = 'text';
            $log_text3 = $text3;
        }
        $guid = $fields->get('guid', '');
        $name = $fields->get('name');
        $hash = Str::of('log'.$name.$team->id.$date.$guid)->pipe('md5');

        $error = new Log();
        $error->hash = $hash;
        $error->team = $team->id;
        $error->name = $name;
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
        $error->data = $log_text;
        $error->duration = $fields->get('duration', 0);
        $error->len = strlen($log_text);
        $error->query = $log_query;
        $error->query_type = $type_query;
        $error->response = $log_text3;
        $error->response_type = $type3;
        $error->save();

        Cache::set($hash, $error->toArray(), 200);
    }

    public static function getLogs($team, $filters = [], $sort = []): mixed
    {
        $query = self::query()->where('team', $team);
        Filters::setFilters($query, $filters);
        Filters::setSort($query, $sort);
        return $query;
    }

}
