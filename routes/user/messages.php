<?php


if (\Muleta\Modules\Features\Resources\FeatureHelper::hasActiveFeature(
    [
        'transmissor',
    ]
)){

    /*
    |--------------------------------------------------------------------------
    | Rotas do exemplo novo
    |--------------------------------------------------------------------------
    */
    Route::group(['prefix' => 'messages'], function () {
        Route::get('/', ['as' => 'messages.index', 'uses' => 'MessagesController@index']);
        Route::get('create', ['as' => 'messages.create', 'uses' => 'MessagesController@create']);
        Route::post('/', ['as' => 'messages.store', 'uses' => 'MessagesController@store']);
        Route::get('{id}', ['as' => 'messages.show', 'uses' => 'MessagesController@show']);
        Route::put('{id}', ['as' => 'messages.update', 'uses' => 'MessagesController@update']);
    });
    // /*
    // |--------------------------------------------------------------------------
    // | Rotas do exemplo antigo
    // |--------------------------------------------------------------------------
    // */
    // Route::group(['prefix' => 'messages'], function () {
    //     Route::get('/', 'MessagesController@index')->name('messages.index');
    //     Route::get('/to/{id?}', 'MessagesController@create')->name('messages.create');
    //     Route::post('/', 'MessagesController@store')->name('messages.store');
    //     Route::get('/{id}', 'MessagesController@show')->name('messages.show');
    //     Route::put('/{id}', 'MessagesController@update')->name('messages.update');
    // });
}