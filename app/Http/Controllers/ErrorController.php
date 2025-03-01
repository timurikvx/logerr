<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Actions\RabbitMQ\LogerrRabbit;

class ErrorController extends Controller
{
    public function apiAdd(Request $request)
    {
        $message = 'Message of time '.(new \DateTime())->modify('+3 hours')->format('Y-m-d H:i:s');
        LogerrRabbit::publish($message, 'New error');
    }

    public function read(Request $request)
    {
        LogerrRabbit::receive('New error');
    }

}
