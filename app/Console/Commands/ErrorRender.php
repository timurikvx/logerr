<?php

namespace App\Console\Commands;

use App\Actions\Generator;
use App\Models\User;
use Illuminate\Console\Command;

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
        $user = User::find(1);
        Generator::render($user);
    }
}
