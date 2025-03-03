<?php

namespace App\Console\Commands;

use App\Actions\RabbitMQ\LogerrRabbit;
use Illuminate\Console\Command;

class receive extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rabbit:read';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        LogerrRabbit::receive('errors');
    }
}
