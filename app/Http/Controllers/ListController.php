<?php

namespace App\Http\Controllers;

use App\Actions\PageOptions;
use App\Actions\Paginate;
use App\Http\Resources\Crew\CrewItemResource;
use App\Http\Resources\Errors\ErrorItemResource;
use App\Models\Crew;
use App\Models\Error;
use App\Models\ErrorOption;
use App\Models\UserOption;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Inertia\Inertia;

class ListController extends Controller
{

    protected string $cache_sort = 'error_sort';
    protected string $cache_filters = 'error_filters';
    protected string $cache_columns = 'error_columns';
    protected string $current_option = 'current_option';

    protected string $head = 'Список ошибок';

    protected string $prefix = 'error';

    protected string $title = 'Список ошибок';

    protected string $OPTION = ErrorOption::class;


    public function getListData($team, $filters, $sort): \stdClass
    {
        $query = Error::getErrors($team->id, [], []); //$filters, $sort
        return Paginate::paginate($query, $filters, $sort, ErrorItemResource::class);
    }

    public function getList(Request $request): mixed
    {
        $setTeam = $request->get('set-team');
        if(!empty($setTeam)){
            $team = Crew::getByGuid($setTeam);
            if(is_null($team)){
                return redirect()->route('dashboard');
            }else{
                UserOption::set('current_team', 0, $team->id);
            }
        }else{
            $team_id = UserOption::get('current_team', 0);
            $team = Crew::getByID($team_id);
        }
        if(is_null($team)){
            return redirect()->route('selectTeamError');
        }
        $data = $this->getData($request, $team);
        return Inertia::render('Errors/ErrorTeam', $data);
    }

    public function getData(Request $request, $team): Collection
    {
        $start = microtime(true) * 1000;
        $guid_option = UserOption::get($this->current_option, $team->id, '');
        $option = $this->OPTION::getByGuid($team->id, $guid_option);
        if(is_null($option)){
            $sort = UserOption::get($this->cache_sort, $team->id, []);
            $filters = UserOption::get($this->cache_filters, $team->id, $this->filterFields());
            $columns = UserOption::get($this->cache_columns, $team->id, $this->columns());
        }else{
            $sort = $option['data']['sort'];
            $filters = $option['data']['filters'];
            $columns = $option['data']['columns'];
        }

        $paginate = $this->getListData($team, $filters, $sort);
        $options = $this->OPTION::getAll($team->id,true);
        $end = microtime(true) * 1000;

        $data = PageOptions::get();
        $data->put('title', $this->title);
        $data->put('guid', $team->guid);
        $data->put('crew', (new CrewItemResource($team))->toArray($request));
        $data->put('list', $paginate->data);
        $data->put('sort', $sort);
        $data->put('filters', $filters);
        $data->put('columns', $columns);
        $data->put('options', $options);
        $data->put('option', $option);
        $data->put('paginate', $paginate->paginate);
        $data->put('time', ($end - $start));
        $data->put('head', $this->head);
        $data->put('prefix', $this->prefix);
        $data->put('teams', CrewItemResource::collection(Crew::list())->toArray($request));
        $data->put('team', (new CrewItemResource($team))->toArray($request));
        return $data;
    }

    public function filter(Request $request): array
    {
        $team_guid = $request->get('team');
        $team = Crew::getByGuid($team_guid);
        $filters = $request->get('filter');
        $sort = $request->get('sort');
        $data = $this->getListData($team, $filters, $sort);
        return [
            'list'=>$data->data,
            'paginate'=>$data->paginate
        ];
    }

    public function page(Request $request): array
    {
        $team_guid = $request->get('team');
        $team = Crew::getByGuid($team_guid);

        $filters = UserOption::get($this->cache_filters, $team->id, []);
        $sort = UserOption::get($this->cache_sort, $team->id, []);
        $data = $this->getListData($team, $filters, $sort);
        return [
            'list'=>$data->data,
            'paginate'=>$data->paginate
        ];
    }

    public function optionSet(Request $request): array
    {
        $team_guid = $request->get('team');
        $team = Crew::getByGuid($team_guid);

        $filters = $request->get('filters');
        $sort = $request->get('sort');
        $columns = $request->get('columns');
        if(!is_null($sort)){
            UserOption::set($this->cache_sort, $team->id, $sort);
            return ['result'=>true];
        }
        if(!is_null($filters)){
            UserOption::set($this->cache_filters, $team->id, $filters);
            return ['result'=>true];
        }
        if(!is_null($columns)){
            UserOption::set($this->cache_columns, $team->id, $columns);
            return ['result'=>true];
        }
        return ['result'=>false];
    }

