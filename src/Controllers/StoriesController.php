<?php
namespace DemocracyApps\CNP\Controllers;

use \DemocracyApps\CNP\Entities as DAEntity;
use \DemocracyApps\CNP\Compositions\Composer as Composer;
use \DemocracyApps\CNP\Compositions\Composition;
use \DemocracyApps\CNP\Entities\Element;
use \DemocracyApps\CNP\Entities\Project;

class StoriesController extends BaseController {
    protected $story;

    public function __construct(DAEntity\Story $story)
    {
        $this->story = $story;
    }
    public function explore()
    {
        return \View::make('stories.explore');
    }

    public function curate()
    {
        if (\Input::has('project')) {
            $project = \Input::get('project');
            $composers = \DemocracyApps\CNP\Compositions\Composer::where('project', '=', $project)->get();
            $ctmp = array();
            foreach($composers as $c) {
                if (strstr($c->contains, 'input')) $ctmp[] = $c;
                \Log::info("Composer contains: " . $c->contains);
            }
            $selectedComposers = null;
            if (\Input::has('templates')) {
                $selectedComposers = \Input::get('templates');
            }
            return \View::make('stories.curate', 
                           array('project' => $project,
                                 'composers' => $ctmp,
                                 'selectedComposers' => $selectedComposers));
        }
        else {
            return \View::make('stories.curate');
        }
    }


	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		if (\Auth::guest()) {
            return \Redirect::to('/login');			
		}

        if ( ! \Input::has('composer')) throw new \Exception("No composer id specified.");

    	$composer = Composer::find(\Input::get('composer'));
    	if ( ! $composer ) throw new \Exception("Composer ".\Input::get('composer'). " not found.");

        if ( ! $composer->validForInput()) throw new \Exception("Composer ".\Input::get('composer') . " not valid for input.");
        $composer->initializeForInput(\Input::all());

        if (\Input::get('referent')) {
            $composer->setReferentByElementId(\Input::get('referent'));
        }

        $composition = new \DemocracyApps\CNP\Compositions\Composition;
        $composition->input_composer_id = $composer->id;
        $composition->userid = \Auth::user()->getId();
        $composition->title = "No title";
        $composition->project = $composer->project;
        $composition->save();

    	$inputType = $composer->getInputType();
    	if ($inputType == 'csv-simple') {
	    	return \View::make('stories.csvUpload', array('composer' => $composer, 'composition' => $composition));
    	}
    	elseif ($inputType == 'auto-interactive') {
            return \View::make('stories.autoinput', array('composer' => $composer, 'composition' => $composition));
    	}
    	else {
    		return "Unknown input type " . $inputType;
    	}
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
        \Log::info("In store");
		if (\Auth::guest()) {
            return \Redirect::to('/login');			
		}
        $input = \Input::all();
        $composition = Composition::find(\Input::get('composition'));
        $composer = Composer::find($composition->input_composer_id);
        if ( ! $composer->validateInput($input)) {
            return \Redirect::back()->withInput()->withErrors($composer->messages());
        }
        if (\Input::get('referentId')) {
            $composer->setReferentByReferentId(\Input::get('referentId'));
        }
        $inputType = $composer->getInputType();

        $composer->initializeForInput($input);
        $composer->processInput($input, $composition);
        if ($inputType == 'auto-interactive') {
            if ( ! $composer->getDriver()->done()) {
                return \View::make('stories.autoinput', array('composer' => $composer, 'composition' => $composition));
            }
        }
        return \Redirect::to('/compositions?project='.$composer->project);
    }
}
