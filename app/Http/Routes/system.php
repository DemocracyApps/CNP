<?php

Route::get('settings', 'System\SystemController@settings');
Route::get('users', 'System\SystemController@users');
Route::get('users/{id}/edit', 'System\SystemController@userEdit');
Route::put('users/{id}', 'System\SystemController@userEdit');

Route::get('projects', 'System\SystemController@projects');

Route::resource('elementtypes','System\ElementTypesController');
Route::resource('relationtypes','System\RelationTypesController');
