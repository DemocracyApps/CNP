<?php
namespace DemocracyApps\CNP\Compositions;
use \DemocracyApps\CNP\Entities as DAEntity;
use \DemocracyApps\CNP\Graph\ElementSet;
use \DemocracyApps\CNP\Entities\Relation;
use \DemocracyApps\CNP\Compositions\Outputs\ComposerOutputDriver;
use \DemocracyApps\CNP\Compositions\Inputs\ComposerInputDriver;
use \DemocracyApps\CNP\Compositions\Inputs\ElementGenerator;

class Composer extends \Eloquent {
    protected $fullSpecification = null;

    //
    // A story is commonly about something or is an extension of something
    // and so a relationship should be created when the story is created.
    // 
    // Because a story may refer to more than one elements, the referent is
    // a DemocracyApps\CNP\Graph\ElementSet.
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

    protected function getAnchorId()
    {
        $anchorId = null;
        if ($this->elementsSpec) {
            foreach ($this->elementsSpec as $e) {
                if ($e['type'] == 'CnpComposition') {
                    $anchorId = $e['id'];
                    break;
                }
            }
        }
        return $anchorId;
    }

    public static function getUserComposers ($userId)
    {
        $records = \DB::table(self::$tableName)
                    ->join('elements', 'composers.project', '=', 'elements.id')
                    ->where('elements.userid', '=', $userId)
                    ->select('composers.id', 'composers.name', 'composers.project', 'composers.description',
                             'composers.dependson', 'composers.contains', 'elements.userid')
                    ->orderBy('composers.project', 'composers.id')
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
        $instance->{'project'} = $data->project;
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

    public function initializeForOutput($input, $elementsMap)
    {
        $this->checkReady();
        $this->doingInput = false;
        if (isset($input)) {
            if (array_key_exists('driver', $input)) {
                $driverId = \Input::get('driver');
                $this->outputDriver = ComposerOutputDriver::find($driverId);
                if ( ! $this->outputDriver) {
                    dd("No damn driver " . $driverId);
                }
                $this->outputDriver->reInitialize($this, $input, $elementsMap);
            }
            else {
                $this->outputDriver = new ComposerOutputDriver;
                $this->outputDriver->initialize($this, $input, $elementsMap);
                $this->outputDriver->save();
            }
        }
    }

    protected function resolveFullSpecification ()
    {
        $spec = json_minify($this->specification);
        $spec = json_decode($spec, true);
        if (array_key_exists('baseSpecificationId', $spec)) {
            $nextComposer = Composer::findOrFail($spec['baseSpecificationId']);
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

    public function setReferentByReferentId ($id, $relation)
    {
        $this->referent = ElementSet::find($id);
        $this->referentRelation = $relation;
    }

    public function setReferentByElementId ($id, $relation) {
        $d = DAEntity\Element::find($id);
        $this->referent = new ElementSet;
        $this->referent->initialize();
        $this->referent->addElement($d);
        $this->referent->save();
        $this->referentRelation = $relation;
        return $this->referent->id;
    }

    public function setReferent ($d) 
    {
        $this->referent = new ElementSet;
        $this->referent->initialize();
        $this->referent->addElement($d);
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

    public function getReferentRelation()
    {
        return $this->referentRelation;
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
            $file->move('/var/www/cnp/public/downloads', $name);
            $data['filePath'] = '/var/www/cnp/public/downloads/' . $name;
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

    private function commonProcessInput (Composition $composition, $data, $elementsSpec, $relationsSpec, $project)
    {
        $elements = array();

        $elementsIn = $data['elementsIn'];
        $relationsIn = $data['relationsIn'];

        $anchorId = $this->getAnchorId(); // This is the 'Composition' element
        if (!$anchorId) throw new Exception("Spec lacks anchor id");
        $topElement = null;
        // So now we create output elements
        foreach ($elementsSpec as $espec) {
            $id = $espec['id'];
            if (array_key_exists($id, $elementsIn)) {
                $properties = null;
                if (array_key_exists('properties', $elementsIn[$id])) $properties = $elementsIn[$id]['properties'];
                $createdElements = ElementGenerator::generateElement($espec, $id, 
                                                                     $elementsIn[$id], $properties, $project);
                if ($anchorId == $espec['id']) {
                    if (count($createdElements) > 1) throw new \Exception("Cannot set multiple elements as anchors " . count($createdElements));
                    if ($createdElements) {
                        $topElement = $createdElements[0];
                    }
                }
                if ($createdElements) $elements[$id] = $createdElements;
            }
            else {
                if (array_key_exists('required', $espec) && $espec['required'] == true) {
                    dd("Required element " . $id . " doesn't exist");
                }
            }
        }
        foreach($elements as $eid => $elementList) {
            foreach ($elementList as $element) {
                $element->save();
                if ($element != $topElement) {
                    $relations = Relation::createRelationPair($topElement->id, 
                                                              $element->id,
                                                              'has-part',
                                                              $project,
                                                              array('composerElements'
                                                                    => $anchorId.','.$eid),
                                                              array('composerElements'
                                                                    => $eid.','.$anchorId)
                                                           );
                    foreach ($relations as $relation) {
                        $relation->setCompositionId($composition->id);
                        $relation->save(); 
                    }                        
                }
            }
        }

        foreach ($relationsSpec as $relation) {
            $from = $relation['from'];
            $to   = $relation['to'];
            $relType = $relation['type'];
            if (array_key_exists($from, $elements) && array_key_exists($to,$elements)) {
                $dfrom = $elements[$from];
                $dto   = $elements[$to];
                // One or the other may expand to multiple elements (e.g., tags), but let's not
                // let things get out of hand.
                if (count($dfrom) > 1 && count($dto) > 1) {
                    throw new \Exception("Composer processing - N X M relation generation not allowed");
                }
                foreach ($dfrom as $df) {
                    foreach ($dto as $dt) {
                        $relations = Relation::createRelationPair($df->id, 
                                                                  $dt->id,
                                                                  $relType,
                                                                  $project,
                                                                  array('composerElements'
                                                                        => $from.','.$to),
                                                                  array('composerElements'
                                                                        => $to.','.$from)
                                                               );
                        foreach ($relations as $relation) {
                            $relation->setCompositionId($composition->id);
                            $relation->save(); 
                        }                        
                    }
                }
            }
        }
        foreach ($relationsIn as $relation) {
            $from = $relation['from'];
            $to   = $relation['to'];
            $relType = $relation['relation']['value'];
            if (array_key_exists($from, $elements) && array_key_exists($to,$elements)) {
                $dfrom = $elements[$from];
                $dto   = $elements[$to];
                // One or the other may expand to multiple elements (e.g., tags), but let's not
                // let things get out of hand.
                if (count($dfrom) > 1 && count($dto) > 1) {
                    throw new \Exception("Composer processing - N X M relation generation not allowed");
                }
                foreach ($dfrom as $df) {
                    foreach ($dto as $dt) {
                        $relations = Relation::createRelationPair($df->id, 
                                                                  $dt->id,
                                                                  $relType,
                                                                  $project,
                                                                  array('composerElements'
                                                                        => $from.','.$to),
                                                                  array('composerElements'
                                                                        => $to.','.$from)
                                                               );
                        foreach ($relations as $relation) {
                            $relation->setCompositionId($composition->id);
                            $relation->save(); 
                        }                        
                    }
                }
            }            
        }
        $haveit = ($this->referent != null);
        if ($this->referent) {
            $referents = $this->referent->getElements();
            foreach($referents as $ref) {
                $relations = DAEntity\Relation::createRelationPair($ref->id, 
                                                                   $topElement->id, $this->referentRelation, $project);
                foreach ($relations as $relation) { $relation->save(); }

            }
        }
        if (array_key_exists('compositionTitle', $data)) {
            $composition->title = $data['compositionTitle'];
        }
        else {
            $composition->title = $topElement->content;
        }
        $composition->top = $topElement->id;
        $composition->save();

    }

    private function processAutoInput($input, Composition $composition) {
        $map = $this->inputSpec['map'];
        $values = $this->inputDriver['runDriver']['map'];

        if (! $map) return "No map!";
        $data = array();

        $elementsIn = array();
        $relationsIn = array();
        foreach ($map as $item) {
            // If no id, then it wasn't used to get input (e.g., page breaks).
            if (array_key_exists('use', $item) && array_key_exists('id', $item)) {
                $id = $item['id'];
                $isRef = false;
                $value = null;
                if (array_key_exists('inputType', $item) && $item['inputType'] == 'auto') {
                    if (array_key_exists('inputValue', $item)) {
                        if ($item['inputValue'] == '!user') {
                            $user = \Auth::user();
                            $isRef = true;
                            $refId = $user->elementid;
                        }
                    }
                }
                else {
                    $value = $this->inputDriver->getCompositionElementById($id);
                }

                if ($value) {
                    $base = ucfirst($value['inputType']);
                    $inputControllerClassName = '\DemocracyApps\CNP\Compositions\Inputs\\'.$base."InputHandler";
                    $reflectionMethod = new \ReflectionMethod($inputControllerClassName, 'getValue');
                    $val = $reflectionMethod->invoke(null, $value);
                    if ($val['value']) {
                        if (array_key_exists('properties', $value)) {
                            $properties = [];
                            foreach ($value['properties'] as $prop) {
                                $properties[$prop['name']] = $prop['value'];
                            }
                            $val['properties'] = $properties;
                        }
                        if ($item['use'] == 'element') {
                            $elementId = $item['elementId'];
                            $elementsIn[$elementId] = $val;
                        }
                        else if ($item['use'] == 'relation' && $val['value']) {
                            $rel = array();
                            $rel['from'] = $item['from'];
                            $rel['to'] = $item['to'];
                            $rel['relation'] = $val;
                            $relationsIn[] = $rel;
                        }
                        else if ($item['use'] == 'compositionTitle' && $val['value']) {
                            $data['compositionTitle'] = $val['value'];
                        }
                    }
                }
                else if ($isRef) {
                    $elementId = $item['elementId'];
                    $val = array();
                    $val['isRef'] = true;
                    $val['id'] = $refId;
                    $elementsIn[$elementId] = $val;
                }
            }
        }

        $data['elementsIn'] = $elementsIn;
        $data['relationsIn'] = $relationsIn;
        $this->commonProcessInput($composition, $data, $this->elementsSpec, $this->relationsSpec, $this->project);
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
            $relationsIn = array();
            $title = " - ";
            $summary = null;
            if ($line) {
                ++$count;
                $valid = true;
                foreach ($columnMap as $column) {
                    $use = $column['use'];
                    $required =  false;
                    if (array_key_exists('required', $column)) {
                        $required = $column['required'];
                    }

                    $val = array();
                    $val['isRef'] = false;
                    $val['value'] = $line[$column['column']];
                    if ($required && ! $val['value']) {
                        if (! $lmessage) $lmessage = "Line " . $count . ": ";
                        else $lmessage .= ", ";
                        $lmessage .= "missing value in column " . $column['column'];
                        $valid = false;
                    }
                    if ($use == 'element') {
                        $elementsIn[$column['elementId']] = $val;
                    }
                    else if ($use == 'relation' && $val['value']) {
                        $relationMap = $column['relationMap'];
                        if (array_key_exists($val['value'], $relationMap)) {
                            $val['value'] = $relationMap[$val['value']];
                            $rel = array();
                            $rel['from'] = $column['from'];
                            $rel['to'] = $column['to'];
                            $rel['relation'] = $val;

                            $relationsIn[] = $rel;
                        }
                    }
                }
                if ($valid) {
                    $data = array();
                    $data['elementsIn'] = $elementsIn;
                    $data['relationsIn'] = $relationsIn;
                    $childComposition = $composition->createChildComposition($title);
                    $this->commonProcessInput($childComposition, $data, $this->elementsSpec, $this->relationsSpec, $this->project);
                }
                if ($lmessage) $messages .= $lmessage . "\n";
            }
        }
        return $messages;
    }

}
