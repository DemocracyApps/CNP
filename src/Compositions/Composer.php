<?php
namespace DemocracyApps\CNP\Compositions;
use \DemocracyApps\CNP\Entities as DAEntity;
use \DemocracyApps\CNP\Graph\DenizenSet;
use \DemocracyApps\CNP\Entities\Relation;
use \DemocracyApps\CNP\Compositions\Outputs\ComposerOutputDriver;
use \DemocracyApps\CNP\Compositions\Inputs\ComposerInputDriver;
use \DemocracyApps\CNP\Compositions\Inputs\DenizenGenerator;

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
    protected $outputDriver = null;

    protected $doingInput = true;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
    protected $table = 'composers';
    protected static $tableName = 'composers';

    public static function getUserComposers ($userId)
    {
        $records = \DB::table(self::$tableName)
                    ->join('denizens', 'composers.scape', '=', 'denizens.id')
                    ->where('denizens.userid', '=', $userId)
                    ->select('composers.id', 'composers.name', 'composers.scape', 'composers.description',
                             'composers.dependson', 'composers.contains', 'denizens.userid')
                    ->orderBy('composers.scape', 'composers.id')
                    ->distinct()
                    ->get();
        $result = array();
        foreach($records as $record)
        {
            $item = new static();
            self::fillData($item, $record);
            $result[] = $item;
        }
        return $result;
    }

    protected static function fillData($instance, $data)
    {
        $instance->{'id'} = $data->id;
        $instance->{'name'} = $data->name;
        $instance->{'scape'} = $data->scape;
        if (property_exists($data, 'description')) {
            $instance->{'description'} = $data->description;
        }

        if (property_exists($data, 'dependson')) {
            $instance->{'dependson'} = $data->dependson;
        }
        if (property_exists($data, 'contains')) {
            $instance->{'contains'} = $data->contains;
        }
    }
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

    public function initializeForInput($input)
    {
        $this->checkReady();
        $this->doingInput = true;
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
        $this->checkReady();
        $this->doingInput = false;
        if ($input) {
            if (array_key_exists('driver', $input)) {
                $driverId = \Input::get('driver');
                $this->outputDriver = ComposerOutputDriver::find($driverId);
                if ( ! $this->outputDriver) {
                    dd("No damn driver " . $driverId);
                }
                $this->outputDriver->reInitialize($this, $input, $denizensMap);
            }
            else {
                $this->outputDriver = new ComposerOutputDriver;
                $this->outputDriver->initialize($this, $input, $denizensMap);
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

    public function getDriver()
    {
        if ($this->doingInput)
            return $this->inputDriver;
        else
            return $this->outputDriver;
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
            $file = \Input::file('csv');
            $data = array();
            $data['userId'] = \Auth::user()->getId();
            $data['composerId'] = $this->id;
            $data['compositionId'] = $composition->id;
            $name = uniqid('upload');
            \Log::info("Attempt to create /vagrant/cnp/public/downloads" . $name);
            $file->move('/vagrant/cnp/public/downloads', $name);
            $data['filePath'] = '/vagrant/cnp/public/downloads/' . $name;
            $notification = new \DemocracyApps\CNP\Utility\Notification;
            $notification->user_id = $data['userId'];
            $notification->status = 'Running';
            $notification->type = 'CVSUpload';
            $notification->save();
            $data['notificationId'] = $notification->id;
            \Queue::push('\DemocracyApps\CNP\Compositions\Inputs\CSVInputProcessor', $data);
        }
        else if ($this->inputType == 'auto-interactive') {
            $this->inputDriver->extractSubmittedValues($input); // Import latest batch of form data into inputDriver
            if ($this->inputDriver->done()) {
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
                        if ($title) $createdDenizens[0]->name = $title;
                        if ($summary) $createdDenizens[0]->content = $summary;
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
                $value = $this->inputDriver->getCompositionElementById($id);

                //if (array_key_exists($id, $values)) {
                if ($value) {
                    $base = ucfirst($value['inputType']);
                    $inputControllerClassName = '\DemocracyApps\CNP\Compositions\Inputs\\'.$base."InputHandler";
                    $reflectionMethod = new \ReflectionMethod($inputControllerClassName, 'getValue');
                    $val = $reflectionMethod->invoke(null, $value);
                    if (array_key_exists('properties', $value)) {
                        $properties = [];
                        foreach ($value['properties'] as $prop) {
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

    public function processCsvInput($filePath, Composition $composition) 
    {
        ini_set("auto_detect_line_endings", true); // Deal with Mac line endings
        $messages = "";

        // $file = \Input::file('csv');
        // $myfile = fopen($file->getRealPath(), "r") or die("Unable to open file!");
        $myfile = fopen($filePath, "r") or die("Unable to open file!");
        if (! file_exists($filePath)) {
            \Log::info("The file doesn't exist");
        }

        $map = $this->inputSpec['map'];
        if (! $map) throw new \Exception ("No map found");
        $skip = $map['skip'];
        $columnMap = $map['columnMap'];

        while ($skip-- > 0) {
            $line = fgetcsv($myfile);
        }
        $count = $skip;
        while ( ! feof($myfile) ) {
            $line = fgetcsv($myfile);
            $lmessage = null;
            $elementsIn = array();
            $title = " - ";
            $summary = null;
            if ($line) {
                ++$count;
                if ($count%100 == 0) {
                    \Log::info(" ... " . $count);
                }
                $valid = true;
                foreach ($columnMap as $column) {
                    $use = $column['use'];
                    $required =  false;
                    if (array_key_exists('required', $column)) {
                        $required = $column['required'];
                    }

                    if ($use == 'title') {
                        $title = $line[$column['column']];
                        if ($required && ! $title) {
                            if (! $lmessage) $lmessage = "Line " . $count . ":";
                            $lmessage .= " missing title";
                            $valid = false;
                        }
                    }
                    elseif ($use == 'summary') {
                        $summary = $val;
                    }
                    else {
                        $val = array();
                        $val['isRef'] = false;
                        $val['value'] = $line[$column['column']];
                        if ($required && ! $val['value']) {
                            if (! $lmessage) $lmessage = "Line " . $count . ": ";
                            else $lmessage .= ", ";
                            $lmessage .= "missing value in column " . $column['column'];
                            $valid = false;
                        }
                        $elementsIn[$column['elementId']] = $val;
                    }
                }
                if ($valid) {
                    $data = array();
                    $data['title'] = $title;
                    $data['summary'] = $summary;
                    $data['elementsIn'] = $elementsIn;
                    $childComposition = $composition->createChildComposition();
                    $this->commonProcessInput($childComposition, $data, $this->elementsSpec, $this->relationsSpec, $this->scape);
                }
                if ($lmessage) $messages .= $lmessage . "\n";
            }
        }
        return $messages;
    }

}
