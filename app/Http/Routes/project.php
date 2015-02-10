<?php

Route::get('/', 'ProjectController@index');

Route::resource('compositions', 'CompositionsController');
Route::get('perspectives', function ($projectId) {
    $project = Project::find($projectId);
    $access = $project->viewAuthorization(\Auth::id());
    if (! $access->allowed) {
        if ($access->reason == 'authorization') {
            return \Redirect::guest('/'.$projectId.'/authorize');
        }
        else {
            return \Redirect::guest('user/noconfirm');
        }
    }
    $owner = (ProjectUser::projectAdminAccess($project->id, \Auth::id()));

    $perspectives = Perspective::whereColumn('project', '=', $projectId);

    return \View::make('world.perspectives', array('project' => $project, 'perspectives' => $perspectives, 'owner' => $owner));
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