<?php
namespace DemocracyApps\CNP\Inputs;
use \DemocracyApps\CNP\Entities as DAEntity;

class Collector extends \Eloquent {
    protected $fullSpecification = null;

    //
    // A story is commonly about something or is an extension of something
    // and so a relationship should be created when the story is created.
    // 
    // Because a story may refer to more than one denizens, the referent is
    // a DemocracyApps\CNP\Graph\DenizenSet.
    protected $referent = null; 
    protected $referentRelation = null; 


    protected $anchor = null; /* not sure. Sometimes we don't want to create a story, just some
                               * other elements. I think this is that, maybe an array of IDs.
                               * if null, we just create a story?
                               */

    
    protected $inputSpec = null;
    protected $inputType = null;
    protected $elementsSpec = null;
    protected $relationsSpec = null;
    protected $messages = null; // Type should be Illuminate\Support\MessageBag
    protected $driver = null;
    protected $inputDone = false;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'collectors';

    /**
     * Load the full specification and initialize pointers to individual sections
     */
    protected function checkReady()
    {
        if ( ! $this->fullSpecification) {
            $this->fullSpecification = $this->resolveFullSpecification();
        }
        if (array_key_exists('input', $this->fullSpecification)) {
            $this->inputSpec = $this->fullSpecification['input'];
            if (array_key_exists('inputType', $this->inputSpec)) {
                $this->inputType = $this->inputSpec['inputType'];
            }
        }
        if (array_key_exists('elements', $this->fullSpecification)) {
            $this->elementsSpec = $this->fullSpecification['elements'];
        }
        if (array_key_exists('relations', $this->fullSpecification)) {
            $this->relationsSpec = $this->fullSpecification['relations'];
        }
    }

    public function initialize($input)
    {
        $this->checkReady();
        // Right now initialization is only relevant for interactive input
        if ($this->inputType == 'auto-interactive') {
            $driverId = \Input::get('driver');
            if ($driverId){
                $this->driver = CollectorInputDriver::find($driverId);
                if ( ! $this->driver) {
                    dd("No damn driver " . $driverId);
                }
                $this->driver->reInitialize();
            }
            else {
                $this->driver = new CollectorInputDriver;
                $this->driver->initialize($this->inputSpec);
                $this->driver->save();
            }
        }
    }

    protected function resolveFullSpecification ()
    {
        $spec = json_minify($this->specification);
        $spec = json_decode($spec, true);
        if (array_key_exists('baseSpecificationId', $spec)) {
            $nextCollector = Collector::find($spec['baseSpecificationId']);
            $tmpspec = $nextCollector->resolveFullSpecification($spec['baseSpecificationId']);
            if ( ! array_key_exists('input', $spec) && array_key_exists('input', $tmpspec)) {
                $spec['input'] = $tmpspec['input'];
            }
            if ( ! array_key_exists('elements', $spec) && array_key_exists('elements', $tmpspec)) {
                $spec['elements'] = $tmpspec['elements'];
            }
            if ( ! array_key_exists('relations', $spec) && array_key_exists('relations', $tmpspec)) {
                $spec['relations'] = $tmpspec['relations'];
            }
        }
        return $spec;
    }

	public function getFullSpecification ()
	{
        $this->checkReady();
    	return $this->fullSpecification;
	}

    public function setReferent ($d) 
    {
        $this->referent = new DenizenSet;
        $this->referent->addDenizens(array($d));
    }

    public function setReferentSet (\DemocracyApps\CNP\Graph\DenizenSet $set) 
    {
        $this->referent = $set;
    }

    public function validForInput() // For now, just check input. Should do full validity check.
    {
        $this->checkReady();
        return ($this->inputSpec != null);
    }

    public function messages () 
    {
        return $this->messages;
    }

    public function getInputType ()
    {
        $this->checkReady();
        return $this->inputType;
    }

    public function validateInput($input)
    {
        $ok = true;
        $this->checkReady();
        if ($this->inputType == 'csv-simple') {
            $rules = ['csv'=>'required'];
            $validator = \Validator::make($input, $rules);
            if ($validator->fails()) {
                $this->messages = $validator->messages();
                $ok = false;
            }
        }
        else {
            \Log::info("No validation defined for input type " . $this->inputType);
        }
        return $ok;
    }

    public function getDriver()
    {
        return $this->driver;
    }

    public function inputDone()
    {
        return $this->inputDone;
    }

