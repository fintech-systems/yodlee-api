<?php

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;

// https://developer.yodlee.com/docs/api/1.1/DataExtracts#Event_Payload_Information
Route::post('/yodlee/event/subscription', function () {
    $message = 'Incoming request to the Yodlee event subscription URL';

    Log::debug($message);

    Log::debug(request());

    ray($message);

    ray(request());

    return response()->json(['success' => 'success'], 200);
});
