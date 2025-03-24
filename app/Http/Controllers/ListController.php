<?php

namespace App\Http\Controllers;

use App\Actions\Filters;
use App\Actions\Paginate;
use App\Http\Resources\Crew\CrewItemResource;
use App\Http\Resources\Errors\ErrorItemResource;
use App\Models\Crew;
use App\Models\Error;
use App\Models\UserOption;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ListController extends Controller
{
    public function getListData($team, $filters, $sort): \stdClass
    {
        $path = '/errors/'.$team->guid;
        $query = Error::getErrors($team->id, $filters, $sort);
        return Paginate::paginate($query, $path, ErrorItemResource::class);
    }

    public function getList(Request $request, $guid): Response
    {
        $start = microtime(true) * 1000;
        $team = Crew::getByGuid($guid);
        $guid_option = UserOption::get($this->current_option, $team->id, '');
        $option = $this->OPTION::getByGuid($team->id, $guid_option);
        if(is_null($option)){
            $sort = UserOption::get($this->cache_sort, $team->id, []);
            $filters = UserOption::get($this->cache_filters, $team->id, Filters::filters());
            $columns = UserOption::get($this->cache_columns, $team->id, Filters::columns());
        }else{
            $sort = $option['data']['sort'];
            $filters = $option['data']['filters'];
            $columns = $option['data']['columns'];
        }

        $paginate = $this->getListData($team, $filters, $sort);

        $options = $this->OPTION::getAll($team->id,true);
        $end = microtime(true) * 1000;
        $data = [
            'title'=>'Список ошибок',
            'guid'=>$guid,
            'crew'=> (new CrewItemResource($team))->toArray($request),
            'errors'=>$paginate->data,
            'sort'=>$sort,
            'filters'=>$filters,
            'columns'=>$columns,
            'options'=>$options,
            'option'=>$option,
            'paginate'=>$paginate->paginate,
            'time'=>($end - $start),
            'head'=>$this->head,
            'prefix'=>$this->prefix
        ];
        return Inertia::render('Errors/ErrorTeam', $data);
    }


    public function filter(Request $request): array
    {
        $team_guid = $request->get('team');
        $team = Crew::getByGuid($team_guid);
        $filters = $request->get('filter');
        $sort = $request->get('sort');
        //$errors = Error::getErrors($team->id, $filters, $sort)->limit(20)->get();
        $data = $this->getListData($team, $filters, $sort);
        return [
            'errors'=>$data->data,//ErrorItemResource::collection($errors)->toArray($request)
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
            'errors'=>$data->data,
            'paginate'=>$data->paginate,
            'sort'=>[],
            'filters'=>Filters::filters(),
            'columns'=>Filters::columns()
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
                'filters'=>Filters::filters(),
                'columns'=>Filters::columns(),
                'sort'=>[]
            ];
            $option = [];
        }
        $result = $this->getListDataByOption($team, $option);
        $data['errors'] = $result->data;
        $data['paginate'] = $result->paginate;
        return $data;
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

}
