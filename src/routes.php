<?php 

Route::get('/', function()
{
    $which = 'Story';
    $id = CNP::getScapeId($which);
    $name = CNP::getScapeName($id);
    return "Howdy, World! The id of a " . $name . " is " . $id;
	return View::make('hello');
});


