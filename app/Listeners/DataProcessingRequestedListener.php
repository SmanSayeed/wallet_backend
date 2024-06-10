<?php

namespace App\Listeners;

use App\Events\DataProcessingRequested;
use App\Jobs\ProcessData;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class DataProcessingRequestedListener implements ShouldQueue
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
    public function handle(DataProcessingRequested $event): void
    {
         // Get the data from the event
         $data = $event->data;

         // Create an instance of the job and call its handle method
         ProcessData::dispatch($data);
    }
}
