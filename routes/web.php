<?php
// /**
//  * Include App Routes
//  */
// $loadingRoutes = [
//     'notification',
//     'messages',
// ];

// Route::group(
//     ['middleware' => ['web']], function () use ($loadingRoutes) {
//         Route::group(['middleware' => 'auth'], function () use ($loadingRoutes) {

//             Route::prefix('transmissor')->group(
//                 function () use ($loadingRoutes) {
//                     Route::group(
//                         ['as' => 'transmissor.'], function () use ($loadingRoutes) {

//                             // /**
//                             //  * 
//                             //  */
//                             // Route::get('home', 'HomeController@index')->name('home');
//                             // Route::get('persons', 'HomeController@persons')->name('persons');

//                             // /**
//                             //  * Track
//                             //  */
//                             // Route::prefix('track')->group(
//                             //     function () {
//                             //         Route::namespace('Track')->group(
//                             //             function () {
//                             //                 Route::group(
//                             //                     ['as' => 'track.'], function () {

//                             //                         Route::get('person', 'PersonController@index')->name('person');

//                             //                     }
//                             //                 );
//                             //             }
//                             //         );
//                             //     }
//                             // );

//                         }
//                     );
//                 }
//             );

//             /*
//             |--------------------------------------------------------------------------
//             | User Routes
//             |--------------------------------------------------------------------------
//             */
//             foreach ($loadingRoutes as $loadingRoute) {
//                 if (file_exists(dirname(__FILE__) . DIRECTORY_SEPARATOR . "user". DIRECTORY_SEPARATOR . $loadingRoute.".php")) {
//                     include dirname(__FILE__) . DIRECTORY_SEPARATOR . "user". DIRECTORY_SEPARATOR . $loadingRoute.".php";
//                 }
//             }    


//             /*
//             |--------------------------------------------------------------------------
//             | Admin Routes
//             |--------------------------------------------------------------------------
//             */
//             foreach ($loadingRoutes as $loadingRoute) {
//                 if (file_exists(dirname(__FILE__) . DIRECTORY_SEPARATOR . "admin". DIRECTORY_SEPARATOR . $loadingRoute.".php")) {
//                     include dirname(__FILE__) . DIRECTORY_SEPARATOR . "admin". DIRECTORY_SEPARATOR . $loadingRoute.".php";
//                 }
//             }    


//         });
//     }
// );

