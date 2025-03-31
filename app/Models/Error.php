<?php

namespace App\Models;

use App\Actions\Filters;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Ramsey\Uuid\Uuid;

class Error extends Model
{
    use HasFactory;

    public $timestamps = false;

    public $incrementing = false;

    protected $fillable = [
        'hash',
        'name',
        'team',
        'date',
        'guid',
        'category',
        'sub_category',
        'sender_guid',
        'sender_name',
        'type',
        'code',
        'user',
        'device',
        'city',
        'region',
        'version',
        'duration',
        'data'
    ];

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
            $date = (new \DateTime())->modify('+3 hours')->format('Y-m-d H:i:s');
        }

        $team = Crew::getByGuid($fields->get('team'));
        $guid = $fields->get('guid', '');
        if(empty($guid)){
            $guid = Uuid::uuid4()->toString();
        }

        $name = $fields->get('name');
        //$hash = Hash::make('error'.$name.$team->id.$date.$guid);
        $hash = Str::of('error'.$name.$team->id.$date.$guid)->pipe('md5');

        $error = new Error();
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
        $error->data = $error_text;
        $error->duration = $fields->get('duration', 0);
        $error->len = strlen($error_text);
        $error->save();

        Cache::set($hash, $error->toArray(), 200);

    }

    public static function getErrors($team, $filters = [], $sort = []): Builder
    {
        $query = self::query()->where('team', $team);
        Filters::setFilters($query, $filters);
        Filters::setSort($query, $sort);
        return $query;
    }

    public static function columns(): array
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

    public static function filters(): array
    {
        return [
            'date'=>['use'=>false, 'name'=>'Дата', 'type'=>'datetime-local', 'equal'=>null, 'value'=>null, 'value2'=>null, 'list'=>null],
            'name'=>['use'=>false, 'name'=>'Имя', 'type'=>'text', 'equal'=>null, 'value'=>null, 'value2'=>null, 'list'=>null],
            'category'=>['use'=>false, 'name'=>'Категория', 'type'=>'text', 'equal'=>null, 'value'=>null, 'value2'=>null, 'list'=>null],
            'sub_category'=>['use'=>false, 'name'=>'Подкатегория', 'type'=>'text', 'equal'=>null, 'value'=>null, 'value2'=>null, 'list'=>null],
            'user'=>['use'=>false, 'name'=>'Пользователь', 'type'=>'text', 'equal'=>null, 'value'=>null, 'value2'=>null, 'list'=>null],
            'device'=>['use'=>false, 'name'=>'Устройство', 'type'=>'text', 'equal'=>null, 'value'=>null, 'value2'=>null, 'list'=>null],
            'city'=>['use'=>false, 'name'=>'Город', 'type'=>'text', 'equal'=>null, 'value'=>null, 'value2'=>null, 'list'=>null],
            'region'=>['use'=>false, 'name'=>'Регион', 'type'=>'text', 'equal'=>null, 'value'=>null, 'value2'=>null, 'list'=>null],
            'version'=>['use'=>false, 'name'=>'Версия', 'type'=>'text', 'equal'=>null, 'value'=>null, 'value2'=>null, 'list'=>null],
            'duration'=>['use'=>false, 'name'=>'Длительность', 'type'=>'number', 'equal'=>null, 'value'=>null, 'value2'=>null, 'list'=>null],
        ];
    }

}
