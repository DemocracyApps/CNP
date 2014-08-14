<?php 

Route::get('/home', array(
    'before' => 'auth', 
    'uses'   => 'DemocracyApps\CNP\Controllers\LoginController@home'
));

Route::get('/', function()
{
    $data = array();
    if (Auth::check()) {
        $data = Auth::user();
        return View::make('home', array('data'=>$data));
    }
    else {
        return Redirect::to('/login');
    }
/*
    $which = 'People';
    $id = CNP::getScapeId($which);
    $name = CNP::getScapeName($id);
    $story = new DemocracyApps\CNP\Models\Denizen("First Settler", $id);
    $story->save();
    return "Howdy, World! The id of our first settler is ". $story->getID();
    return View::make('hello');
*/
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

Route::resource('relationtypes', 'DemocracyApps\CNP\Controllers\RelationTypesController');

