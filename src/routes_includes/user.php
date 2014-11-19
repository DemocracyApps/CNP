<?php 
use \DemocracyApps\CNP\Entities as DAEntity;


Route::get('account', array('as' => 'account', 'before' => 'cnp.auth', function()
{
    $user = DAEntity\Eloquent\User::find(\Auth::user()->getId());
    $person = DAEntity\Person::find($user->getElementId());
    $projects = DAEntity\Project::where('userid', '=', $user->getId())->get();
    return View::make('account', array('user' => $user, 'person' => $person, 
                      'projects' => $projects));
}));

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
