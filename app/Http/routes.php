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
//Log::info("Top of routes with URI " . \Request::server('REQUEST_URI') .
//          " and method " .\Request::server('REQUEST_METHOD'));

Route::pattern('projectId', '[0-9]+');

/*************************************************
 *************************************************
 * Ajax calls
 *************************************************
 *************************************************/
Route::get('ajax/{section}/{page}/{function}', ['uses' => 'AjaxController@main']);

/*************************************************
 *************************************************
 * Home and routing sandbox
 *************************************************
 *************************************************/
Route::get('/', 'HomeController@index');

Route::get('home', 'HomeController@index');

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
 * Administrative pages for platform admins
 *************************************************
 *************************************************/
Route::group(['prefix' => 'system', 'middleware' => 'cnp.system'], function ()
{
	require __DIR__.'/Routes/system.php';
});


/*************************************************
 *************************************************
 * Public-facing project pages
 *************************************************
 *************************************************/

Route::get('/project404', array (function () {
	return View::make('admin.project404', array('project' => \Input::get('project')));
}));

Route::group(['prefix' => '{projectId}', 'middleware' => 'cnp.project'], function ($projectId) {
	require __DIR__.'/Routes/project.php';

});


Route::controllers([
	'password' => 'Auth\PasswordController',
]);

require __DIR__.'/Routes/miscellaneous.php';