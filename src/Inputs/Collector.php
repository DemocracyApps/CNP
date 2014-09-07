<?php
namespace DemocracyApps\CNP\Inputs;
use \DemocracyApps\CNP\Entities as DAEntity;

class Collector extends \Eloquent {
    protected $fullSpecification = null;
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

    public function getInputSpec ()
    {
        $this->checkReady();
        return $this->inputSpec;
    }
    public function getElementsSpec ()
    {
        $this->checkReady();
        return $this->elementsSpec;
    }
    public function getRelationsSpec ()
    {
        $this->checkReady();
        return $this->relationsSpec;
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

    public function initialize($input)
    {
        $this->checkReady();
        if ($this->inputType == 'auto-interactive') {
            $driverId = \Input::get('driver');
            if ($driverId){
                $this->driver = CollectorAutoInputter::find($driverId);
                if ( ! $this->driver) {
                    dd("No damn driver " . $driverId);
                }
                $this->driver->reInitialize();
            }
            else {
                $this->driver = new CollectorAutoInputter;
                $this->driver->initialize($this->inputSpec);
                $this->driver->save();
            }
        }
    }

    public function inputDone()
    {
        return $this->inputDone;
    }

    public function processInput($input)
    {
        if ($this->inputType == 'csv-simple') {
            self::processCsvInput($input);
        }
        else if ($this->inputType == 'auto-interactive') {
            $this->driver->extractSubmittedValues($input);
            if ($this->driver->inputDone()) {
                $this->inputDone = true;
                self::processAutoInput($input);
                $this->driver->delete();
            }
        }
    }

    private function processAutoInput($input) {
        $map = $this->inputSpec['map'];
        $values = $this->driver['runDriver']['map'];

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
                        $elementsIn[$column['element']] = $line[$column['column']];
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

    private function commonProcessInput ($data, $elementsSpec, $relationsSpec, $scape)
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

}
