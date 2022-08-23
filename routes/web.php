<?php

use Illuminate\Support\Facades\Route;

// TODO Where is this used?
Route::get('/yodlee/callback', function () {
    ray('Incoming request to the Yodlee callback');

    ray(request());
});
