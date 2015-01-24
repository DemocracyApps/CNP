<?php 

use \DemocracyApps\CNP\Entities as DAEntity;
use \DemocracyApps\CNP\Entities\Project;
use \DemocracyApps\CNP\Entities\Element;
use \DemocracyApps\CNP\Entities\Relation;
use \DemocracyApps\CNP\Compositions\Composition;
use \DemocracyApps\CNP\Compositions\Composer;
use GrahamCampbell\Flysystem\Facades\Flysystem;

//Log::info("Top of routes with URI " . \Request::server('REQUEST_URI') .
//          " and method " .\Request::server('REQUEST_METHOD'));

//$pu = new \DemocracyApps\CNP\Entities\ProjectUser();
//$pu->project = 2;
//$pu->user = 2;
//$pu->access = 3;
//$pu->save();

/*
 * Patterns for use in routes
 */
Route::pattern('projectId', '[0-9]+');
Route::when('*', 'csrf', array('post', 'put', 'delete'));

Route::get('/', function()
{
    $projects = Project::all();
    return View::make('home', array('projects' => $projects));
    //return Redirect::route('account', \Input::all());
});

/*************************************************
 *************************************************
 * Current routes work area 
 *************************************************
 *************************************************/

Route::get('/rite', array(function()
{
    Flysystem::put('hi.txt', 'foo');
    return ("Writ");
}));

Route::get('/reed', array(function()
{
    return Flysystem::read('hi.txt');
}));


// /compositions/export must be defined before Route::resource('compositions'). Probably need
// to come up with a different route.
Route::get('/compositions/export', array('as' => 'compositions.export', function() 
    {
        return View::make('compositions.export', array('project' => \Input::get('project')));
    }));

Route::get('/compositions/explore', 
            array('as' => 'compositions.explore', 
                  'uses' => 'DemocracyApps\CNP\Controllers\CompositionsController@explore'));


Route::resource('compositions', 'DemocracyApps\CNP\Controllers\CompositionsController');

/*************************************************
 *************************************************
 * Public-facing pages 
 *************************************************
 *************************************************/

Route::get('/project404', array (function () {
    return View::make('projects.project404', array('project' => \Input::get('project')));
}));

Route::group(['prefix' => '{projectId}', 'before' => 'cnp.project'], function () {

    require __DIR__.'/routes_includes/public.php';

});

/*************************************************
 *************************************************
 * Administrative pages for project admins 
 *************************************************
 *************************************************/

Route::group(['prefix' => 'admin', 'before' => 'cnp.admin'], function () 
{
    require __DIR__.'/routes_includes/admin.php';
});

/*************************************************
 *************************************************
 * System-wide administrative pages (superuser) 
 *************************************************
 *************************************************/

Route::group(['prefix' => 'system', 'before' => 'cnp.system'], function () 
{
    require __DIR__.'/routes_includes/system.php';
});

/*************************************************
 *************************************************
 * User routes (login/out, account profile, etc.)
 *************************************************
 *************************************************/

require __DIR__.'/routes_includes/user.php';

/*************************************************
 *************************************************
 * Ajax routes 
 *************************************************
 *************************************************/

require __DIR__.'/routes_includes/ajax.php';

/*************************************************
 *************************************************
 * Miscellaneous routes 
 *************************************************
 *************************************************/

require __DIR__.'/routes_includes/misc.php';

/*************************************************
 *************************************************
 * API routes 
 *************************************************
 *************************************************/

// Deal with moving these into a separate file later.

Route::when('api/v1/*','force.ssl'); // Forces SSL if cnp.json has apiRequiresSsl=true

Route::when('api/v1/*', 'api.key'); // Logs user in based on API key - see User.php

Route::group(['prefix' => 'api/v1'], function () 
    {
        Route::resource('projects', 'DemocracyApps\CNP\Controllers\ProjectsController');
    }
);


