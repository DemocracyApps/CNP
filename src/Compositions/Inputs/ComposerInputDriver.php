<?php
namespace DemocracyApps\CNP\Compositions\Inputs;

use \DemocracyApps\CNP\Compositions\Composer;
use \DemocracyApps\CNP\Compositions\ComposerProgram;

/**
 * This class is generated from an 'auto-interactive' input
 */
class ComposerInputDriver extends \Eloquent {
    protected $table = 'composer_input_drivers';
    protected $composer = null;

    protected $program = null;

    // public $runDriver = null;
    // private $done = false;
    // private $pageBreak = false;

    public function reInitialize(Composer $composer)
    {
        $this->composer = $composer;
        $this->program = new ComposerProgram;
        $this->program->restart($this->driver);
        // $this->runDriver = json_decode($this->driver, true);
        // $this->done = $this->runDriver['done'];
    }

    public function initialize(Composer $composer) 
    {
        $inputSpec = $composer->getInputSpec();
        $this->userid = \Auth::user()->getId();
        $this->expires = date('Y-m-d H:i:s', time() + 24 * 60 * 60);

        $this->program = new ComposerProgram;
        $this->program->compile($inputSpec);
/*
        $this->runDriver = array();
        $this->runDriver['start'] = $this->runDriver['current'] = null;
        $this->runDriver['done'] = false;
        $this->runDriver['expecting'] = null;
        $this->runDriver['map'] = array();
        $previous = null;

        $breakSequence = false;
        for ($i = 0, $size = count($inputSpec['map']); $i<$size; ++$i) {
            $item = $inputSpec['map'][$i];
            $item['prev'] = null;
            if (!array_key_exists('next', $item)) $item['next'] = null;
            $item['pagebreak'] = false;

            if (array_key_exists('id', $item)) {
                $id = $item['id'];
                // If this is the first real item, set start to it.
                if ($this->runDriver['start'] == null)   $this->runDriver['start']   = $id;

                if ($previous && ! $breakSequence) { // A sequence element breaks connection
                    if ( ! $previous['next'] ) {
                        $previous['next'] = $id; // don't override
                    }
                    $item['prev'] = $previous['id'];
                }
                $breakSequence = false;

                $this->runDriver['map'][$id] = $item;
                $previous = &$this->runDriver['map'][$id];

                $item['value'] = null;

                // If next item is contingent, set
                //    $item['pagebreak'] = true;
            }
            else {
                if ($item['use'] == 'pagebreak') {
                    if ($previous) $previous['pagebreak'] = true;
                }
                elseif ($item['use'] == 'break') {
                    if ($this->runDriver['start'] != null) { // We just ignore stop elements at the beginning.
                        $breakSequence = true; // This will suppress next/prev calculation above
                    }

                }
            }
        }
*/
    }

    public function inputDone ()
    {
        //return $this->done;
        return $this->program->executionDone();
    }

    public function cleanupAndSave()
    {
        // $this->driver = json_encode($this->runDriver);
        $this->driver = $this->program->getProgramState();
        $this->save();
    }

    public function extractSubmittedValues($input)
    {
        $expectedIds = $this->program->getExpectedIds();
        foreach ($expectedIds as $id) {
            $cElem = &$this->program->getExpectedCompositionElement($id);
            /*
             * We get a processor associated with the particular input type.
             */
            $base = ucfirst($cElem['inputType']);
            $inputControllerClassName = '\DemocracyApps\CNP\Compositions\Inputs\\'.$base."InputHandler";
            $reflectionMethod = new \ReflectionMethod($inputControllerClassName, 'extractValue');
            $reflectionMethod->invokeArgs(null, array($id, $input, &$cElem));
        }
    }

    public function getCompositionElementById($id)
    {
        return $this->program->getCompositionElementById($id);
    }

    public function getNextInput()
    {
        return $this->program->getNext();
    }
/*
    public function getNextInput()
    {
        $data = &$this->runDriver;

        if ($this->pageBreak) {
            return null; 
        }

        $current = $data['current'];
        if ($current == null) { // We are at the beginning
            $data['current'] = $current = $data['start'];
            $result = $data['map'][$current];
            $data['expecting'] = array($current);
        }
        else {
            $result = $data['map'][$current];
            if ($result['next']) {
                $data['current'] = $result['next'];
                $data['expecting'][] = $data['current'];
                $result = $data['map'][$result['next']];
            }
            else {
                $this->done = true;
                $data['done'] = true;
                $result = null;
            }
        }
        if ($result != null && $result['pagebreak']) $this->pageBreak = true;
        return $result;
    }
*/
}

