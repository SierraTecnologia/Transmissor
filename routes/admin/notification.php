<?php
/**
 * Admin
 */

Route::resource('notifications', 'NotificationController', ['except' => ['show']/*, 'as' => 'admin'*/]);
Route::post('notifications/search', 'NotificationController@search')->name('notifications.search');
