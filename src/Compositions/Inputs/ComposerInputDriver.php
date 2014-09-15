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

    public function reInitialize(Composer $composer)
    {
        $this->composer = $composer;
        $this->program = new ComposerProgram;
        $this->program->restart($this->driver);
    }

    public function initialize(Composer $composer) 
    {
        $inputSpec = $composer->getInputSpec();
        $this->userid = \Auth::user()->getId();
        $this->expires = date('Y-m-d H:i:s', time() + 24 * 60 * 60);

        $this->program = new ComposerProgram;
        $this->program->compile($inputSpec);
    }

    public function done ()
    {
        return $this->program->executionDone();
    }

    public function cleanupAndSave()
    {
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

    public function getNext()
    {
        return $this->program->getNext();
    }
}

