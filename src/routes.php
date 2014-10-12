<?php 

use \DemocracyApps\CNP\Entities as DAEntity;
use \DemocracyApps\CNP\Entities\Project;
use \DemocracyApps\CNP\Entities\Element;
use \DemocracyApps\CNP\Entities\Relation;
use \DemocracyApps\CNP\Compositions\Composition;
use \DemocracyApps\CNP\Compositions\Composer;

// Log::info("Top of routes with URI " . \Request::server('REQUEST_URI') .
//           " and method " .\Request::server('REQUEST_METHOD'));
$environment = App::environment();

/********************************
 ********************************
 *
 * Default website routes
 * 
 ********************************
 *********************************/


Route::get('/test', function()
{
    $c = new \DemocracyApps\CNP\Entities\Eloquent\Collection;
    $c->set = array();
    $c->set[] = "1-100";
    $c->project = 1;
    $c->save();
});

Route::get('/demo', function()
{
    $stage = Input::get('stage');
    return View::make('demo.'.$stage, array());
});

// /compositions/export must be defined before Route::resource('compositions'). Probably need
// to come up with a different route.
Route::get('/compositions/export', array('as' => 'compositions.export', function() 
    {
        return View::make('compositions.export', array('project' => \Input::get('project')));
    }));

Route::get('/compositions/explore', 
            array('as' => 'compositions.explore', 
                  'uses' => 'DemocracyApps\CNP\Controllers\CompositionsController@explore'));


Route::resource('notifications', 'DemocracyApps\CNP\Controllers\NotificationsController');
Route::resource('collections', 'DemocracyApps\CNP\Controllers\CollectionsController');
Route::resource('compositions', 'DemocracyApps\CNP\Controllers\CompositionsController');
Route::resource('projects', '\DemocracyApps\CNP\Controllers\ProjectsController');
Route::resource('composers', 'DemocracyApps\CNP\Controllers\ComposersController');

/*
 * Right now the route pattern and the cnp.ext filter are checking for the same thing.
 */
Route::pattern('projectId', '[0-9]+');

