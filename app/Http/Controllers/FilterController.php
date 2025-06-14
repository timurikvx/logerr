<?php

namespace App\Http\Controllers;

use App\Actions\Filters;
use App\Interfaces\FilterProviderInterface;
use Illuminate\Http\Request;

class FilterController extends Controller
{

    private FilterProviderInterface $filterProvider;

    public function __construct(FilterProviderInterface $filterProvider)
    {
        parent::__construct();
        $this->filterProvider = $filterProvider;
    }

    public function search(Request $request): array
    {
        $value = $request->get('value');
        $type = $request->get('type');
        $field = $request->get('field');

        $list = $this->filterProvider->search($type, $field, $value);
        return [
            'list'=>$list
        ];
    }

    public function filters(): array
    {
        return $this->filterProvider->filters();
    }


}
