<?php
namespace DemocracyApps\CNP\Controllers;

use \DemocracyApps\CNP\Entities as DAEntity;
use \DemocracyApps\CNP\Inputs\Collector as Collector;

class StoriesController extends BaseController {
    protected $story;

    public function __construct(DAEntity\Story $story)
    {
        $this->story = $story;
    }

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
        $stories = DAEntity\Story::all();
        return \View::make('stories.index')->with('stories', $stories);
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

        if ( ! \Input::has('collector')) throw new \Exception("No collector id specified.");

    	$collector = Collector::find(\Input::get('collector'));
    	if ( ! $collector ) throw new \Exception("Collector ".\Input::get('collector'). " not found.");

        if ( ! $collector->validForInput()) throw new \Exception("Collector ".\Input::get('collector') . " not valid for input.");
        $collector->initialize(\Input::all());

        if (\Input::get('referent')) {
            $collector->setReferentByDenizenId(\Input::get('referent'));
        }

    	$inputType = $collector->getInputType();
    	if ($inputType == 'csv-simple') {
	    	return \View::make('stories.csvUpload', array('collector' => $collector));
    	}
    	elseif ($inputType == 'auto-interactive') {
            return \View::make('stories.autoinput', array('collector' => $collector));
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
        $collector = Collector::find(\Input::get('collector'));
        if ( ! $collector->validateInput($input)) {
            return \Redirect::back()->withInput()->withErrors($collector->messages());
        }
        if (\Input::get('referentId')) {
            $collector->setReferentByReferentId(\Input::get('referentId'));
        }

        $inputType = $collector->getInputType();

        $collector->initialize($input);
        $collector->processInput($input);
        if ($inputType == 'auto-interactive') {
            if ( ! $collector->inputDone()) {
                return \View::make('stories.autoinput', array('collector' => $collector));
            }
        }
        return \Redirect::to('/stories');
    }
}
