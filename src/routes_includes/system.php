<?php 
use \DemocracyApps\CNP\Entities\Eloquent\User;


Route::when('relationtypes*', 'cnp.auth');
Route::resource('relationtypes','DemocracyApps\CNP\Controllers\RelationTypesController');

Route::get('settings', array('as' => 'system.settings', function()
{
    return View::make('system.settings', array());
}));

Route::get('users', array('as' => 'system.users', function()
{
    $users = User::orderBy('id')->get();
    return View::make('system.users', array('users' => $users));
}));

Route::get('users/{userId}/edit', array('as' => 'system.users.edit', function($userId)
{
    $user = User::find($userId);
    return View::make('user.edit', array('user' => $user, 'putUrl'=>'system.users.update'));
}));

Route::put('users/{userId}', array('as' => 'system.users.update', function($userId)
{
    $data = \Input::all();
    $user = User::find($userId);
    $user->name = $data['name'];
    $user->superuser = ($data['superuser']=='1')?true:false;
    $user->projectcreator = ($data['projectcreator']=='1')?true:false;
    $user->save();
    return \Redirect::to('/system/users');
}));

