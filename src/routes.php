<?php 

Route::get('/', function()
{
    $which = 'People';
    $id = CNP::getScapeId($which);
    $name = CNP::getScapeName($id);
    $story = new DemocracyApps\CNP\Models\Denizen("First Settler", $id);
    $story->save();
    return "Howdy, World! The id of our first settler is ". $story->getID();
    return View::make('hello');

});

Route::get('/fb', 'DemocracyApps\CNP\Controllers\LoginController@fbLogin');
Route::get('/tw', 'DemocracyApps\CNP\Controllers\LoginController@twitLogin');


