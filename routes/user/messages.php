<?php

    /*
    |--------------------------------------------------------------------------
    | User Routes
    |--------------------------------------------------------------------------
    */
    Route::group(['prefix' => 'messages'], function () {
        Route::get('/', 'MessagesController@index')->name('messages.index');
        Route::get('/to/{id?}', 'MessagesController@create')->name('messages.create');
        Route::post('/', 'MessagesController@store')->name('messages.store');
        Route::get('/{id}', 'MessagesController@show')->name('messages.show');
        Route::put('/{id}', 'MessagesController@update')->name('messages.update');
    });
