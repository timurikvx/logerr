<?php

namespace App\Services;

use App\Actions\RabbitMQ\LogerrRabbit;
use App\Interfaces\QueueProviderInterface;
use App\Models\Error;
use App\Models\Log;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use PhpAmqpLib\Message\AMQPMessage;

class QueueProvider implements QueueProviderInterface
{

    private TeamProvider $teamProvider;

    public function __construct(TeamProvider $teamProvider)
    {
        $this->teamProvider = $teamProvider;
    }

    public function publish(string $message, string $channel): void
    {
        LogerrRabbit::publish($message, $channel);
    }

    public function receive(string $channel): void
    {
        LogerrRabbit::receive($channel, $this);
    }

    public function handle(AMQPMessage $msg, $channel): bool
    {
        $data = json_decode($msg->getBody(), true);

        $provider = new ($data['provider']);
        $user_id = $data['user'];

        $message = $data['data'];
        $user = User::find($user_id);
        $team = $this->teamProvider->getByID($data['team']);

        return $provider->add($message, $team, $user);
    }

}
