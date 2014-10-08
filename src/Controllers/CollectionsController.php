<?php namespace DemocracyApps\CNP\Controllers;

use \DemocracyApps\CNP\Entities\Project;
use \DemocracyApps\CNP\Compositions\Composition;
use \DemocracyApps\CNP\Entities\Eloquent\Collection;

use \DemocracyApps\CNP\Utility\Api as Api;

class CollectionsController extends ApiController {

    public function index()
    {
        $collections = Collection::where('userid', '=', \Auth::user()->getId())->get();
        return \View::make('collections.index', array('collections' => $collections));
    }

    public function create() 
    {
        $projects = Project::where('userid', '=', \Auth::user()->getId())->get();
        return \View::make('collections.create', array('projects' => $projects));
    }

    public function store()
    {
        $data = \Input::all();
        $rules = ['name'=>'required'];
        $validator = \Validator::make($data, $rules);
        if ($validator->fails()) {
            return \Redirect::back()->withInput()->withErrors($validator->messages());
        }
        // Validation OK, let's create it
        $project = $data['project'];
        $collection = new Collection;
        $min = Composition::where('project', '=', $project)->min('id');
        $max = Composition::where('project', '=', $project)->max('id');

        $collection->name = $data['name'];
        $collection->project = $data['project'];
        $collection->userid = \Auth::user()->getId();
        $collection->set = array($min, $max);
        $collection->save();
        return \Redirect::to('/collections');
    }
}
