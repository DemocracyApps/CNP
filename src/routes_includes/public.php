<?php 

use \DemocracyApps\CNP\Entities\Project;

/*
 * Route::group(['prefix' => '{projectId}', 'before' => 'cnp.ext'], ...
 */

Route::get('/', function($projectId) {
    $project = Project::find($projectId);
    $owner = ($project->userid == \Auth::user()->getId());
    $composerId = ($project->hasProperty('defaultInputComposer'))?$project->getProperty('defaultInputComposer'):-1;
    return View::make('world.home', array('project' => $project, 'owner' => $owner, 'defaultInputComposer' => $composerId));
});

Route::get('compositions', function ($projectId) {
    $project = Project::find($projectId);
    $owner = ($project->userid == \Auth::user()->getId());
    $page = \Input::get('page', 1);
    $pageLimit=\CNP::getConfigurationValue('pageLimit');
    $data = \DemocracyApps\CNP\Compositions\Composition::allProjectCompositionsPaged($project->id, $page, $pageLimit);
    $stories = \Paginator::make($data['items'], $data['total'], $pageLimit);
    return \View::make('world.index', array('stories' => $stories, 'project' => $project, 'owner' => $owner));
});

Route::get('compositions/create', function ($projectId) {
    if (\Auth::guest()) {
        return \Redirect::to('/login');         
    }

    $project = Project::find($projectId); 

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
        return \Redirect::to('/login');         
    }
    $project = Project::find($projectId);
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

Route::get('compositions/{compositionId}', function ($projectId) {
    $project = Project::find($projectId);
    $owner = ($project->userid == \Auth::user()->getId());
    $composition = Composition::find($compositionId);
//        $composition = Composition::find(\Request::segment(3));
    $viewMode='normal';
    if (\Input::has('view')) {
        $viewMode=\Input::get('view');
    }
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

    if ($viewMode == 'normal') {
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
          return \Redirect::to('/'.$composer->project.'/compositions?project='.$composer->project);
        }
    }
    else if ($viewMode == 'structure') {
        if (\Input::has('element')) {
            $topElement = Element::find(\Input::get('element'));
        }
        $elementRelations = array(); 
        $elementsById = array();
        // $elements = Relation::getRelatedElements($topElement->id, null);
        $elements = Element::getRelatedElements($topElement->id, null);
        array_unshift($elements, $topElement);

        foreach ($elements as $element) { // Get the relations
            if ( ! array_key_exists($element->id, $elementsById)) $elementsById[$element->id] = $element;
            $relations = Relation::getRelations($element->id);

            $elementRelations[$element->id] = array();
            foreach ($relations as $relation) {
                $to = $relation->toId;
                $relType = \DemocracyApps\CNP\Entities\Eloquent\RelationType::find($relation->relationId);
                $relationName = $relType->name;
                if ( ! array_key_exists($to, $elementsById)) {
                    $elementsById[$to] = Element::find($to);
                }
                //<a href="/{{$project}}/compositions/{{$composition->id}}?view=structure&element={{$element->id}}">{{$element->id}}</a>
                $link = '<a href="/' . $project->id . "/compositions" . "/" . $composition->id . "?view=structure&element=" . 
                        $elementsById[$to]->id . '">' .  $elementsById[$to]->id . "</a>";
                $elementRelations[$element->id][] = array($relationName, 
                                                          $elementsById[$to]->name . " (".$link .")");
            }
        }
        return \View::make('world.show_structure', array('story' => $topElement, 
                                                 'elements' => $elements,
                                                 'elementsById' => $elementsById,
                                                 'relations' => $elementRelations,
                                                 'composition' => $composition,
                                                 'project' => $project->id));
    }

});

Route::get('sos_start', function ($projectId) {
    $project = Project::find($projectId);
    $owner = ($project->userid == \Auth::user()->getId());
    return \View::make('world.sos_start', array('project' => $project, 'owner' => $owner));
});

Route::get('stories/{another}', array('as' => 'ext.stories',
                                              'uses' => 'DemocracyApps\CNP\Controllers\CompositionsController@test'));

