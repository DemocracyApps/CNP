<?php
namespace DemocracyApps\CNP\Controllers;

use \DemocracyApps\CNP\Entities as DAEntity;
use \DemocracyApps\CNP\Compositions\Composer as Composer;
use \DemocracyApps\CNP\Compositions\Composition;
use \DemocracyApps\CNP\Compositions\Outputs\Vista;
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
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
        if (\Input::has('vista')) {
            $vista = Vista::find(\Input::get('vista'));
            $typeList = null;
            $elements = null;
            if ($vista->selector) {
                $typeList = array();
                $s = trim(preg_replace("([, ]+)", ' ', $vista->selector));
                if ($s) $types = explode(" ", $s);
                foreach ($types as $type) {
                    $typeList [] = \CNP::getElementTypeId($type);
                }
            }
            $s = trim(preg_replace("([, ]+)", ' ', $vista->input_composers));
            if ($s) $allowedComposers = explode(" ", $s);

            $page = \Input::get('page', 1);
            $pageLimit=\CNP::getConfigurationValue('pageLimit');
            $data = Element::getVistaElements ($vista->project, $allowedComposers, $typeList, $page, $pageLimit);
            $elements = \Paginator::make($data['items'], $data['total'], $pageLimit);

            $args = array('elements' => $elements, 'vista' => $vista);
            $args['composer'] = $vista->output_composer;
            return \View::make('stories.vistaindex', array('elements'=>$elements, 'vista'=>$vista, 'composer'=>$vista->output_composer));
            return \View::make('vistas.index', $args);
        }
        else { // just raw
            if (\Input::has('project')) {
                $project = Project::find(\Input::get('project'));
                $page = \Input::get('page', 1);
                $pageLimit=\CNP::getConfigurationValue('pageLimit');
                $data = Composition::allProjectCompositionsPaged($project->id, $page, $pageLimit);
                $stories = \Paginator::make($data['items'], $data['total'], $pageLimit);
                return \View::make('stories.index', array('stories' => $stories, 'project' => $project));
            }
            else {
                return \Redirect::to('/projects');
            }
        }
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
    public function show($id)
    {
        $story = DAEntity\Story::find($id);
        $elements = array();
        $elements[$story->id] = $story;
        $elementRelations = array();
       
        $elements = DAEntity\Relation::getRelatedElements($story->id, null);
        array_unshift($elements, $story);
        foreach ($elements as $element) { // Get the relations
            if ( ! array_key_exists($element->id, $elements)) $elements[$element->id] = $element;
            $relations = DAEntity\Relation::getRelations($element->id);
            //if ($element->type != 3) dd($relations);
            $elementRelations[$element->id] = array();
            foreach ($relations as $relation) {
                $to = $relation->toId;
                $relType = DAEntity\Eloquent\RelationType::find($relation->relationId);
                $relationName = $relType->name;
                if ( ! array_key_exists($to, $elements)) {
                    $elements[$to] = DAEntity\Element::find($to);
                }
                $elementRelations[$element->id][] = array($relationName, 
                                                          $elements[$to]->name . " (".$elements[$to]->id.")");
            }
        }

        return \View::make('stories.show', array('story' => $story, 'elements' => $elements,
                                                 'relations' => $elementRelations));
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
        return \Redirect::to('/stories?project='.$composer->project);
    }
}
