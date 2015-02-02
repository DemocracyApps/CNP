<?php

use \DemocracyApps\CNP\Entities\Project;
use \DemocracyApps\CNP\Entities\ProjectUser;
use \DemocracyApps\CNP\Compositions\Composer;
use \DemocracyApps\CNP\Compositions\Composition;
use \DemocracyApps\CNP\Entities\Element;
use \DemocracyApps\CNP\Analysis\Perspective;

/*
 * Route::group(['prefix' => '{projectId}', 'before' => 'cnp.ext'], ...
 */

//
//Route::get('authorize',
//    array('uses' => 'DemocracyApps\CNP\Controllers\ProjectsController@authorize', array('projectId' => $projectId)));

Route::get('authorized', function ($projectId) {
    if (\Auth::guest()) {
        return Redirect::guest('/login');
    }
    return Redirect::intended('/'.$projectId);
});


Route::get('authorize', function ($projectId) {
    if (\Auth::guest()) {
        return Redirect::guest('/login');
    }
    //$intended = \Session::pull('url.intended', '/abc');
    $app = app();
    $controller = $app->make('DemocracyApps\CNP\Controllers\ProjectsController');
    return $controller->callAction('authorize', $parameters = array('id'=> $projectId));
});

Route::post('authorize', function ($projectId) {
    if (\Auth::guest()) {
        return Redirect::guest('/login');
    }
    $app = app();
    $controller = $app->make('DemocracyApps\CNP\Controllers\ProjectsController');
    return $controller->callAction('authorize', $parameters = array('id'=> $projectId));
});

Route::get('/', function($projectId) {
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

    $owner = false;
    if (!\Auth::guest()) {
        $owner = (ProjectUser::projectAdminAccess($project->id, \Auth::id()));
    }
    $composerId = ($project->hasProperty('defaultInputComposer'))?$project->getProperty('defaultInputComposer'):-1;
    return View::make('world.home', array('project' => $project, 'owner' => $owner, 'defaultInputComposer' => $composerId));
});

Route::get('compositions', function ($projectId) {
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

    if (! $project->isViewAuthorized(\Auth::id())) {
        return \Redirect::guest('/'.$projectId.'/authorize');
    }
    $sort = 'date';
    $desc  = true;
    if (\Input::has('sort')) {
        $sort = \Input::get('sort');
    }
    if (\Input::has('desc')) {
        $val = \Input::get('desc');
        if ($val == 'false') $desc=false;
    }
    $filter = false;
    $target = null;
    if (\Input::has('filter')) {
        $filter = true;
    }

    $owner = false;
    if (!\Auth::guest()) {
        $owner = (ProjectUser::projectAdminAccess($project->id, \Auth::id()));
        //$owner = ($project->userid == \Auth::user()->getId());
    }
    $page = \Input::get('page', 1);
    $pageLimit=\CNP::getConfigurationValue('pageLimit');

    // Need to:
    // 2. Change output to say it is filtered.
    // 3. Add a "back" button to show that takes you back to current view.

    $filterDescription = null;
    $advancedView = false;
    $advancedViewMessage = null;
    $structureView = false;
    if (\Input::has('structureView')) {
        if (\Input::get('structureView') == 1) $structureView = true;
    }

    if ($filter) {
        $filterType = \Input::get('filter');
        if ($filterType == 'related') {
            $target = \Input::get('element');
            $element = Element::find($target);
            // We want all compositions that refer to this target element
            $data = Composition::projectCompositionsByReferentPaged($target, $sort, $desc, $project->id, $page, $pageLimit);
            $filterDescription = "Contributions related to " . $element->getContent();

            if (\Input::has('advancedView')) {
                if (\Input::get('advancedView') == 1) {
                    $advancedView = true;
                    $href = "/$project->id/compositions?filter=related&element=$element->id&structureView=1";
                    $advancedViewMessage = "This is a list of stories and contributions that refer to " . $element->getContent() . ".";
                    $advancedViewMessage .= " Click <a href='" . $href . "'>here</a> to see the internal structure of the references.";

                }
            }
        }
    }
    else {
        $data = Composition::allProjectCompositionsPaged($sort, $desc, $project->id, $page, $pageLimit);
    }

    $stories = \Paginator::make($data['items'], $data['total'], $pageLimit);

    if ($structureView) {
        return "Structure view not implemented";
    }
    else {
        return \View::make('world.index', array('stories' => $stories, 'project' => $project,
            'owner' => $owner, 'sort' => $sort, 'desc' => $desc,
            'filterDescription' => $filterDescription,
            'advancedView' => $advancedView,
            'advancedViewMessage' => $advancedViewMessage));
    }
});

