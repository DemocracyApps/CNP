<?php 

use \DemocracyApps\CNP\Entities as DAEntity;

/********************************
 ********************************
 *
 * Default website routes
 * 
 ********************************
 *********************************/

Route::get('/test', function()
{
    return View::make('test');
});

Log::info("Top of routes with URI " . \Request::server('REQUEST_URI') .
          " and method " .\Request::server('REQUEST_METHOD'));

Route::get('/', function()
{
    return Redirect::to('/stories');
});

Route::resource('stories', 'DemocracyApps\CNP\Controllers\StoriesController');
Route::resource('scapes', 'DemocracyApps\CNP\Controllers\ScapesController');
Route::resource('collectors', 'DemocracyApps\CNP\Controllers\CollectorsController');

Route::get('account', array('before' => 'cnp.auth', function()
{
    $user = DAEntity\Eloquent\User::find(\Auth::user()->getId());
    $person = DAEntity\Person::find($user->getDenizenId());
    $scapes = DAEntity\Scape::allUserDenizens($user->getId());

    return View::make('account', array('user' => $user, 'person' => $person, 
                      'scapes' => $scapes));
}));

Route::when('relationtypes*', 'cnp.auth');
Route::resource('relationtypes','DemocracyApps\CNP\Controllers\RelationTypesController');

Route::get('/map', 'DemocracyApps\CNP\Controllers\MapController@show');
Route::get('/map/test', 'DemocracyApps\CNP\Controllers\MapController@test');

/********************************
 ** Login/Logout
 ********************************/
Route::get('/login', function() {
    return View::make('login');
});

Route::get('/logout', function() {
    Auth::logout();
    return Redirect::to('/');
});

Route::get('/loginfb', 'DemocracyApps\CNP\Controllers\LoginController@fbLogin');
Route::get('/logintw', 'DemocracyApps\CNP\Controllers\LoginController@twitLogin');
Route::get('/logincheat', 'DemocracyApps\CNP\Controllers\LoginController@cheatLogin');


/********************************
 ********************************
 *
 * API routes
 * 
 ********************************
 *********************************/

Route::when('api/v1/*','force.ssl'); // Forces SSL if cnp.json has apiRequiresSsl=true

Route::when('api/v1/*', 'api.key'); // Logs user in based on API key - see User.php

Route::group(['prefix' => 'api/v1'], function () 
    {
        Route::resource('scapes', 'DemocracyApps\CNP\Controllers\ScapesController');
    }
);


