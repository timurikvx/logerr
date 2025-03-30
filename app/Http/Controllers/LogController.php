<?php

namespace App\Http\Controllers;

use App\Actions\PageOptions;
use App\Http\Resources\Errors\ErrorItemResource;
use App\Http\Resources\Log\LogItemResource;
use App\Models\LogOption;
use App\Models\Log;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use App\Actions\Paginate;

class LogController extends ListController
{

    protected string $cache_sort = 'log_sort';
    protected string $cache_filters = 'log_filters';
    protected string $cache_columns = 'log_columns';
    protected string $current_option = 'current_option';

    protected string $head = 'Список логов';

    protected string $prefix = 'log';
    protected string $OPTION = LogOption::class;

    public function apiAdd(Request $request)
    {

    }

    public function logs(Request $request): Response
    {
        $data = PageOptions::get();
        $data->put('title', 'Выбор команды просмотра логов');
        return Inertia::render('Logs/Logs', $data);
    }

    public function getListData($team, $filters, $sort): \stdClass
    {
        $query = Log::getLogs($team->id, $filters, $sort);
        return Paginate::paginate($query, LogItemResource::class);
    }

    public function columns(): array
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

    public function filterFields(): array
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
