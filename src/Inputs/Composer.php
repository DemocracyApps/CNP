<?php
namespace DemocracyApps\CNP\Inputs;
use \DemocracyApps\CNP\Entities as DAEntity;
use \DemocracyApps\CNP\Graph\DenizenSet;
use \DemocracyApps\CNP\Entities\Relation;
use \DemocracyApps\CNP\Outputs\ComposerOutputDriver;

class Composer extends \Eloquent {
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
    protected $outputSpec = null;
    protected $elementsSpec = null;
    protected $relationsSpec = null;
    protected $messages = null; // Type should be Illuminate\Support\MessageBag
    protected $inputDriver = null;
    protected $inputDone = false;
    protected $outputDriver = null;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'composers';

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
        if (array_key_exists('output', $this->fullSpecification)) {
            $this->outputSpec = $this->fullSpecification['output'];
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
        if ($this->inputType == 'auto-interactive' && $input != null) {
            $driverId = \Input::get('driver');
            if ($driverId){
                $this->inputDriver = ComposerInputDriver::find($driverId);
                if ( ! $this->inputDriver) {
                    dd("No damn driver " . $driverId);
                }
                $this->inputDriver->reInitialize($this);
            }
            else {
                $this->inputDriver = new ComposerInputDriver;
                $this->inputDriver->initialize($this);
                $this->inputDriver->save();
            }
        }
    }

    public function initializeForOutput($input, $denizensMap)
    {
        if ($input) {
            if (array_key_exists('driver', $input)) {
                // We're not at beginning
            }
            else {
                $this->outputDriver = new ComposerOutputDriver;
                $this->outputDriver->initialize($this, $denizensMap);
                $this->outputDriver->save();
            }
        }
    }

