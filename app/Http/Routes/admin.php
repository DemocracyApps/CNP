<?php

Route::resource('projects', 'ProjectAdminController');
Route::resource('composers', 'ComposersController');
\Log::info("About to try the perspectives");
Route::resource('perspectives', 'PerspectiveAdminController');
Route::resource('notifications', 'NotificationsController');