Route::get('compositions/create', function ($projectId) {
    if (\Auth::guest()) {
        return \Redirect::guest('/login');
    }

    $project = Project::find($projectId);
    $access = $project->postAuthorization(\Auth::id());
    if (! $access->allowed) {
        if ($access->reason == 'authorization') {
            return \Redirect::guest('/'.$projectId.'/authorize');
        }
        else {
            return \Redirect::guest('user/noconfirm');
        }
    }

    if ( ! \Input::has('composer')) throw new \Exception("No composer id specified.");

    $composer = Composer::find(\Input::get('composer'));
    if ( ! $composer ) throw new \Exception("Composer ".\Input::get('composer'). " not found.");

    if ( ! $composer->validForInput()) throw new \Exception("Composer ".\Input::get('composer') . " not valid for input.");
    $composer->initializeForInput(\Input::all());

    if (\Input::get('referent')) {
        $composer->setReferentByElementId(\Input::get('referent'), \Input::get('referentRelation'));
    }

    $composition = new \DemocracyApps\CNP\Compositions\Composition;
    $composition->title = "No Title";
    $composition->input_composer_id = $composer->id;
    $composition->userid = \Auth::user()->getId();
    $composition->project = $composer->project;
    $composition->save();

    $inputType = $composer->getInputType();
    if ($inputType == 'csv-simple') {
        return \View::make('world.csvUpload', array('composer' => $composer, 'composition' => $composition));
    }
    elseif ($inputType == 'auto-interactive') {
        return \View::make('world.autoinput', array('composer' => $composer, 'composition' => $composition));
    }
    else {
        return "Unknown input type " . $inputType;
    }

});

/* Store */
Route::post('compositions', function($projectId) {
    if (\Auth::guest()) {
        return \Redirect::guest('/login');
    }
    $project = Project::find($projectId);
    $access = $project->postAuthorization(\Auth::id());
    if (! $access->allowed) {
        if ($access->reason == 'authorization') {
            return \Redirect::guest('/'.$projectId.'/authorize');
        }
        else {
            return \Redirect::guest('user/noconfirm');
        }
    }

    $input = \Input::all();
    $composition = Composition::find(\Input::get('composition'));
    $composer = Composer::find($composition->input_composer_id);
    if ( ! $composer->validateInput($input)) {
        return \Redirect::back()->withInput()->withErrors($composer->messages());
    }
    if (\Input::get('referentId')) {
        $composer->setReferentByReferentId(\Input::get('referentId'), \Input::get('referentRelation'));
    }
    $inputType = $composer->getInputType();

    $composer->initializeForInput($input);
    $composer->processInput($input, $composition);
    if ($inputType == 'auto-interactive') {
        if ( ! $composer->getDriver()->done()) {
            return \View::make('world.autoinput', array('composer' => $composer, 'composition' => $composition));
        }
    }
    return \Redirect::to('/'.$composer->project.'/compositions');
});


Route::get('compositions/{compositionId}', function ($projectId, $compositionId) {
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

    $owner = false;
    if (!\Auth::guest()) {
        $owner = (ProjectUser::projectAdminAccess($project->id, \Auth::id()));
//        $owner = ($project->userid == \Auth::user()->getId());
    }
    $composition = Composition::find($compositionId);

    $defaultComposer = null;
    if ($project->hasProperty('defaultOutputComposer')) {
        $defaultComposer = Composer::find($project->getProperty('defaultOutputComposer'));
    }
    /*
     * In order of preference:
     *    1. Preferred output composer defined by input composer
     *    2. Project-level default output composer
     *    3. Original input composer
     */
    $composer = Composer::find($composition->input_composer_id); // the default if nothing else found
    if ($composer->output) { // Top preference if defined
        $ctmp = Composer::find($composer->output);
        if ($ctmp) $composer = $ctmp;
    }
    else if ($defaultComposer) { // Second preference if defined
        $composer = $defaultComposer;
    }

    $topElement = Element::find($composition->top);

    // Get all the elements associated with this composition.
    // We get back a hash by Composer element ID.
    $elements = array();
    Element::getCompositionElements($composition->id, $elements);

    $composer->initializeForOutput(\Input::all(), $elements);
    if ( ! $composer->getDriver()->done()) {
        if (! $composer->getDriver()->usingInputForOutput()) {
            return \View::make('world.layoutdriven', array('composer' => $composer,
                                                                  'topElement' => $topElement,
                                                                  'composition' => $composition,
                                                                  'project' => $project->id));
        }
        else {
            return \View::make('world.show', array('composer' => $composer,
                                                                  'topElement' => $topElement,
                                                                  'composition' => $composition,
                                                                  'project' => $project->id));
        }
    }
    else {
      return \Redirect::to('/'.$composer->project.'/compositions');
    }

});

Route::get('sos_start', function ($projectId) {
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

    return \View::make('world.sos_start', array('project' => $project, 'owner' => $owner));
});

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

Route::get('stories/{another}', array('as' => 'ext.stories',
                                              'uses' => 'DemocracyApps\CNP\Controllers\CompositionsController@test'));

