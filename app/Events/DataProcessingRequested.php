<?php

namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DataProcessingRequested
{
    use Dispatchable, SerializesModels;

    public $data;

    public function __construct($data)
    {
        $this->data = $data;
    }
}
