<?php 
use \DemocracyApps\CNP\Entities as DAEntity;
use \DemocracyApps\CNP\Entities\Eloquent\User;


Route::get('account', array('as' => 'account', 'before' => 'cnp.auth', function()
{
    $user = DAEntity\Eloquent\User::find(\Auth::user()->getId());
    $person = DAEntity\Element::find($user->getElementId());
    $projects = DAEntity\Project::where('userid', '=', $user->getId())->get();
    return View::make('user.account', array('user' => $user, 'person' => $person, 
                      'projects' => $projects));
}));

Route::get('users/{userId}/edit', array('as' => 'system.users.edit', function($userId)
{
    $user = User::find($userId);
    return View::make('user.edit', array('user' => $user, 'putUrl'=>'account.update',
                                         'system' => false));
}));

Route::put('account/update/{userId}', array('as' => 'account.update', function($userId)
{
    $data = \Input::all();
    $user = User::find($userId);
    $user->name = $data['name'];
    $user->save();
    return \Redirect::to('/account');
}));

/********************************
 ** Login/Logout
 ********************************/
Route::get('/login', function() {
    return View::make('user.login');
});

Route::get('/logout', function() {
    Auth::logout();
    return Redirect::to('/');
});

Route::get('/loginfb', 'DemocracyApps\CNP\Controllers\LoginController@fbLogin');
Route::get('/logintw', 'DemocracyApps\CNP\Controllers\LoginController@twitLogin');
Route::get('/logincheat', 'DemocracyApps\CNP\Controllers\LoginController@cheatLogin');