    protected function resolveFullSpecification ()
    {
        $spec = json_minify($this->specification);
        $spec = json_decode($spec, true);
        if (array_key_exists('baseSpecificationId', $spec)) {
            $nextComposer = Composer::find($spec['baseSpecificationId']);
            $tmpspec = $nextComposer->resolveFullSpecification($spec['baseSpecificationId']);
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

    public function getInputProperty ($propName) 
    {
        $value = null;
        $this->checkReady();
        if (array_key_exists($propName, $this->inputSpec) && $this->inputSpec[$propName]) {
            $value = $this->inputSpec[$propName];
        }
        return $value;
    }

    public function setReferentByReferentId ($id)
    {
        $this->referent = DenizenSet::find($id);
    }

    public function setReferentByDenizenId ($id) {
        $d = DAEntity\Denizen::find($id);
        $this->referent = new DenizenSet;
        $this->referent->initialize();
        $this->referent->addDenizen($d);
        $this->referent->save();
        return $this->referent->id;
    }

    public function setReferent ($d) 
    {
        $this->referent = new DenizenSet;
        $this->referent->initialize();
        $this->referent->addDenizen($d);
        $this->referent->save();
        return $this->referent->id;
    }

    public function getInputSpec()
    {
        $this->checkReady();
        return $this->inputSpec;
    }
    public function getOutputSpec()
    {
        $this->checkReady();
        return $this->outputSpec;
    }
    public function getElementsSpec()
    {
        $this->checkReady();
        return $this->elementsSpec;
    }
    public function getRelationsSpec()
    {
        $this->checkReady();
        return $this->relationsSpec;
    }

    public function getReferentId()
    {
        $refId = null;
        if ($this->referent) $refId = $this->referent->id;
        return $refId;
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

    public function getOutputDriver()
    {
        return $this->outputDriver;
    }

    public function getDriver()
    {
        return $this->inputDriver;
    }

    public function inputDone()
    {
        return $this->inputDone;
    }

    /*
     *  Graph Generation
     */
    
    public function generateGraph()
    {
        $graph = new \DemocracyApps\CNP\Graph\Graph;

        foreach ($this->elementsSpec as $element) {
            $graph->addNode($element['id'], null);
        }

        foreach ($this->relationsSpec as $relation) {
            $graph->addEdge($relation['from'], $relation['to'], $relation['type']);
            $graph->addEdge($relation['to'], $relation['from'], 
                            Relation::getInverseRelationName($relation['type']));
        }

        return $graph;
    }


    /*************************************************************************************
     *************************************************************************************
     **
     **
     **
     **
     **
     **   INPUT PROCESSING ROUTINES
     **
     **
     **
     **
     **
     *************************************************************************************
     *************************************************************************************/

    public function processInput($input, Composition $composition)
    {
        self::registerElementProcessors();
        if ($this->inputType == 'csv-simple') {
            self::processCsvInput($input, $composition);
        }
        else if ($this->inputType == 'auto-interactive') {
            $this->inputDriver->extractSubmittedValues($input); // Import latest batch of form data into inputDriver
            if ($this->inputDriver->inputDone()) {
                $this->inputDone = true;
                self::processAutoInput($input, $composition);
                $this->inputDriver->delete();
            }
        }
    }

    public static function tryit ($elementType, $content, $properties) 
    {
        dd("Hallelujah!");
    }

    private static function registerElementProcessors ()
    {
        //ElementGenerator::registerElementGenerator('Tag', 'DemocracyApps\CNP\Inputs\Composer::tryit');
    }

    private function commonProcessInput (Composition $composition, $data, $elementsSpec, $relationsSpec, $scape)
    {
        $denizens = array();

        $title = $data['title'];
        $summary = $data['summary'];
        $elementsIn = $data['elementsIn'];
        $anchorId = null;
        if (array_key_exists('anchor', $this->inputSpec)) {
            $anchorId = $this->inputSpec['anchor'];
        }
        else {
            $anchorId = $this->elementsSpec[0]['id'];
        }
        $topElement = null;
        // So now we create output denizens
        foreach ($elementsSpec as $espec) {
            $id = $espec['id'];
            if (array_key_exists($id, $elementsIn)) {
                $properties = null;
                if (array_key_exists('properties', $elementsIn[$id])) $properties = $elementsIn[$id]['properties'];
                $createdDenizens = DenizenGenerator::generateDenizen($espec['type'], $id, 
                                                                     $elementsIn[$id], $properties, $scape);
                if ($anchorId == $espec['id']) {
                    if (count($createdDenizens) > 1) throw new \Exception("Cannot set multiple denizens as anchors " . count($createdDenizens));
                    if ($createdDenizens) {
                        $createdDenizens[0]->name = $title;
                        $createdDenizens[0]->content = $summary;
                        $topElement = $createdDenizens[0];
                    }
                }
                if ($createdDenizens) $denizens[$id] = $createdDenizens;
            }
            else {
                if (array_key_exists('required', $espec) && $espec['required'] == true) {
                    return "Required element " . $id . " doesn't exist on datum ". $count;
                }
            }
        }

        foreach($denizens as $denizenList) {
            foreach ($denizenList as $denizen) {
                $denizen->save();
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
                    throw new \Exception("Composer processing - N X M relation generation not allowed");
                }
                foreach ($dfrom as $df) {
                    foreach ($dto as $dt) {
                        $relations = Relation::createRelationPair($df->id, 
                                                                  $dt->id,
                                                                  $relType,
                                                                  array('composerElements'
                                                                        => $from.','.$to),
                                                                  array('composerElements'
                                                                        => $to.','.$from)
                                                               );
                        foreach ($relations as $relation) {
                            $relation->setComposerId($this->id);
                            $relation->setCompositionId($composition->id);
                            $relation->save(); 
                        }                        
                    }
                }
            }
        }
        $haveit = ($this->referent != null);
        if ($this->referent) {
            $referents = $this->referent->getDenizens();
            $referentRelation = $this->inputSpec['referentRelation'];
            foreach($referents as $ref) {
                $relations = DAEntity\Relation::createRelationPair($ref->id, 
                                                                   $topElement->id, $referentRelation);
                foreach ($relations as $relation) { $relation->save(); }

            }
        }

    }

    private function processAutoInput($input, Composition $composition) {
        $map = $this->inputSpec['map'];
        $values = $this->inputDriver['runDriver']['map'];

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
                    $inputControllerClassName = '\DemocracyApps\CNP\Inputs\\'.$base."InputHandler";
                    $reflectionMethod = new \ReflectionMethod($inputControllerClassName, 'getValue');
                    $val = $reflectionMethod->invoke(null, $values[$id]);
                    if (array_key_exists('properties', $values[$id])) {
                        \Log::info("We've got some values! They are " . json_encode($values[$id]['properties']));
                        $properties = [];
                        foreach ($values[$id]['properties'] as $prop) {
                            $properties[$prop['name']] = $prop['value'];
                        }
                        $val['properties'] = $properties;
                    }
                    $use = $item['use'];
                    if ($use == 'title') {
                        $title = $val['value'];
                    }
                    elseif ($use == 'summary') {
                        $summary = $val['value'];
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
        $this->commonProcessInput($composition, $data, $this->elementsSpec, $this->relationsSpec, $this->scape);
    }

    private function processCsvInput($input, Composition $composition) 
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
            $title = " - ";
            $summary = null;
            if ($line) {
                ++$count;
                foreach ($columnMap as $column) {
                    $use = $column['use'];
                    if ($use == 'title') {
                        $title = $line[$column['column']];
                    }
                    elseif ($use == 'summary') {
                        $summary = $val;
                    }
                    else {
                        $val = array();
                        $val['isRef'] = false;
                        $val['value'] = $line[$column['column']];
                        $elementsIn[$column['elementId']] = $val;
                    }
                }
                $data = array();
                $data['title'] = $title;
                $data['summary'] = $summary;
                $data['elementsIn'] = $elementsIn;
                $childComposition = $composition->createChildComposition();
                $this->commonProcessInput($childComposition, $data, $this->elementsSpec, $this->relationsSpec, $this->scape);
            }
        }
    }

}
