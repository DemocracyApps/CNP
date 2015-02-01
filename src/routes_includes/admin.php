<?php 

Route::resource('projects', '\DemocracyApps\CNP\Controllers\ProjectsController');
Route::resource('composers', 'DemocracyApps\CNP\Controllers\ComposersController');
Route::resource('perspectives', '\DemocracyApps\CNP\Controllers\PerspectivesController');
Route::resource('notifications', 'DemocracyApps\CNP\Controllers\NotificationsController');
