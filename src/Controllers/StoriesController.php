<?php
namespace DemocracyApps\CNP\Controllers;

use \DemocracyApps\CNP\Entities as DAEntity;
use \DemocracyApps\CNP\Inputs\Collector as Collector;
use \DemocracyApps\CNP\Inputs\CollectorAutoInputter as CollectorAutoInputter;

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
        $elements = DAEntity\Relation::getRelatedDenizens($id, "HasPart");
        $elementRelations = array();
        $denizens = array();
        $denizens[$story->id] = $story;
        $count = 0;
        foreach ($elements as $element) { // Get the relations
        	if ( ! array_key_exists($element->id, $denizens)) $denizens[$element->id] = $element;
        	$relations = DAEntity\Relation::getRelations($element->id);
        	//if ($element->type != 3) dd($relations);
        	++$count;
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

        if ( ! $collector->validForInput()) throw new \Exception("Collector ".$collectorId . " not valid for input.");
        $collector->initialize(\Input::all());

    	$inputType = $collector->getInputType();
        \Log::info("Input type is " . $inputType);
    	if ($inputType == 'csv-simple') {
	    	return \View::make('stories.csvUpload', array('collector' => $collector));
    	}
    	elseif ($inputType == 'auto-interactive') {
    		$driver = $collector->getDriver();
            return \View::make('stories.autoinput', array('collector' => $collector, 'driver' => $driver));
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

        $inputType = $collector->getInputType();
        \Log::info("Input type is " . $inputType);
        $collector->initialize($input);
        if ($inputType == 'csv-simple') {
            self::processCsvInput($input, $collector);
        }
        elseif ($inputType == 'auto-interactive') {
            $driver = $collector->getDriver();
            $driver->extractSubmittedValues($input);
            if ($driver->inputDone()) {
                $values = $driver['runDriver']['map'];
                $view = self::processAutoInput($input, $collector, $driver);
                $driver->delete();
                return $view;
            }
            else {
                return \View::make('stories.autoinput', array('collector' => $collector, 'driver' => $driver));
            }
        }
        else {
            return "Unknown input type " . $inputType;
        }
        return \Redirect::to('/stories');
    }

    private function processInput ($data, $elementsSpec, $relationsSpec, $scape)
    {
        $denizens = array();

        $title = $data['title'];
        $summary = $data['summary'];
        $elementsIn = $data['elementsIn'];

        // So now we create output denizens
        foreach ($elementsSpec as $espec) {
            $tag = $espec['tag'];
            if (array_key_exists($tag, $elementsIn)) {
                $className = '\\DemocracyApps\\CNP\Entities\\'.$espec['type'];
                if (!class_exists($className)) return "No class " . $className;
                $denizen = new $className($tag, \Auth::user()->getId());
                $denizen->scapeId = $scape;
                $denizen->content = $elementsIn[$tag];
                $denizens[$tag] = $denizen;                     
            }
            else {
                if (array_key_exists('required', $espec) && $espec['required'] == true) {
                    return "Required element " . $tag . " doesn't exist on datum ". $count;
                }
            }
        }

        // Now save them all out, relate them, etc.
        $story = new \DemocracyApps\CNP\Entities\Story($title, \Auth::user()->getId());
        if ($summary) $story->content = $summary;
        $story->scapeId = $scape;
        $story->save();
        foreach($denizens as $denizen) {
            $denizen->save();
            $relations = DAEntity\Relation::createRelationPair($story->id, 
                                                               $denizen->id, "HasPart");
            foreach ($relations as $relation) { $relation->save(); }
        }
        foreach ($relationsSpec as $relation) {
            $from = $relation['from'];
            $to   = $relation['to'];
            $relType = $relation['type'];
            if (array_key_exists($from, $denizens) && array_key_exists($to,$denizens)) {
                $relations = DAEntity\Relation::createRelationPair($denizens[$from]->id, 
                                                                   $denizens[$to]->id,
                                                                   $relType);
                foreach ($relations as $relation) { $relation->save(); }
            }
        }

    }
    private function processAutoInput($input, $collector, $driver) {
        $scape = $collector->scape;
        $inputSpec = $collector->getInputSpec();
        $elementsSpec = $collector->getElementsSpec();
        $relationsSpec = $collector->getRelationsSpec();
        $map = $inputSpec['map'];
        $values = $driver['runDriver']['map'];

        if (! $map) return "No map!";

        $elementsIn = array();
        $title = "No Title";
        $summary = null;

        foreach ($map as $item) {
            if (array_key_exists('use', $item)) {
                $tag = $item['tag'];
                $use = $item['use'];
                if ($use == 'title') {
                    $title = $values[$tag]['value'];
                }
                elseif ($use == 'summary') {
                    $summary = $values[$tag]['value'];
                }
                else {
                    $elementsIn[$tag] = $values[$tag]['value'];
                }
            }
        }

        $data = array();
        $data['title'] = $title;
        $data['summary'] = $summary;
        $data['elementsIn'] = $elementsIn;
        $this->processInput($data, $elementsSpec, $relationsSpec, $scape);
        return \Redirect::to('/stories');
    }


    private function processCsvInput($input, $collector) 
    {
        ini_set("auto_detect_line_endings", true); // Deal with Mac line endings

		$file = \Input::file('csv');
		$myfile = fopen($file->getRealPath(), "r") or die("Unable to open file!");

		$scape = $collector->scape;

        $inputSpec = $collector->getInputSpec();
        $elementsSpec = $collector->getElementsSpec();
        $relationsSpec = $collector->getRelationsSpec();

    	$map = $inputSpec['map'];
    	if (! $map) return "No map!";
    	$skip = $map['skip'];
    	$columnMap = $map['columnMap'];

    	while ($skip-- > 0) {
    		$line = fgetcsv($myfile);
    		\Log::info("Skipping a line");
    	}
		$count = 0;
		while ( ! feof($myfile) ) {
			$line = fgetcsv($myfile);
			$elementsIn = array();
			$title = "No Title";
			if ($line) {
				++$count;
				foreach ($columnMap as $column) {
					$use = $column['use'];
					if ($use == 'title') {
						$title = $line[$column['column']];
					}
					else {
						$elementsIn[$column['element']] = $line[$column['column']];
					}
				}
                $data = array();
                $data['title'] = $title;
                $data['summary'] = null;
                $data['elementsIn'] = $elementsIn;
                $this->processInput($data, $elementsSpec, $relationsSpec, $scape);
			}
		}
        return \Redirect::to('stories');			
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		//
	}


	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		//
	}


	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		//
	}


}
