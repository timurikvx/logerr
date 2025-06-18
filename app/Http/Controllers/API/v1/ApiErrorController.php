<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;
use App\Interfaces\ListWriterProviderInterface;
use App\Interfaces\QueueProviderInterface;
use App\Interfaces\TeamProviderInterface;
use App\Models\Error;
use Illuminate\Http\Request;

class ApiErrorController extends Controller
{
    private ListWriterProviderInterface $listWriterProvider;
    private QueueProviderInterface $queueProvider;

    public function __construct(ListWriterProviderInterface $listWriterProvider, QueueProviderInterface $queueProvider)
    {
        parent::__construct();
        $this->listWriterProvider = $listWriterProvider;
        $this->queueProvider = $queueProvider;
    }

    public function add(Request $request): mixed
    {
        if(count($request->all()) === 0){
            return response(['message'=>'Тело запроса должно быть объектом'], '400');
        }

        $errors = [];
        $this->listWriterProvider->setProvider(new Error());
        $channel = $this->listWriterProvider->channel();
        $message = $this->listWriterProvider->getMessage($request->all(), $errors);
        if(is_null($message)){
            return response(['errors'=>$message], 400);
        }

        $guid = $this->listWriterProvider->getGuid();
        $this->queueProvider->publish(json_encode($message), $channel);
        return ['result'=>true, 'guid'=>$guid];

    }
}
