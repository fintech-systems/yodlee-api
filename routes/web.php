<?php

Route::get('/yodlee/callback', function() {
    ray('Incoming request to the Yodlee callback');
    ray(request());
});
