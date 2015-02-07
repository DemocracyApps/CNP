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


Route::controllers([
	'password' => 'Auth\PasswordController',
]);
