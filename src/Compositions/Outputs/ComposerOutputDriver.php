<?php
namespace DemocracyApps\CNP\Compositions\Outputs;

use \DemocracyApps\CNP\Compositions\Composer;
use \DemocracyApps\CNP\Compositions\ComposerProgram;
use \DemocracyApps\CNP\Utility\Html;

class ComposerOutputDriver extends \Eloquent {
    protected $table = 'composer_output_drivers';
    protected $composer = null;
    protected $outputSpec = null;
    protected $inputSpec = null;

    private $usingInputSpec = false;

    protected $program = null;

    protected $denizensMap = null;

    public function reInitialize(Composer $composer)
    {
        $this->program = new ComposerProgram;
        $this->program->restart($this->driver);
    }
    public function initialize(Composer $composer, $denizensMap) 
    {
        $this->composer = $composer;
        $this->userid = \Auth::user()->getId();
        $this->expires = date('Y-m-d H:i:s', time() + 24 * 60 * 60);

        $this->outputSpec = $composer->getOutputSpec();
        if (! $this->outputSpec) {
            $this->outputSpec = $composer->getInputSpec();
            $usingInputSpec = true;
        }
        $this->program = new ComposerProgram;
        $this->program->compile($this->outputSpec);
        $this->denizensMap = $denizensMap;
    }

    public function getDenizens ($id) {
        return (array_key_exists($id, $this->denizensMap))?$this->denizensMap[$id]:array();
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

    public function getNext()
    {
        return $this->program->getNext();
    }

    public static function validForOutput($next)
    {
        return array_key_exists('prompt', $next);
    }

    static public function createOutput($anchor, $driver, $desc)
    {
        Html::startElement("div", array('class' => 'span6'));
        $prompt = (array_key_exists('outputPrompt', $desc))?$desc['outputPrompt']:$desc['prompt'];
        Html::createElement("h3", $prompt, array('id' => $desc['id']));

        if ($desc['use'] == 'title') {
            Html::createElement('p', $anchor->getName(), array('id' => $anchor->id));
        }
        else if ($desc['use'] == 'summary') {
            Html::createElement('p', $anchor->getContent(), array('id' => $anchor->id));
        }
        else if ($desc['use'] == 'element') {
            $denizens = $driver->getDenizens($desc['elementId']);
            foreach($denizens as $den) {
                Html::createElement('p', $den->getContent(), array('id'=>$den->id, 'class' => 'span6'));
                Html::createSelfClosingElement('br');
            }
        }
        else {
            Html::createElement('p', "Still TBD", array('class'=>'whoknows'));
        }
        Html::endElement("div");
    }

}