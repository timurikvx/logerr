<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\UserOption;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Auth;

class UserOptions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'options:set';

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
        Auth::login($user);;
        UserOption::set('asdadsad', ['312', '123']);
    }
}
