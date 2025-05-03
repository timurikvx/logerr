<?php

namespace App\Actions\RabbitMQ;

use App\Models\Error;
use App\Models\Log;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class LogerrRabbit
{
    public static function publish(string $message, $channel):void
    {
        $host = env('RABBIT_HOST');
        $port = env('RABBIT_PORT');
        $connection = new AMQPStreamConnection($host, $port, 'guest', 'guest');
        $channel_object = $connection->channel();

        //$channel_object->exchange_declare('errors_exchange', 'fanout', false, true);
        $channel_object->queue_declare($channel, false, false, false, false);
        //$channel_object->queue_bind($channel, 'errors_exchange');

        $msg = new AMQPMessage($message);
        //$key = 'route.errors';
        $channel_object->basic_publish($msg, '', $channel);

        $channel_object->close();
        $connection->close();
    }

    public static function receive($name):void
    {
        $host = env('RABBIT_HOST');
        $port = env('RABBIT_PORT');
        $connection = new AMQPStreamConnection($host, $port, 'guest', 'guest');
        $channel = $connection->channel();

        //$channel->exchange_declare($name, 'fanout', false, true, true);
        //list($queue_name, ,) = $channel->queue_declare("", false, true, true, true);
        $channel->queue_declare($name, false, false, false, false);

        //$channel->queue_bind($queue_name, $name);

        $callback = function ($msg) use ($name) {
            $result = true;
            //dump($name.' '.(microtime(true) * 1000));
            if($name == 'errors'){
                $result = Error::writeFromText($msg->getBody());
            }
            if($name == 'logs'){
                $result = Log::writeFromText($msg->getBody());
            }
            if($result){
                $msg->ack();
            }
        };

        $channel->basic_consume($name, '', false, false, false, false, $callback);

//        while ($channel->is_open()){
//            $channel->wait();
//        }

        try {
            $channel->consume();
        } catch (\Throwable $exception) {
            echo $exception->getMessage();
        }

        $channel->close();
        $connection->close();

    }
}
