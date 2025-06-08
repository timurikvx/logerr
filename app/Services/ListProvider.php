<?php

namespace App\Services;

use App\Actions\PageOptions;
use App\Actions\Paginate;
use App\Http\Resources\Crew\CrewItemResource;
use App\Http\Resources\Errors\ErrorItemResource;
use App\Interfaces\IListPreferences;
use App\Interfaces\IListProvider;
use App\Interfaces\IListSettings;
use App\Models\Crew;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class ListProvider implements IListProvider
{

    public function __construct(IListPreferences $listPreferences, IListSettings $listSettings)
    {
        $this->listPreferences = $listPreferences;
        $this->listSettings = $listSettings;
    }

    public function get($provider, $team, array $filters = [], array $sort = []): \stdClass
    {
        $query = $provider->get($team);
        return Paginate::paginate($query, $filters, $sort, ErrorItemResource::class);
    }

    public function list($provider, $team, string $title, Request $request): Collection
    {
        $list = $this->updateList($provider, $team);

        $data = PageOptions::get();
        $data->put('title', $title);
        $data->put('crew', (new CrewItemResource($team))->toArray($request));
        $data->put('list', $list['list']);
        $data->put('sort', $list['sort']);
        $data->put('filters', $list['filters']);
        $data->put('columns', $list['columns']);
        $data->put('options', $list['options']);
        $data->put('option', $list['option']);
        $data->put('paginate', $list['paginate']);
        $data->put('head', $title);
        $data->put('prefix', $provider->prefix());
        $data->put('teams', CrewItemResource::collection(Crew::list())->toArray($request));
        $data->put('team', (new CrewItemResource($team))->toArray($request));
        return $data;
    }

    public function updateList($provider, $team): array
    {
        $columns = $this->listPreferences->columns($provider);
        $sort = $this->listPreferences->sort($provider);
        $filters = $this->listPreferences->filters($provider);

        $paginate = $this->get($provider, $team, $filters, $sort);
        $settings = $this->listSettings->settings($provider);
        $setting = $this->listSettings->current($provider);

        $data = array();
        $data['options'] = $settings;
        $data['option'] = $setting;
        $data['list'] = $paginate->data;
        $data['paginate'] = $paginate->paginate;
        $data['filters'] = $filters;
        $data['sort'] = $sort;
        $data['columns'] = $columns;
        return $data;
    }

}
