<?php

namespace App\Providers;

use App\Events\DataProcessingRequested;
use App\Listeners\DataProcessingRequestedListener;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;
use App\Listeners\SendVerificationEmail;
use App\Events\UserRegistered;
use App\Listeners\SendOtpEmailListener;
use App\Events\SendOtpEmailEvent;

use App\Listeners\HandlePaymentSuccess;
use App\Events\PaymentSuccessEvent;

use App\Listeners\HandlePaymentFailure;
use App\Events\PaymentFailureEvent;

use App\Listeners\SendEmailListener;
use App\Events\SendEmailEvent;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            // SendEmailVerificationNotification::class,
            SendEmailVerificationNotification::class,
        ],
        UserRegistered::class => [
            SendVerificationEmail::class,
        ],
        SendOtpEmailEvent::class => [
            SendOtpEmailListener::class,
        ],
        DataProcessingRequested::class => [
            DataProcessingRequestedListener::class,
        ],
        PaymentSuccessEvent::class => [
            HandlePaymentSuccess::class,
        ],
        PaymentFailureEvent::class => [
            HandlePaymentFailure::class,
        ],
        SendEmailEvent::class => [
            SendEmailListener::class,
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        //
        parent::boot();
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
