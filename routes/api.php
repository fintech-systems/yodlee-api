<?php

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;

// https://developer.yodlee.com/docs/api/1.1/DataExtracts#Event_Payload_Information
Route::post('/api/v1/event', function () {
    $message = 'Incoming request to the Yodlee event subscription URL';

    Log::debug($message);

    ray($message);

    ray(request());

    return response()->json(['success' => 'success'], 200);
});
