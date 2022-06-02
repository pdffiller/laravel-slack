<?php

use Pdffiller\LaravelSlack\VerifySlackToken;

use Illuminate\Support\Facades\Config;
/*
 * Slack app interaction
 */
Route::group([
    'middleware' => [VerifySlackToken::class],
], function () {
    Route::post("/api/v1/webhook/slack", 'Pdffiller\LaravelSlack\HandleRequestController@handle');
});
