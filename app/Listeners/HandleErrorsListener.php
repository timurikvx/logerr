<?php

namespace App\Listeners;

use App\Events\HandleErrorsEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class HandleErrorsListener implements ShouldQueue
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
    public function handle(HandleErrorsEvent $event): void
    {
        foreach ($event->list as $error){
            $hash = Str::of('error'.$error['name'].$error['team'].$error['date'].$error['guid'])->pipe('md5');
            if(Cache::has($hash)){
                continue;
            }
            Cache::set($hash, $error, 1800);
        }
    }
}
