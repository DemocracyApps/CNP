<?php
namespace DemocracyApps\CNP\Controllers;

use \DemocracyApps\CNP\Entities as DAEntity;
use \DemocracyApps\CNP\Compositions\Composer as Composer;
use \DemocracyApps\CNP\Compositions\Outputs\Vista;
use \DemocracyApps\CNP\Entities\Denizen;

class StoriesController extends BaseController {
    protected $story;

    public function __construct(DAEntity\Story $story)
    {
        $this->story = $story;
    }

    public function curate()
    {
        return \View::make('stories.curate');
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
            $denizens = null;
            if ($vista->selector) {
                $typeList = array();
                $s = trim(preg_replace("([, ]+)", ' ', $vista->selector));
                if ($s) $types = explode(" ", $s);
                foreach ($types as $type) {
                    $typeList [] = \CNP::getDenizenTypeId($type);
                }
            }
            $s = trim(preg_replace("([, ]+)", ' ', $vista->input_composers));
            if ($s) $allowedComposers = explode(" ", $s);

            $page = \Input::get('page', 1);
            $pageLimit=\CNP::getConfigurationValue('pageLimit');
            $data = Denizen::getVistaDenizens ($vista->scape, $allowedComposers, $typeList, $page, $pageLimit);
            $denizens = \Paginator::make($data['items'], $data['total'], $pageLimit);

            $args = array('denizens' => $denizens, 'vista' => $vista);
            $args['composer'] = $vista->output_composer;
            return \View::make('stories.vistaindex', array('denizens'=>$denizens, 'vista'=>$vista, 'composer'=>$vista->output_composer));
            return \View::make('vistas.index', $args);
        }
        else { // just raw
            $page = \Input::get('page', 1);
            $pageLimit=\CNP::getConfigurationValue('pageLimit');
            $data = DAEntity\Story::allPaged($page, $pageLimit);
            $stories = \Paginator::make($data['items'], $data['total'], $pageLimit);
            return \View::make('stories.index')->with('stories', $stories);
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
        $denizens = array();
        $denizens[$story->id] = $story;
        $elementRelations = array();
       
        $elements = DAEntity\Relation::getRelatedDenizens($story->id, null);
        array_unshift($elements, $story);
        foreach ($elements as $element) { // Get the relations
            if ( ! array_key_exists($element->id, $denizens)) $denizens[$element->id] = $element;
            $relations = DAEntity\Relation::getRelations($element->id);
            //if ($element->type != 3) dd($relations);
            $elementRelations[$element->id] = array();
            foreach ($relations as $relation) {
                $to = $relation->toId;
                $relType = DAEntity\Eloquent\RelationType::find($relation->relationId);
                $relationName = $relType->name;
                if ( ! array_key_exists($to, $denizens)) {
                    $denizens[$to] = DAEntity\Denizen::find($to);
                }
                $elementRelations[$element->id][] = array($relationName, 
                                                          $denizens[$to]->name . " (".$denizens[$to]->id.")");
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
            $composer->setReferentByDenizenId(\Input::get('referent'));
        }

    	$inputType = $composer->getInputType();
    	if ($inputType == 'csv-simple') {
	    	return \View::make('stories.csvUpload', array('composer' => $composer));
    	}
    	elseif ($inputType == 'auto-interactive') {
            return \View::make('stories.autoinput', array('composer' => $composer));
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
		if (\Auth::guest()) {
            return \Redirect::to('/login');			
		}
        $input = \Input::all();
        $composer = Composer::find(\Input::get('composer'));
        if ( ! $composer->validateInput($input)) {
            return \Redirect::back()->withInput()->withErrors($composer->messages());
        }
        if (\Input::get('referentId')) {
            $composer->setReferentByReferentId(\Input::get('referentId'));
        }

        $inputType = $composer->getInputType();

        $composer->initializeForInput($input);
        $composition = new \DemocracyApps\CNP\Compositions\Composition;
        $composition->input_composer_id = $composer->id;
        $composition->save();
        $composer->processInput($input, $composition);
        if ($inputType == 'auto-interactive') {
            if ( ! $composer->getDriver()->done()) {
                return \View::make('stories.autoinput', array('composer' => $composer));
            }
        }
        return \Redirect::to('/stories');
    }
}
