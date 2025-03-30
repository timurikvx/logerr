<?php

namespace App\Listeners;

use App\Events\HandleErrorsEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

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
        dump('listener');
        //file_put_contents('D:\\handle_errors.txt', '11111');
    }
}