    public function optionClear(Request $request): array
    {
        $team_guid = $request->get('team');
        $team = Crew::getByGuid($team_guid);

        $field = $request->get('field');
        $name = 'error_'.$field;
        UserOption::remove($name, $team->id);
        return ['result'=>true];
    }

    public function optionCreate(Request $request): array
    {
        $team_guid = $request->get('team');
        $team = Crew::getByGuid($team_guid);

        $name = $request->get('name');

        $data = $this->getOptionData($request);
        $guid = $this->OPTION::set($team->id, $name, $data);

        UserOption::remove($this->cache_sort, $team->id);
        UserOption::remove($this->cache_filters, $team->id);
        UserOption::remove($this->cache_columns, $team->id);
        UserOption::set($this->current_option, $team->id, $guid);

        $option = $this->OPTION::getByGuid($team->id, $guid, true);
        $options = $this->OPTION::getAll($team->id, true);

        return ['result'=>true, 'options'=>$options, 'option'=>$option];
    }

    public function optionSave(Request $request): array
    {
        $team_guid = $request->get('team');
        $team = Crew::getByGuid($team_guid);

        $guid = $request->get('guid');

        $data = $this->getOptionData($request);
        $this->OPTION::updateByGuid($team->id, $guid, $data);

        $this->OPTION::remove($this->cache_sort, $team->id);
        $this->OPTION::remove($this->cache_filters, $team->id);
        $this->OPTION::remove($this->cache_columns, $team->id);
        $this->OPTION::set($this->current_option, $team->id, $guid);

        $option = $this->OPTION::getByGuid($team->id, $guid, true);
        $options = $this->OPTION::getAll($team->id, true);
        return ['result'=>true, 'options'=>$options, 'option'=>$option];
    }

    private function getOptionData(Request $request): array
    {
        return [
            'filters'=>$request->get('filters'),
            'sort'=>$request->get('sort'),
            'columns'=>$request->get('columns')
        ];
    }

    public function optionDelete(Request $request): array
    {
        $team_guid = $request->get('team');
        $team = Crew::getByGuid($team_guid);

        $guid = $request->get('guid');
        ($this->OPTION)::removeByGuid($team->id, $guid);

        $options = ($this->OPTION)::getAll($team->id);

        UserOption::remove($this->current_option, $team->id);
        UserOption::remove($this->cache_sort, $team->id);
        UserOption::remove($this->cache_filters, $team->id);
        UserOption::remove($this->cache_columns, $team->id);

        $data = $this->getListDataByOption($team, []);
        ($this->OPTION)::clearData($options);
        return [
            'options'=>$options,
            'option'=>[],
            'list'=>$data->data,
            'paginate'=>$data->paginate,
            'sort'=>[],
            'filters'=>$this->filterFields(),
            'columns'=>$this->columns()
        ];
    }

    public function optionChange(Request $request): array
    {
        $team_guid = $request->get('team');
        $team = Crew::getByGuid($team_guid);

        $guid = $request->get('guid');
        $option = ($this->OPTION)::getByGuid($team->id, $guid);
        if(is_null($option)){
            UserOption::remove($this->current_option, $team->id);
        }else{
            UserOption::set($this->current_option, $team->id, $guid);
        }

        if(!is_null($option)){
            $data = $option['data'];
        }else{
            $data = [
                'filters'=>$this->filterFields(),
                'columns'=>$this->columns(),
                'sort'=>[]
            ];
            $option = [];
        }
        $result = $this->getListDataByOption($team, $option);
        $data['list'] = $result->data;
        $data['paginate'] = $result->paginate;
        return $data;
    }

    public function teamChange(Request $request): mixed
    {
        $guid = $request->get('team');
        $team = Crew::getByGuid($guid);
        if($team == null){
            return collect([]);
        }
        return $this->getData($request, $team);
    }

    private function getListDataByOption($team, $option): \stdClass
    {
        if(key_exists('data', $option)){
            $data = $option['data'];
        }else{
            $data = ['sort'=>[], 'filters'=>[]];
        }
        return $this->getListData($team, $data['filters'], $data['sort']);
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
            'guid'=>['use'=>false, 'name'=>'Идентификатор', 'type'=>'text', 'equal'=>null, 'value'=>null, 'value2'=>null, 'list'=>null],
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
