<?php 

Route::when('relationtypes*', 'cnp.auth');
Route::resource('relationtypes','DemocracyApps\CNP\Controllers\RelationTypesController');

Route::get('settings', array('as' => 'system.settings', function()
{
    return View::make('system.settings', array());
}));

Route::get('users', array('as' => 'system.users', function()
{
    return View::make('system.users', array());
}));