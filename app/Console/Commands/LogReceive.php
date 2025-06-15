<?php

namespace App\Console\Commands;

use App\Actions\RabbitMQ\LogerrRabbit;
use App\Interfaces\QueueProviderInterface;
use App\Models\Error;
use App\Models\Log;
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
    protected $description = 'Shell RabbitMQ log receiving';

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
        $log = new Log();
        $this->queueProvider->receive($log->prefix());
    }
}
