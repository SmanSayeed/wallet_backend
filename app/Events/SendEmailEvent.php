<?php

namespace App\Events;

use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SendEmailEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $user;
    public $subject;
    public $message;

    /**
     * Create a new event instance.
     *
     * @param User $user
     * @param string $subject
     * @param string $message
     */
    public function __construct(User $user, string $subject, string $message)
    {
        $this->user = $user;
        $this->subject = $subject;
        $this->message = $message;
    }
}
