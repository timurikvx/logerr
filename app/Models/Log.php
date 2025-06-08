<?php

namespace App\Models;

use App\Actions\Filters;
use App\Interfaces\IListModel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Ramsey\Uuid\Uuid;

class Log extends Error implements IListModel
{
    use HasFactory;

    public $timestamps = false;
    public $incrementing = false;

    public static function writeFromText($text): bool
    {
        if(empty($text)){
            return false;
        }
        try{
            $data = json_decode($text, true);
        }catch (\Throwable $e){
            return false;
        }
        $error = $data['error'];
        $user = $data['user'];
        return self::write($error, $user);
    }

    public static function write($data, $user = null): bool
    {
        if(!Auth::check() && $user !== null){
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
        $team = Crew::getByGuid($fields->get('team'));
        if($team == null){
            return false;
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

        if(self::exist($name, $team->id, $date, $guid)){
            return true;
        }

        $log = new Log();
        $log->hash = $hash;
        $log->team = $team->id;
        $log->name = $name;
        $log->date = $date;
        $log->guid = $guid;
        $log->category = $fields->get('category', '');
        $log->sub_category = $fields->get('sub_category', '');
        $log->sender_guid = $fields->get('sender_guid', '');
        $log->sender_name = $fields->get('sender_name', '');
        $log->type = $type;
        $log->code = $fields->get('code', 0);
        $log->user = $fields->get('user', '');
        $log->device = $fields->get('device', '');
        $log->city = $fields->get('city', '');
        $log->region = $fields->get('region', '');
        $log->version = $fields->get('version', '');
        $log->duration = $fields->get('duration', 0);
        $log->data = $log_text;
        $log->len = strlen($log_text);
        $log->query = $log_query;
        $log->query_type = $type_query;
        $log->response = $log_text3;
        $log->response_type = $type3;
        try{
            $log->save();
        }catch (\Throwable $ex){
            dump(mb_substr($ex->getMessage(), 0, 320));
            return false;
        }
        self::saveNames($log, 'logs');
        //Cache::set($hash, $log->toArray(), 200);
        return true;

    }

    public static function exist($name, $team, $date, $guid): bool
    {
        return self::query()
            ->where('name', '=', $name)
            ->where('team', '=', $team)
            ->where('date', '=', $date)
            ->where('guid', '=', $guid)
            ->count() > 0;
    }

    public static function getLogs($team, $filters = [], $sort = []): Builder
    {
        $query = self::query()->where('team', $team);
        return $query;
    }

    //////////////////////////////////////////////////////////////////////////////////

    public function availableColumns(): array
    {
        return [
            ['class'=>'column1', 'name'=>'Дата', 'type'=>'date', 'column'=>'date', 'width'=>1],
            ['class'=>'column2', 'name'=>'Имя', 'type'=>'text', 'column'=>'name', 'width'=>1],
            ['class'=>'column3', 'name'=>'ID', 'type'=>'text', 'column'=>'guid', 'width'=>1],
            ['class'=>'column4', 'name'=>'Категория', 'type'=>'text', 'column'=>'category', 'width'=>1],
            ['class'=>'column5', 'name'=>'Подкатегория', 'type'=>'text', 'column'=>'sub_category', 'width'=>1],
            ['class'=>'column6', 'name'=>'Отправитель', 'type'=>'text', 'column'=>'sender_name', 'width'=>1],
            ['class'=>'column7', 'name'=>'Код', 'type'=>'text', 'column'=>'code', 'width'=>1],
            ['class'=>'column8', 'name'=>'Пользователь', 'type'=>'text', 'column'=>'user', 'width'=>1],
            ['class'=>'column9', 'name'=>'Устройство', 'type'=>'text', 'column'=>'device', 'width'=>1],
            ['class'=>'column10', 'name'=>'Город', 'type'=>'text', 'column'=>'city', 'width'=>1],
            ['class'=>'column11', 'name'=>'Регион', 'type'=>'text', 'column'=>'region', 'width'=>1],
            ['class'=>'column12', 'name'=>'Версия', 'type'=>'text', 'column'=>'version', 'width'=>1],
            ['class'=>'column13', 'name'=>'Длительность', 'type'=>'text', 'column'=>'duration', 'width'=>1],
        ];
    }

    public function cacheSort(): string
    {
        return 'log_sort';
    }

    public function cacheFilters(): string
    {
        return 'log_filters';
    }

    public function cacheColumns(): string
    {
        return 'log_columns';
    }

    public function prefix(): string
    {
        return 'log';
    }


}
