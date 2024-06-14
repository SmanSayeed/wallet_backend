<?php

namespace App\Listeners;

use App\Events\PaymentSuccessEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class HandlePaymentSuccess
{
    public function handle(PaymentSuccessEvent $event)
    {
        // Handle payment success logic, e.g., send a confirmation email
    }
}
