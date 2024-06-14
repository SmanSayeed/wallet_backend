<?php

namespace App\Listeners;

use App\Events\PaymentFailureEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class HandlePaymentFailure
{
    public function handle(PaymentFailureEvent $event)
    {
        // Handle payment failure logic, e.g., notify the user
    }
}
