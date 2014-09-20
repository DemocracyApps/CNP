<?php 

use \DemocracyApps\CNP\Entities as DAEntity;

Log::info("Top of routes with URI " . \Request::server('REQUEST_URI') .
          " and method " .\Request::server('REQUEST_METHOD'));

/********************************
 ********************************
 *
 * Default website routes
 * 
 ********************************
 *********************************/

Route::get('/q', function () {
    Queue::push(function ($job) {



        \Log::info("Queues are very cool");


        $job->delete();
    });
    echo "Hello";
});

Route::get('/test', function()
{
    $s = ",  ,  a sa, ls,, l,";
    $s = trim(preg_replace("([, ]+)", ' ', $s));
    $s = explode(" ", $s);
    dd($s);
    return View::make('test');
});

Route::get('/demo', function()
{
    $stage = Input::get('stage');
    return View::make('demo.'.$stage, array());
});

// DenizensController doesn't actually need all the routes, but I can't 
// figure out how to call just show($id)
Route::resource('denizens', 'DemocracyApps\CNP\Controllers\DenizensController');

// /stories/export must be defined before Route::resource('stories'). Probably need
// to come up with a different route.
Route::get('/stories/export', array('as' => 'stories.export', function() 
    {
        \Log::info("Heading off to stories.export");
        return View::make('stories.export', array('scape' => \Input::get('scape')));
    }));


Route::resource('stories', 'DemocracyApps\CNP\Controllers\StoriesController');
Route::resource('scapes', 'DemocracyApps\CNP\Controllers\ScapesController');
Route::resource('composers', 'DemocracyApps\CNP\Controllers\ComposersController');
Route::resource('vistas', 'DemocracyApps\CNP\Controllers\VistasController');

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
            $scape = $composer->scape;
            $list = DAEntity\Person::getDenizensLike($scape, $term);
            $ret = array();
            foreach ($list as $item) {
                $ret[] = new PP($item->name, $item->id);
            }
//            $ret = array(new PP('Johnjohn', 101), new PP('Wilma', 102), new PP('Barney', 301), new PP('Bloomberg', 333), new PP('abc', 122));
            return json_encode($ret);            
        });
    }
);

Route::get('/kumu', array('as' => 'kumu', function ()
{
    $scape = \Input::get('scape');
    $file= public_path(). "/downloads/kumu1.csv";
    $fptr = fopen($file, "w");
    $line = "Label,Type,Tags,Description,Attribution\n";
    fwrite($fptr,$line);
    $denizens = DAEntity\Denizen::allScapeDenizens($scape);
    foreach($denizens as $d) {
        $line = $d->id . "," . CNP::getDenizenTypeName($d->type) . ",,\"" . $d->name . "\",\n";
        fwrite($fptr,$line);
    }
    $line = ",,,,\n";
    fwrite($fptr,$line);
    $line = ",,,,\n";
    fwrite($fptr,$line);
    $line = "From,To,Label,Type,Tags\n";
    fwrite($fptr,$line);
    $relations = DAEntity\Relation::getScapeRelations($scape);
    $relationsTypesMap = DAEntity\Eloquent\RelationType::getRelationTypesMap();
    foreach($relations as $d) {
        $line = $d->fromId . "," . $d->toId . ",," . $relationsTypesMap[$d->relationId] . ",\n";
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
    $person = DAEntity\Person::find($user->getDenizenId());
    $scapes = DAEntity\Scape::allUserDenizens($user->getId());
    return View::make('account', array('user' => $user, 'person' => $person, 
                      'scapes' => $scapes));
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
        Route::resource('scapes', 'DemocracyApps\CNP\Controllers\ScapesController');
    }
);


