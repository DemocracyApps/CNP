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
Log::info("Top of routes with URI " . \Request::server('REQUEST_URI') .
          " and method " .\Request::server('REQUEST_METHOD'));

Route::get('/', 'HomeController@index');

Route::get('home', 'HomeController@index');

Route::get('compositions/export', function() {
	return "Not implemented";
});
/*************************************************
 *************************************************
 * Sign-up & login pages
 *************************************************
 *************************************************/
require app_path().'/Http/Routes/auth.php';

/*************************************************
 *************************************************
 * Profile functions for users
 *************************************************
 *************************************************/
require app_path().'/Http/Routes/user.php';

/*************************************************
 *************************************************
 * Administrative pages for project admins
 *************************************************
 *************************************************/
Route::group(['prefix' => 'admin', 'middleware' => 'cnp.admin'], function ()
{
	require __DIR__.'/Routes/admin.php';
});

/*************************************************
 *************************************************
 * Public-facing project pages
 *************************************************
 *************************************************/

Route::get('/project404', array (function () {
	return View::make('projects.project404', array('project' => \Input::get('project')));
}));

Route::group(['prefix' => '{projectId}', 'middleware' => 'cnp.project'], function () {

	require __DIR__.'/Routes/project.php';

});


Route::resource('photo', 'PhotoController');

Route::controllers([
	'password' => 'Auth\PasswordController',
]);
