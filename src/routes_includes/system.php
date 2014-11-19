<?php 

Route::when('relationtypes*', 'cnp.auth');
Route::resource('relationtypes','DemocracyApps\CNP\Controllers\RelationTypesController');
    
