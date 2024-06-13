<?php

namespace App\Events;

use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SendOtpEmailEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $user;
    public $otp;

    /**
     * Create a new event instance.
     *
     * @param User $user
     * @param int $otp
     */
    public function __construct(User $user, $otp)
    {
        $this->user = $user;
        $this->otp = $otp;
    }
}
