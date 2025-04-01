<?php

namespace App\Console\Commands;

use App\Actions\RabbitMQ\LogerrRabbit;
use Illuminate\Console\Command;

class LogReceive extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rabbit:logs';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Shell rabbit MQ error receiving';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        LogerrRabbit::receive('logs');
    }
}