    private function processAutoInput($input) {
        $map = $this->inputSpec['map'];
        $values = $this->driver['runDriver']['map'];

        if (! $map) return "No map!";

        $elementsIn = array();
        $title = "No Title";
        $summary = null;

        foreach ($map as $item) {
            // If no id, then it wasn't used to get input (e.g., page breaks).
            if (array_key_exists('use', $item) && array_key_exists('id', $item)) {
                $id = $item['id'];
                if (array_key_exists($id, $values)) {
                    $base = ucfirst($values[$id]['inputType']);
                    $inputControllerClassName = '\DemocracyApps\CNP\Inputs\\'.$base."InputController";
                    $reflectionMethod = new \ReflectionMethod($inputControllerClassName, 'getValue');
                    $val = $reflectionMethod->invoke(null, $values[$id]);
                    $use = $item['use'];
                    if ($use == 'title') {
                        $title = $val;
                    }
                    elseif ($use == 'summary') {
                        $summary = $val;
                    }
                    else {
                        $elementId = $item['elementId'];
                        $elementsIn[$elementId] = $val;
                    }
                }
            }
        }

        $data = array();
        $data['title'] = $title;
        $data['summary'] = $summary;
        $data['elementsIn'] = $elementsIn;
        $this->commonProcessInput($data, $this->elementsSpec, $this->relationsSpec, $this->scape);
    }

    private function processCsvInput($input) 
    {
        ini_set("auto_detect_line_endings", true); // Deal with Mac line endings

        $file = \Input::file('csv');
        $myfile = fopen($file->getRealPath(), "r") or die("Unable to open file!");

        $map = $this->inputSpec['map'];
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
                        $elementsIn[$column['elementId']] = $line[$column['column']];
                    }
                }
                $data = array();
                $data['title'] = $title;
                $data['summary'] = null;
                $data['elementsIn'] = $elementsIn;
                $this->commonProcessInput($data, $this->elementsSpec, $this->relationsSpec, $this->scape);
            }
        }
    }

    public static function tryit ($elementType, $content, $properties) 
    {
        dd("Hallelujah!");
    }

    private static function registerElementProcessors ()
    {
        //ElementGenerator::registerElementGenerator('Tag', 'DemocracyApps\CNP\Inputs\Collector::tryit');
    }

    public function processInput($input)
    {
        self::registerElementProcessors();
        if ($this->inputType == 'csv-simple') {
            self::processCsvInput($input);
        }
        else if ($this->inputType == 'auto-interactive') {
            $this->driver->extractSubmittedValues($input); // Import latest batch of form data into driver
            if ($this->driver->inputDone()) {
                $this->inputDone = true;
                self::processAutoInput($input);
                $this->driver->delete();
            }
        }
    }

    private function commonProcessInput ($data, $elementsSpec, $relationsSpec, $scape)
    {
        $denizens = array();

        $title = $data['title'];
        $summary = $data['summary'];
        $elementsIn = $data['elementsIn'];

        // So now we create output denizens
        foreach ($elementsSpec as $espec) {
            $id = $espec['id'];
            if (array_key_exists($id, $elementsIn)) {
                $createdDenizens = DenizenGenerator::generateDenizen($espec['type'], $id, $elementsIn[$id], null, $scape);
                if ($createdDenizens) $denizens[$id] = $createdDenizens; // Can happen, e.g., tags
            }
            else {
                if (array_key_exists('required', $espec) && $espec['required'] == true) {
                    return "Required element " . $id . " doesn't exist on datum ". $count;
                }
            }
        }

        // Now save them all out, relate them, etc.
        $story = new \DemocracyApps\CNP\Entities\Story($title, \Auth::user()->getId());
        if ($summary) $story->content = $summary;
        $story->scapeId = $scape;
        $story->save();
        foreach($denizens as $denizenList) {
            foreach ($denizenList as $denizen) {
                $denizen->save();
                $relations = DAEntity\Relation::createRelationPair($story->id, 
                                                                   $denizen->id, "HasPart");
                foreach ($relations as $relation) { $relation->save(); }
            }
        }
        foreach ($relationsSpec as $relation) {
            $from = $relation['from'];
            $to   = $relation['to'];
            $relType = $relation['type'];
            if (array_key_exists($from, $denizens) && array_key_exists($to,$denizens)) {
                $dfrom = $denizens[$from];
                $dto   = $denizens[$to];
                // One or the other may expand to multiple denizens (e.g., tags), but let's not
                // let things get out of hand.
                if (count($dfrom) > 1 && count($dto) > 1) {
                    throw new \Exception("Collector processing - N X M relation generation not allowed");
                }
                foreach ($dfrom as $df) {
                    foreach ($dto as $dt) {
                        $relations = DAEntity\Relation::createRelationPair($df->id, 
                                                                           $dt->id,
                                                                           $relType);
                        foreach ($relations as $relation) { $relation->save(); }                        
                    }
                }
            }
        }

    }

}
