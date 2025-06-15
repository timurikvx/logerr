<?php

namespace App\Console\Commands;

use App\Actions\RabbitMQ\LogerrRabbit;
use App\Interfaces\QueueProviderInterface;
use App\Models\Error;
use App\Models\Log;
use Illuminate\Console\Command;

class ErrorReceive extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rabbit:errors';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Shell RabbitMQ error receiving';
    private QueueProviderInterface $queueProvider;


    public function __construct(QueueProviderInterface $queueProvider)
    {
        parent::__construct();
        $this->queueProvider = $queueProvider;
    }
    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $error = new Error();
        $this->queueProvider->receive($error->prefix());
    }
}
