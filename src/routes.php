<?php

Route::group(['middleware' => 'web'], function () {
    Route::post('/laraform/process', '\Laraform\Controllers\FormController@process');

    Route::post('/laraform/trix-attachment', '\Laraform\Controllers\FormController@trixAttachment');
    Route::get('/laraform/csrf', '\Laraform\Controllers\FormController@csrf');

    Route::post('/laraform/validator/active_url', '\Laraform\Controllers\ValidatorController@activeUrl');
});