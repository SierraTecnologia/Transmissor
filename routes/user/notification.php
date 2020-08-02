<?php

    /*
    |--------------------------------------------------------------------------
    | User Routes
    |--------------------------------------------------------------------------
    */
    Route::group(['prefix' => 'notifications'], function () {
        Route::get('/', 'NotificationController@index');
        Route::get('{uuid}/read', 'NotificationController@read');
        Route::delete('{uuid}/delete', 'NotificationController@delete');
        Route::get('search', 'NotificationController@search');                                    
        
        /**
        * Veio separdo
        */

        Route::get('/unread', 'NotificationsController@unread')->name('notifications.unread');
        // Route::get('/', 'NotificationsController@index')->name('notifications.index');
        Route::get('/count', 'NotificationsController@count')->name('notifications.count');

    });