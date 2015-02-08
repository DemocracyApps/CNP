<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', 'HomeController@index');

Route::get('home', 'HomeController@index');

Route::get('auth/login', 'Auth\AuthController@login');
Route::post('auth/login', 'Auth\AuthController@login');
Route::get('auth/loginfb', 'Auth\AuthController@loginfb');
Route::get('auth/logintw', 'Auth\AuthController@logintw');

Route::get('auth/register', 'Auth\AuthController@register');
Route::post('auth/register', 'Auth\AuthController@register');
Route::get('auth/logout', 'Auth\AuthController@logout');

Route::any('auth/thanks', 'Auth\AuthController@thanks');
Route::get('auth/confirm', 'Auth\AuthController@confirm');
Route::get('auth/confirm/{status}', 'Auth\AuthController@confirmResponse');
Route::get('user/profile', ['middleware' => 'cnp.auth', function () {
	$id = Auth::user()->id;
	$controller = app()->make('DemocracyApps\CNP\Http\Controllers\UserController');
	return $controller->callAction('show', $parameters=array('id'=>$id));
}]);

Route::get('user/email_changed', function() {
	return view('user.email_changed');
});
Route::post('user/email_changed', function() {
	return redirect()->intended('/');
});

// The {id} routes need to follow routes above
Route::get('user/{id}/edit', 'UserController@edit');
Route::get('user/{id}', 'UserController@show');
Route::put('user/{id}', 'UserController@update');


Route::resource('photo', 'PhotoController');

Route::controllers([
	'password' => 'Auth\PasswordController',
]);
