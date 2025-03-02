<?php

namespace App\Actions\RabbitMQ;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class LogerrRabbit
{
    public static function publish(string $message, $channel):void
    {
        $connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');
        $channel_object = $connection->channel();

        $channel_object->exchange_declare('errors', 'fanout', false, true);
        $channel_object->queue_declare($channel, false, false, false, false);
        $channel_object->queue_bind($channel, 'errors');

        $msg = new AMQPMessage($message);
        $channel_object->basic_publish($msg, 'errors');

        $channel_object->close();
        $connection->close();
    }

    public static function receive($name):void
    {
        $connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');
        $channel = $connection->channel();

        $channel->queue_declare($name, false, false, false, false);

        echo " [*] Waiting for messages. To exit press CTRL+C\n";

        $callback = function ($msg) {
            $time = microtime(true) * 1000;
            file_put_contents('D:\\logs\\'.$time.'.txt', $msg->getBody());
            echo ' [x] Received ', $msg->getBody(), "\n";
        };

        $channel->basic_consume($name, '', false, true, false, false, $callback);

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
