<?php

namespace App\Actions\RabbitMQ;

use App\Models\Error;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class LogerrRabbit
{
    public static function publish(string $message, $channel):void
    {
        $connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');
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
        $connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');
        $channel = $connection->channel();

        //$channel->exchange_declare($name, 'fanout', false, true, true);
        //list($queue_name, ,) = $channel->queue_declare("", false, true, true, true);
        $channel->queue_declare($name, false, false, false, false);

        //$channel->queue_bind($queue_name, $name);

        $callback = function ($msg) {
            $time = microtime(true) * 10000;
            dump('message '.$time);
            sleep(7);
            file_put_contents('D:\\logs\\'.$time.'.txt', $msg->getBody());
            Error::writeFromText($msg->getBody());
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
