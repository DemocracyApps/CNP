<?php 

use \DemocracyApps\CNP\Entities as DAEntity;
use \DemocracyApps\CNP\Entities\Project;

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
        return View::make('world.home', array('project' => $project, 'owner' => $owner));
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

        Route::get('setProjectDefaultComposer', '\DemocracyApps\CNP\Controllers\ProjectsController@setDefaultComposer');

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


