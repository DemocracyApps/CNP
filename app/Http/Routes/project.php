<?php

use DemocracyApps\CNP\Analysis\Perspective;
use DemocracyApps\CNP\Project\Project;
use DemocracyApps\CNP\Project\ProjectUser;

Route::get('/', 'ProjectController@index');

Route::resource('compositions', 'CompositionsController');
Route::get('perspectives', function ($projectId) {
    $project = Project::find($projectId);
    $owner = (ProjectUser::projectAdminAccess($project->id, \Auth::id()));

    $perspectives = Perspective::whereColumn('project', '=', $projectId);

    return view('project.perspectives', array('project' => $project, 'perspectives' => $perspectives, 'owner' => $owner));
});
/*************************************************
 *************************************************
 * Project access authorization
 *************************************************
 *************************************************/

Route::get('/authorize', 'ProjectController@authorize');
Route::post('/authorize', 'ProjectController@authorize');

Route::get('/authorized', function ($projectId) {
    if (\Auth::guest()) {
        return redirect()->guest('/login');
    }
    return redirect()->intended('/'.$projectId);
});