Route::group(['prefix' => '{projectId}', 'before' => 'cnp.ext'], function () {

    Route::get('/', function() {
        $project = Project::find(\Request::segment(1));
        $owner = ($project->userid == \Auth::user()->getId());
        $composerId = ($project->hasProperty('defaultInputComposer'))?$project->getProperty('defaultInputComposer'):-1;
        return View::make('world.home', array('project' => $project, 'owner' => $owner, 'defaultInputComposer' => $composerId));
    });
    Route::get('compositions', function () {
        $project = Project::find(\Request::segment(1));
        $owner = ($project->userid == \Auth::user()->getId());
        $page = \Input::get('page', 1);
        $pageLimit=\CNP::getConfigurationValue('pageLimit');
        $data = \DemocracyApps\CNP\Compositions\Composition::allProjectCompositionsPaged($project->id, $page, $pageLimit);
        $stories = \Paginator::make($data['items'], $data['total'], $pageLimit);
        return \View::make('world.index', array('stories' => $stories, 'project' => $project, 'owner' => $owner));
    });

    Route::get('compositions/create', function () {
        if (\Auth::guest()) {
            return \Redirect::to('/login');         
        }

        $project = Project::find(\Request::segment(1));

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
    Route::post('compositions', function() {
        if (\Auth::guest()) {
            return \Redirect::to('/login');         
        }
        $project = Project::find(\Request::segment(1));
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

    Route::get('compositions/{compositionId}', function () {
        $project = Project::find(\Request::segment(1));
        $owner = ($project->userid == \Auth::user()->getId());
        $composition = Composition::find(\Request::segment(3));
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

    Route::get('sos_start', function () {
        $project = Project::find(\Request::segment(1));
        $owner = ($project->userid == \Auth::user()->getId());
        return \View::make('world.sos_start', array('project' => $project, 'owner' => $owner));
    });
    Route::get('stories/{another}', array('as' => 'ext.stories',
                                                  'uses' => 'DemocracyApps\CNP\Controllers\CompositionsController@test'));

});

Route::get("/unknownproject", function () {
    return "Unknown Project";
});

class PP {

    public $label = null;
    public $value = null;
    public function __construct($label, $value) {
        $this->label = $label;
        $this->value = $value;
    }

}

Route::group(['prefix' => 'ajax'], function () 
    {
        Route::get('person', function()
        {
            $term = Input::get('term');
            $composer = \DemocracyApps\CNP\Compositions\Composer::find(\Input::get('composer'));
            $project = $composer->project;
            $list = DAEntity\Person::getElementsLike($project, $term);
            $ret = array();
            foreach ($list as $item) {
                $ret[] = new PP($item->name, $item->id);
            }
//            $ret = array(new PP('Johnjohn', 101), new PP('Wilma', 102), new PP('Barney', 301), new PP('Bloomberg', 333), new PP('abc', 122));
            return json_encode($ret);            
        });

        Route::get('setProjectDefaultInputComposer', '\DemocracyApps\CNP\Controllers\ProjectsController@setDefaultInputComposer');
        Route::get('setProjectDefaultOutputComposer', '\DemocracyApps\CNP\Controllers\ProjectsController@setDefaultOutputComposer');

        Route::get('curate', function()
        {
            $value = implode(':', \Input::all());

            return json_encode($value);
        });
    }
);

Route::get('/kumu', array('as' => 'kumu', function ()
{
    $project = \Input::get('project');
    $file= public_path(). "/downloads/kumu1.csv";
    $fptr = fopen($file, "w");
    $line = "Label,Type,Description\n";
    fwrite($fptr,$line);
    $elements = DAEntity\Element::allProjectElements($project);
    foreach($elements as $d) {
        $line = $d->id . "," . CNP::getElementTypeName($d->type) . ",\"" . $d->name . "\"\n";
        fwrite($fptr,$line);
    }
    $line = "\n";
    fwrite($fptr,$line);
    $line = "\n";
    fwrite($fptr,$line);
    $line = "From,To,Type\n";
    fwrite($fptr,$line);
    $relations = DAEntity\Relation::getProjectRelations($project);
    $relationsTypesMap = DAEntity\Eloquent\RelationType::getRelationTypesMap();
    foreach($relations as $d) {
        $line = $d->fromId . "," . $d->toId . "," . $relationsTypesMap[$d->relationId] . "\n";
        fwrite($fptr,$line);
    }
    fclose($fptr);
    $headers = array(
                     'Content-Type: text/csv',
                    );
    return Response::download($file, 'kumu1.csv', $headers);

}));

Route::get('/download', function ()
{
    $file= public_path(). "/downloads/export.csv";
    $headers = array(
                     'Content-Type: text/csv',
                    );
    return Response::download($file, 'export.csv', $headers);

});


Route::get('/', function()
{
    $params = array();
    if (\Input::get('mode')) {
        $params['mode'] = \Input::get('mode');
    }
    return Redirect::route('account', \Input::all());
});

Route::get('account', array('as' => 'account', 'before' => 'cnp.auth', function()
{
    $user = DAEntity\Eloquent\User::find(\Auth::user()->getId());
    $person = DAEntity\Person::find($user->getElementId());
    $projects = DAEntity\Project::where('userid', '=', $user->getId())->get();
    return View::make('account', array('user' => $user, 'person' => $person, 
                      'projects' => $projects));
}));

Route::when('relationtypes*', 'cnp.auth');
Route::resource('relationtypes','DemocracyApps\CNP\Controllers\RelationTypesController');

Route::get('/map', 'DemocracyApps\CNP\Controllers\MapController@show');
Route::get('/map/test', 'DemocracyApps\CNP\Controllers\MapController@test');

/********************************
 ** Login/Logout
 ********************************/
Route::get('/login', function() {
    return View::make('login');
});

Route::get('/logout', function() {
    Auth::logout();
    return Redirect::to('/');
});

Route::get('/loginfb', 'DemocracyApps\CNP\Controllers\LoginController@fbLogin');
Route::get('/logintw', 'DemocracyApps\CNP\Controllers\LoginController@twitLogin');
Route::get('/logincheat', 'DemocracyApps\CNP\Controllers\LoginController@cheatLogin');


/********************************
 ********************************
 *
 * API routes
 * 
 ********************************
 *********************************/

Route::when('api/v1/*','force.ssl'); // Forces SSL if cnp.json has apiRequiresSsl=true

Route::when('api/v1/*', 'api.key'); // Logs user in based on API key - see User.php

Route::group(['prefix' => 'api/v1'], function () 
    {
        Route::resource('projects', 'DemocracyApps\CNP\Controllers\ProjectsController');
    }
);


