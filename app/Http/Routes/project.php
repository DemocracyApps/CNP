<?php

Route::get('/', 'ProjectController@index');

Route::resource('compositions', 'CompositionsController');

/*************************************************
 *************************************************
 * Project access authorization
 *************************************************
 *************************************************/

Route::get('/authorize', 'ProjectController@authorize');
Route::post('/authorize', 'ProjectController@authorize');

Route::get('/authorized', function ($projectId) {
    if (\Auth::guest()) {
        return redirect()->guest('/login');
    }
    return redirect()->intended('/'.$projectId);
});