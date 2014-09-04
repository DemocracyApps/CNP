<?php
namespace DemocracyApps\CNP\Controllers;

use \DemocracyApps\CNP\Entities as DAEntity;

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
		\Log::info("In stories controller index");
        $stories = DAEntity\Story::all();
        return \View::make('stories.index')->with('stories', $stories);
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
		$spec = \Input::get('spec')?\Input::get('spec'):'default';
		if ($spec == 'default') {
        	return \View::make('stories.create'); // For now ...
    	}
    	// Ok, we're here, which means we're operating according to a spec. Hoo, boy.
    	$spec = DAEntity\Eloquent\Collector::find($spec);
    	if ( ! $spec ) return "StoriesController.create - no such spec";

    	return \View::make('stories.csvUpload', array('spec' => $spec));
    	return "We are operating according to the spec: " . $spec->name;
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
		$spec = \Input::get('spec')?\Input::get('spec'):'default';
		if ($spec == 'default') {
			/*
			 * Old story save - probably will lose it later.
			 */
	        $rules = ['name'=>'required', 'content'=>'required'];
	        $validator = \Validator::make($input, $rules);
	        if ($validator->fails()) {
	            return \Redirect::back()->withInput()->withErrors($validator->messages());
	        }
	        $this->story->setName($input['name']);
	        $this->story->setContent($input['content']);
	        $this->story->save();
	        $user = DAEntity\Eloquent\User::find(\Auth::user()->getId());
	        $person = DAEntity\Person::find($user->getDenizenId());
	        $relations = DAEntity\Relation::createRelationPair($person->getId(), $this->story->getId(),
	                                                          "CreatorOf");
	        foreach($relations as $relation) {
	            $relation->save();
	        }
	        return var_dump($relations); // Should redirect back to /stories or to display of this story
    	}
    	// Ok, we're here, which means we're operating according to a spec. Hoo, boy.
    	// 	        $rules = ['name'=>'required', 'content'=>'required'];
        $rules = ['csv'=>'required'];
        $validator = \Validator::make($input, $rules);
        if ($validator->fails()) {
            return \Redirect::back()->withInput()->withErrors($validator->messages());
        }
        ini_set("auto_detect_line_endings", true); // Deal with
    	// Let's start by just learning to parse the file

		if ( ! \Input::hasFile('csv')) {
			die ("Not sure what to do here since we already checked for the parameter");
		}
		$file = \Input::file('csv');
		$myfile = fopen($file->getRealPath(), "r") or die("Unable to open file!");
		if (! $myfile ) return "WhaFa!!??";

		$collector = DAEntity\Eloquent\Collector::find($spec);
		$scape = $collector->scape;

        $spec = DAEntity\Eloquent\Collector::getFullSpecification($spec);
    	if ( ! $spec ) return "StoriesController.create - no such spec";

    	$inputSpec = $spec['input'];
    	$elementsSpec = $spec['elements'];
    	$relationsSpec = $spec['relations'];

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
			$denizens = array();
			$title = "No Title";
			if ($line) {
				++$count;
				foreach ($columnMap as $column) {
					$use = $column['use'];
					if ($use == 'title') {
						$title = $line[$column['column']];
						\Log::info("THE TITLE WILL BE " . $title);
					}
					else {
						$elementsIn[$column['element']] = $line[$column['column']];
					}
				}
				// So now we create output denizens
				foreach ($elementsSpec as $espec) {
					$tag = $espec['tag'];
					if (array_key_exists($tag, $elementsIn)) {
						\Log::info("Create a new " . $espec['type'] . " with name " . $tag 
									. " and value " . $elementsIn[$tag]);
						$className = '\\DemocracyApps\\CNP\Entities\\'.$espec['type'];
						if (!class_exists($className)) return "No class " . $className;
						$denizen = new $className($tag, \Auth::user()->getId());
						$denizen->scapeId = $scape;
						$denizen->content = $elementsIn[$tag];
						$denizens[$tag] = $denizen;						
					}
					else {
						\Log::info(" No element with tag " . $tag);
						if (array_key_exists('required', $espec) && $espec['required'] == true) {
							return "Required element " . $tag . " doesn't exist on datum ". $count;
						}
					}
				}

				// So now we save them all out, relate them, etc.
				$story = new \DemocracyApps\CNP\Entities\Story($title, \Auth::user()->getId());
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
						\Log::info("Create relation ".$relType." from ".$denizens[$from]->name.
								   " to ".$denizens[$to]->name);
						$relations = DAEntity\Relation::createRelationPair($denizens[$from]->id, 
																		   $denizens[$to]->id,
																		   $relType);
						foreach ($relations as $relation) { $relation->save(); }
					}
				}

			}
		}
		// $line = fgets($myfile);
		
        return \Redirect::to('stories');			
	}


	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		//
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
