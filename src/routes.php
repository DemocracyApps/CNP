<?php 

Route::get('/home', array(
    'before' => 'auth', 
    'uses'   => 'DemocracyApps\CNP\Controllers\LoginController@home'
));

Route::resource('relationtypes', 'DemocracyApps\CNP\Controllers\RelationTypesController');
Route::resource('stories', 'DemocracyApps\CNP\Controllers\StoriesController');

Route::get('/map', 'DemocracyApps\CNP\Controllers\MapController@show');
Route::get('/map/test', 'DemocracyApps\CNP\Controllers\MapController@test');

Route::get('/', function()
{

    //$person = \DemocracyApps\CNP\Entities\Person::find(4);
    $person = \DemocracyApps\CNP\Entities\Person::all();
    dd($person);
    $which = 'People';
    $id = CNP::getDenizenTypeId($which);
    $name = CNP::getDenizenTypeName($id);
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


