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

    }
);