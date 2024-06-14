<?php

namespace App\Listeners;

use App\Events\SendEmailEvent;
use App\Mail\BaseMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class SendEmailListener implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     *
     * @param SendEmailEvent $event
     * @return void
     */
    public function handle(SendEmailEvent $event)
    {
        $user = $event->user;
        $subject = $event->subject;
        $message = $event->message;

        // Send email using BaseMail mailable
        Mail::to($user->email)->send(new BaseMail($subject, $message));
    }
}
