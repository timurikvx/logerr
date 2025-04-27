<?php

namespace App\Console\Commands;

use App\Actions\Generator;
use App\Models\User;
use Detection\Cache\Cache;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Redis;

class ErrorRender extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'error:render';

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
        $user = User::find(19);
        Generator::errors($user);
    }
}
