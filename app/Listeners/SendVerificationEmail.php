<?php

namespace App\Listeners;

use App\Events\UserRegistered;
use App\Jobs\SendVerificationEmailJob;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

use App\Notifications\VerifyEmailNotification;

class SendVerificationEmail implements ShouldQueue
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
    public function handle(UserRegistered $event): void
    {
        SendVerificationEmailJob::dispatch($event->user, $event->token);
        // $event->user->notify(new VerifyEmailNotification());
    }
}
