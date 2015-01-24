<?php 

Route::resource('projects', '\DemocracyApps\CNP\Controllers\ProjectsController');
Route::resource('composers', 'DemocracyApps\CNP\Controllers\ComposersController');
Route::resource('analysis', '\DemocracyApps\CNP\Controllers\AnalysisController');
Route::resource('notifications', 'DemocracyApps\CNP\Controllers\NotificationsController');
