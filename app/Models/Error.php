<?php

namespace App\Models;

use App\Interfaces\ListModelInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class Error extends Model implements ListModelInterface
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

    public static function writeFromText($text): bool
    {
        return false;
    }

    public static function write($data, $user = null): bool
    {
        return false;
    }

    public static function getErrors($team, $filters = [], $sort = []): Builder
    {
        $query = self::query()->where('team', $team);
        return $query;
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

    public static function clearByTeam($team): void
    {
        self::query()->where('team', '=', $team)->limit(10000)->delete();
    }

    public static function count($team): int
    {
        return self::query()->where('team', '=', $team)->count();
    }

    public static function columns(): array
    {
        return [
            ['class' => 'column1', 'name' => 'Дата', 'type' => 'date', 'column' => 'date', 'width' => 1],
            ['class' => 'column2', 'name' => 'Имя', 'type' => 'text', 'column' => 'name', 'width' => 1],
            ['class' => 'column3', 'name' => 'ID', 'type' => 'text', 'column' => 'guid', 'width' => 1],
            ['class' => 'column4', 'name' => 'Категория', 'type' => 'text', 'column' => 'category', 'width' => 1],
            ['class' => 'column5', 'name' => 'Подкатегория', 'type' => 'text', 'column' => 'sub_category', 'width' => 1],
            ['class' => 'column6', 'name' => 'Отправитель', 'type' => 'text', 'column' => 'sender_name', 'width' => 1],
            ['class' => 'column7', 'name' => 'Код', 'type' => 'text', 'column' => 'code', 'width' => 1],
            ['class' => 'column8', 'name' => 'Пользователь', 'type' => 'text', 'column' => 'user', 'width' => 1],
            ['class' => 'column9', 'name' => 'Устройство', 'type' => 'text', 'column' => 'device', 'width' => 1],
            ['class' => 'column10', 'name' => 'Город', 'type' => 'text', 'column' => 'city', 'width' => 1],
            ['class' => 'column11', 'name' => 'Регион', 'type' => 'text', 'column' => 'region', 'width' => 1],
            ['class' => 'column12', 'name' => 'Версия', 'type' => 'text', 'column' => 'version', 'width' => 1],
            ['class' => 'column13', 'name' => 'Длительность', 'type' => 'text', 'column' => 'duration', 'width' => 1],
        ];
    }

    public static function filters(): array
    {
        return [
            'date' => ['use' => false, 'name' => 'Дата', 'type' => 'datetime-local', 'equal' => null, 'value' => null, 'value2' => null, 'list' => null],
            'name' => ['use' => false, 'name' => 'Имя', 'type' => 'text', 'equal' => null, 'value' => null, 'value2' => null, 'list' => null],
            'category' => ['use' => false, 'name' => 'Категория', 'type' => 'text', 'equal' => null, 'value' => null, 'value2' => null, 'list' => null],
            'sub_category' => ['use' => false, 'name' => 'Подкатегория', 'type' => 'text', 'equal' => null, 'value' => null, 'value2' => null, 'list' => null],
            'user' => ['use' => false, 'name' => 'Пользователь', 'type' => 'text', 'equal' => null, 'value' => null, 'value2' => null, 'list' => null],
            'device' => ['use' => false, 'name' => 'Устройство', 'type' => 'text', 'equal' => null, 'value' => null, 'value2' => null, 'list' => null],
            'city' => ['use' => false, 'name' => 'Город', 'type' => 'text', 'equal' => null, 'value' => null, 'value2' => null, 'list' => null],
            'region' => ['use' => false, 'name' => 'Регион', 'type' => 'text', 'equal' => null, 'value' => null, 'value2' => null, 'list' => null],
            'version' => ['use' => false, 'name' => 'Версия', 'type' => 'text', 'equal' => null, 'value' => null, 'value2' => null, 'list' => null],
            'duration' => ['use' => false, 'name' => 'Длительность', 'type' => 'number', 'equal' => null, 'value' => null, 'value2' => null, 'list' => null],
        ];
    }

    protected static function saveNames($error, $type = 'errors'): void
    {
        $fields = [
            'name',
            'category',
            'sub_category',
            'sender_name',
            'code',
            'user',
            'device',
            'city',
            'region',
        ];
        foreach ($fields as $field) {
            $value = $error->$field;
            if (empty($value)) {
                continue;
            }
            $exist = LogerrNames::query()
                ->where('type', '=', $type)
                ->where('field', '=', $field)
                ->where('value', '=', $value)->exists();
            if ($exist) {
                continue;
            }
            DB::table('logerr_names')->insert([
                'type' => $type,
                'field' => $field,
                'value' => $value
            ]);
        }
    }

    /////////////////////////////////////////////////////////////////////

    public function availableColumns(): array
    {
        return [
            ['class' => 'column1', 'name' => 'Дата', 'type' => 'date', 'column' => 'date', 'width' => 1],
            ['class' => 'column2', 'name' => 'Имя', 'type' => 'text', 'column' => 'name', 'width' => 1],
            ['class' => 'column3', 'name' => 'ID', 'type' => 'text', 'column' => 'guid', 'width' => 1],
            ['class' => 'column4', 'name' => 'Категория', 'type' => 'text', 'column' => 'category', 'width' => 1],
            ['class' => 'column5', 'name' => 'Подкатегория', 'type' => 'text', 'column' => 'sub_category', 'width' => 1],
            ['class' => 'column6', 'name' => 'Отправитель', 'type' => 'text', 'column' => 'sender_name', 'width' => 1],
            ['class' => 'column7', 'name' => 'Код', 'type' => 'text', 'column' => 'code', 'width' => 1],
            ['class' => 'column8', 'name' => 'Пользователь', 'type' => 'text', 'column' => 'user', 'width' => 1],
            ['class' => 'column9', 'name' => 'Устройство', 'type' => 'text', 'column' => 'device', 'width' => 1],
            ['class' => 'column10', 'name' => 'Город', 'type' => 'text', 'column' => 'city', 'width' => 1],
            ['class' => 'column11', 'name' => 'Регион', 'type' => 'text', 'column' => 'region', 'width' => 1],
            ['class' => 'column12', 'name' => 'Версия', 'type' => 'text', 'column' => 'version', 'width' => 1],
            ['class' => 'column13', 'name' => 'Длительность', 'type' => 'text', 'column' => 'duration', 'width' => 1],
        ];
    }

    public function get($team): Builder
    {
        $query = self::query()->where('team', $team->id);
        return $query;
    }

    public function cacheSort(): string
    {
        return 'error_sort';
    }

    public function cacheFilters(): string
    {
        return 'error_filters';
    }

    public function cacheColumns(): string
    {
        return 'error_columns';
    }

    public function prefix(): string
    {
        return 'error';
    }

    public function getFilters(): array
    {
        return [
            'date' => ['use' => false, 'name' => 'Дата', 'type' => 'datetime-local', 'equal' => null, 'value' => null, 'value2' => null, 'list' => null],
            'name' => ['use' => false, 'name' => 'Имя', 'type' => 'text', 'equal' => null, 'value' => null, 'value2' => null, 'list' => null],
            'category' => ['use' => false, 'name' => 'Категория', 'type' => 'text', 'equal' => null, 'value' => null, 'value2' => null, 'list' => null],
            'sub_category' => ['use' => false, 'name' => 'Подкатегория', 'type' => 'text', 'equal' => null, 'value' => null, 'value2' => null, 'list' => null],
            'user' => ['use' => false, 'name' => 'Пользователь', 'type' => 'text', 'equal' => null, 'value' => null, 'value2' => null, 'list' => null],
            'device' => ['use' => false, 'name' => 'Устройство', 'type' => 'text', 'equal' => null, 'value' => null, 'value2' => null, 'list' => null],
            'city' => ['use' => false, 'name' => 'Город', 'type' => 'text', 'equal' => null, 'value' => null, 'value2' => null, 'list' => null],
            'region' => ['use' => false, 'name' => 'Регион', 'type' => 'text', 'equal' => null, 'value' => null, 'value2' => null, 'list' => null],
            'version' => ['use' => false, 'name' => 'Версия', 'type' => 'text', 'equal' => null, 'value' => null, 'value2' => null, 'list' => null],
            'duration' => ['use' => false, 'name' => 'Длительность', 'type' => 'number', 'equal' => null, 'value' => null, 'value2' => null, 'list' => null],
        ];
    }

    public function add(array $data, $team, $user = null): bool
    {
        $fields = collect($data);
        $data = $fields->get('data');
        $date = $fields->get('date');

        $type = $this->getTypeData($data);
        $data_string = $this->prepareData($data);

        $guid = $fields->get('guid', '');
        $name = $fields->get('name');
        $hash = Str::of('error' . $name . $team->id . $date . $guid)->pipe('md5');

        if (self::exist($name, $team->id, $date, $guid)) {
            return true;
        }

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
        $error->data = $data_string;
        $error->duration = $fields->get('duration', 0);
        $error->len = strlen($data_string);
        try {
            $error->save();
        } catch (\Throwable $ex) {
            return false;
        }
        //self::saveNames($error);
        return true;
    }

    private function getTypeData(mixed $data): string
    {
        if(is_array($data)) {
            return 'json';
        }
        return 'text';
    }

    private function prepareData(mixed $data): string
    {
        if(is_array($data)) {
            return json_encode($data);
        }
        if(empty($data)){
            return '';
        }
        return $data;
    }

}
