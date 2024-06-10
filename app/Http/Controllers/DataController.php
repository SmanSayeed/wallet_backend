<?php

namespace App\Http\Controllers;

use App\Events\DataProcessingRequested;
use App\Jobs\ProcessData;
use Illuminate\Http\Request;

class DataController extends Controller
{
    public function processData(Request $request)
    {
        // Validate incoming request data if needed

        $data = $request->all(); // Get all data from the request

        // Dispatch the job to process the data asynchronously
        // ProcessData::dispatch($data);
        event(new DataProcessingRequested($data));

        return response()->json(['message' => 'Data received and queued for processing'], 200);
    }
}
