<?php

Route::group(
    ['middleware' => ['web']], function () {

        Route::prefix('transmissor')->group(
            function () {
                Route::group(
                    ['as' => 'transmissor.'], function () {

                        // /**
                        //  * 
                        //  */
                        // Route::get('home', 'HomeController@index')->name('home');
                        // Route::get('persons', 'HomeController@persons')->name('persons');

                        // /**
                        //  * Track
                        //  */
                        // Route::prefix('track')->group(
                        //     function () {
                        //         Route::namespace('Track')->group(
                        //             function () {
                        //                 Route::group(
                        //                     ['as' => 'track.'], function () {

                        //                         Route::get('person', 'PersonController@index')->name('person');

                        //                     }
                        //                 );
                        //             }
                        //         );
                        //     }
                        // );

                    }
                );
            }
        );





        Route::group(
            ['as' => 'profile.'], function () {

                Route::namespace('User')->group(
                    function () {
                        // Route::group(
                        //     ['middleware' => 'admin.user'], function () {

                                Route::get('/messages', 'MessagesController@index')->name('messages.index');
                                Route::get('/messages/to/{id}', 'MessagesController@create')->name('messages.create');
                                Route::post('/messages', 'MessagesController@store')->name('messages.store');
                                Route::get('/messages/{id}', 'MessagesController@show')->name('messages.show');
                                Route::put('/messages/{id}', 'MessagesController@update')->name('messages.update');

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
                            }
                        // );
                    // }
                );
            }
        );

    }
);