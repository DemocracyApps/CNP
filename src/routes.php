<?php 

/*
Route::get('/home', array(
    'before' => 'auth',
    function() {
        return View::make('home');
      }
));
*/

Route::get('/home', array(
    'before' => 'auth', 
    'uses'   => 'DemocracyApps\CNP\Controllers\LoginController@home'
));

Route::get('/login', function() {
    return View::make('login');
});

Route::get('/loginfb', 'DemocracyApps\CNP\Controllers\LoginController@fbLogin');
Route::get('/logintw', 'DemocracyApps\CNP\Controllers\LoginController@twitLogin');
//Route::get('login', 'DemocracyApps\CNP\Controllers\LoginController@login');


Route::get('/', function()
{
    $data = array();
    if (Auth::check()) {
        $data = Auth::user();
    }
    return View::make('home', array('data'=>$data));
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

Route::resource('relationtypes', 'DemocracyApps\CNP\Controllers\RelationTypesController');

