<?php 

Route::get('/home', array(
    'before' => 'auth', 
    'uses'   => 'DemocracyApps\CNP\Controllers\LoginController@home'
));

Route::resource('relationtypes', 'DemocracyApps\CNP\Controllers\RelationTypesController');
Route::resource('stories', 'DemocracyApps\CNP\Controllers\StoriesController');

Route::get('/', function()
{

    $which = 'People';
    $id = CNP::getScapeId($which);
    $name = CNP::getScapeName($id);
    return 'Scape ' . $name . ' ID = ' . $id;

    $data = array();
    if (Auth::check()) {
        $data = Auth::user();
        return View::make('home', array('data'=>$data));
    }
    else {
        return Redirect::to('/login');
    }
});

Route::get('/login', function() {
    return View::make('login');
});

Route::get('/logout', function() {
    Auth::logout();
    return Redirect::to('/');
});

Route::get('/loginfb', 'DemocracyApps\CNP\Controllers\LoginController@fbLogin');
Route::get('/logintw', 'DemocracyApps\CNP\Controllers\LoginController@twitLogin');
//Route::get('login', 'DemocracyApps\CNP\Controllers\LoginController@login');


