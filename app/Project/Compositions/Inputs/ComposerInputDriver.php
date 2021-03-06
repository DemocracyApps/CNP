<?php
namespace DemocracyApps\CNP\Project\Compositions\Inputs;

use \DemocracyApps\CNP\Project\Compositions\Composer;
use \DemocracyApps\CNP\Project\Compositions\ComposerProgram;
use \DemocracyApps\CNP\Utility\Html;
use \DemocracyApps\CNP\Graph\Element;

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
        $this->userid = \Auth::user()->id;
        $this->expires = date('Y-m-d H:i:s', time() + 24 * 60 * 60);

        $this->program = new ComposerProgram;
        $this->program->compile($inputSpec, true);
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
            if (array_key_exists('use', $cElem) && $cElem['use'] == 'output') continue;
            /*
             * We get a processor associated with the particular input type.
             */
            $base = ucfirst($cElem['inputType']);
            if ($base != "None") {
                $inputControllerClassName = '\DemocracyApps\CNP\Project\Compositions\Inputs\\' . $base . "InputHandler";
                $reflectionMethod = new \ReflectionMethod($inputControllerClassName, 'extractValue');
                $reflectionMethod->invokeArgs(null, array($id, $input, &$cElem));
            }
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

    static public function validForInput($next)
    {
        return array_key_exists('prompt', $next);
    }

    static public function createInput($desc)
    {

        if (array_key_exists('suppress', $desc) && $desc['suppress']=='input') return;
        if (array_key_exists('use', $desc) && $desc['use'] == 'output') return;
        if ($desc['inputType'] == "none") {
            Html::startElement("div", array('class' => 'form-group'));
            Html::createElement("p", $desc['prompt'], array());
        }
        else {
            Html::createElement('input', null, array('class' => 'form-control', 'id' => $desc['id'] . '_param', 'name' => $desc['id'] . "_param", 'type' => 'hidden'));
            Html::startElement("div", array('class' => 'form-group'));
            Html::createElement("label", $desc['prompt'], array('for' => $desc['id']));
        }
        if ($desc['inputType'] == 'text') {
            Html::createElement('input', null, array('class' => 'form-control', 'name' => $desc['id'], 'type'=>'text'));
        }
        elseif ($desc['inputType'] == 'textarea') {
            Html::createElement('textarea', null, array('class' => 'form-control', 'name' => $desc['id'],
                       'cols'=>'50', 'rows' => '10'));
        }
        elseif ($desc['inputType'] == 'person') {
            Html::createElement('input', null, 
                array( 'class' => 'form-control auto-person', 'name' => $desc['id'], 'type'=>'text'));
        }
        elseif ($desc['inputType'] == 'picture') {
            Html::createElement('input', null, array('name' => $desc['id'],
                       'type'=>'file'));
        }
        elseif ($desc['inputType'] == 'slider') {
            $min = "0.0";
            $max = "1.0";
            if (array_key_exists('scaleMin', $desc)) $min = $desc['scaleMin'];
            if (array_key_exists('scaleMax', $desc)) $max = $desc['scaleMax'];
            $sliderId = "slider-" . $desc['id'];
            $sliderDisplayId = $sliderId . "-display";
            $sliderControlId = $sliderId . "-control";
            $call = "sliderChange('" . $sliderId . "')";
            Html::createElement('input', null, array('name' => $desc['id'], 'type' => 'range', 'id'=>$sliderControlId,
                'min'=>$min, 'max'=>$max, 'step'=> ".5", 'style'=>'width:50%;', 'onchange' => $call));

            Html::startElement('div', array('style'=>'width:50%;'));
            Html::createElement('b', $min, array('style'=>"float:left;"));
            Html::createElement('b', $max, array('style'=>"float:right;"));
            Html::endElement('div');
            Html::createElement('div', '<b id="'.$sliderDisplayId.'">--</b>', array('style'=>'display:table; margin:0 auto; '));
        }
        elseif ($desc['inputType'] == 'pickElement') {
            $type = \CNP::getElementTypeId($desc['pickType']);
            $elements = Element::allUserElements(\Auth::user()->getId(), $type);
            Html::startElement('select', array('class' => 'form-control', 'name' => $desc['id']));
            foreach ($elements as $elem) {
                $display = $elem->content;
                $value = $elem->id;
                Html::createElement('option', $display, array('value'=>$value));
            }
            Html::endElement('select');
        }
        elseif ($desc['inputType'] == 'select') {
            Html::startElement('select', array('class' => 'form-control', 'name' => $desc['id']));
            $optionList = $desc['options'];
            foreach ($optionList as $opt) {
                $display = $opt['display'];
                $value = $opt['value'];
                Html::createElement('option', $display, array('value'=>$value));
            }
            Html::endElement('select');
        }
        elseif ($desc['inputType'] == 'multiselect') {
            Html::startElement('select', array('class' => 'form-control', 'name' => $desc['id'].'[]', 'multiple' => null));
            $optionList = $desc['options'];
            foreach ($optionList as $opt) {
                $display = $opt['display'];
                $value = $opt['value'];
                Html::createElement('option', $display, array('value'=>$value));
            }
            Html::endElement('select');
        }
        Html::endElement("div");
    }

}

