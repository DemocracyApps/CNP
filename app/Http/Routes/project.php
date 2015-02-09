<?php

Route::resource('compositions', 'DemocracyApps\CNP\Controllers\CompositionsController');

Route::get('/', 'ProjectController@index');

Route::get('/authorize', 'ProjectController@authorize');
Route::post('/authorize', 'ProjectController@authorize');

Route::get('/authorized', function ($projectId) {
    if (\Auth::guest()) {
        return redirect()->guest('/login');
    }
    return redirect()->intended('/'.$projectId);
});