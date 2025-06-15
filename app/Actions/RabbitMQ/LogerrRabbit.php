<?php

namespace App\Actions\RabbitMQ;

use App\Interfaces\QueueProviderInterface;
use App\Models\Error;
use App\Models\Log;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class LogerrRabbit
{

    private static function getConnection(): AMQPStreamConnection
    {
        $host = config('rabbitmq.host');
        $port = config('rabbitmq.port');
        $user = config('rabbitmq.user');
        $pass = config('rabbitmq.pass');
        return new AMQPStreamConnection($host, $port, $user, $pass);
    }

    public static function publish(string $message, $channel):void
    {
        $connection = self::getConnection();
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

    public static function receive($channel, QueueProviderInterface $provider):void
    {
        $connection = self::getConnection();
        $connect = $connection->channel();

        //$channel->exchange_declare($name, 'fanout', false, true, true);
        //list($queue_name, ,) = $channel->queue_declare("", false, true, true, true);
        $connect->queue_declare($channel, false, false, false, false);
        //$channel->queue_bind($queue_name, $name);

        $callback = function ($msg) use ($provider, $channel) {

            $result = $provider->handle($msg, $channel);
            dump($result);
//            $result = true;
//            if($channel == 'errors'){
//                $result = Error::writeFromText($msg->getBody());
//            }
//            if($channel == 'logs'){
//                $result = Log::writeFromText($msg->getBody());
//            }
            if($result){
                $msg->ack();
            }
        };

        $connect->basic_consume($channel, '', false, false, false, false, $callback);
//        while ($channel->is_open()){
//            $channel->wait();
//        }

        try {
            $connect->consume();
        } catch (\Throwable $exception) {
            echo $exception->getMessage();
        }

        $connect->close();
        $connection->close();

    }

}
