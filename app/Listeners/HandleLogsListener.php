<?php

namespace App\Listeners;

use App\Events\HandleLogsEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class HandleLogsListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(HandleLogsEvent $event): void
    {
        foreach ($event->list as $error){
            $hash = Str::of('log'.$error['name'].$error['team'].$error['date'].$error['guid'])->pipe('md5');
            if(Cache::has($hash)){
                continue;
            }
            Cache::set($hash, $error, 1800);
        }
    }
}
