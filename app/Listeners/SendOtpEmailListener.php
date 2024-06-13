<?php

namespace App\Listeners;

use App\Events\SendOtpEmailEvent;
use App\Jobs\SendOtpEmailJob;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendOtpEmailListener implements ShouldQueue
{
    /**
     * Handle the event.
     *
     * @param  SendOtpEmailEvent  $event
     * @return void
     */
    public function handle(SendOtpEmailEvent $event)
    {
        SendOtpEmailJob::dispatch($event->user, $event->otp);
    }
}
