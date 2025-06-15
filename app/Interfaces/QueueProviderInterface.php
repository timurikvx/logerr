<?php

namespace App\Interfaces;

use PhpAmqpLib\Message\AMQPMessage;

interface QueueProviderInterface
{
    function publish(string $message, string $channel): void;

    function receive(string $channel): void;

    function handle(AMQPMessage $msg, $channel): bool;

}
