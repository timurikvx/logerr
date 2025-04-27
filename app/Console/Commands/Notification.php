<?php

namespace App\Console\Commands;

use App\Models\Crew;
use App\Models\NotificationMessage;
use App\Models\NotificationsOption;
use Illuminate\Console\Command;

class Notification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notification:handle';

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
        while (true){
            $teams = Crew::all();
            foreach ($teams as $team){
                NotificationsOption::notification($team->id);
            }
            NotificationMessage::handle();
            sleep(60);
        }

    }
}
