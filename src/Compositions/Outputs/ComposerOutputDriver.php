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

    protected $elementsMap = null;
    protected $layoutsLoaded = false;
    protected $layouts = null;

    public function reInitialize(Composer $composer, $input, $elementsMap)
    {
        $start = null;
        if (array_key_exists('start', $input)) $start = $input['start'];
        $this->outputSpec = $composer->getOutputSpec();
        if (! $this->outputSpec) {
            $this->outputSpec = $composer->getInputSpec();
            $this->usingInputSpec = true;
        }
        $this->program = new ComposerProgram;
        $this->program->restart($this->driver, $start);
        $this->elementsMap = $elementsMap;
    }

    public function initialize(Composer $composer, $input, $elementsMap) 
    {
        $this->composer = $composer;
        $this->userid = \Auth::user()->getId();
        $this->expires = date('Y-m-d H:i:s', time() + 24 * 60 * 60);
        $start = null;
        if (array_key_exists('start', $input)) $start = $input['start'];

        $this->outputSpec = $composer->getOutputSpec();
        if (! $this->outputSpec) {
            $this->outputSpec = $composer->getInputSpec();
            $this->usingInputSpec = true;
        }
        $this->program = new ComposerProgram;
        $this->program->compile($this->outputSpec, $start);
        $this->elementsMap = $elementsMap;
    }

    public function usingInputForOutput() 
    {
        return $this->usingInputSpec;
    }

    public function getElements ($id) {
        return (array_key_exists($id, $this->elementsMap))?$this->elementsMap[$id]:array();
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
        return (array_key_exists('prompt', $next) || array_key_exists('column', $next));
    }

    static public function createInputDrivenOutput($anchor, $driver, $desc)
    {
        Html::startElement("div", array('class' => 'span6'));
        $prompt = "Unknown prompt";
        if (array_key_exists('outputPrompt', $desc)) {
            $prompt = $desc['outputPrompt'];
        }
        else if (array_key_exists('prompt', $desc)) {
            $prompt = $desc['prompt'];
        }
        else if (array_key_exists('column', $desc)) {
            $prompt = 'Column ' . $desc['column'];
        }
        Html::createElement("h3", $prompt, array('id' => $desc['id']));

        if ($desc['use'] == 'title') {
            Html::createElement('p', $anchor->getName(), array('id' => $anchor->id));
        }
        else if ($desc['use'] == 'summary') {
            Html::createElement('p', $anchor->getContent(), array('id' => $anchor->id));
        }
        else if ($desc['use'] == 'element') {
            $elements = $driver->getElements($desc['elementId']);
            foreach($elements as $den) {
                Html::createElement('p', $den->getContent(), array('id'=>$den->id, 'class' => 'span6'));
                Html::createSelfClosingElement('br');
            }
        }
        else {
            Html::createElement('p', "Still TBD", array('class'=>'whoknows'));
        }
        Html::endElement("div");
    }
    
    private function loadLayouts()
    {
        \Log::info("Loading layouts");
        $fileName = base_path()."/src/Compositions/Outputs/layouts.json";
        $str = file_get_contents($fileName);
        $str = json_minify($str);
        $this->layouts = json_decode($str, true);
        $this->layouts = $this->layouts['layouts'];
        $this->layoutsLoaded = true;
    }

    private function validForLayoutOutput($item)
    {
        return array_key_exists('location', $item);
    }
    
    public function getOutputContent($topElement, $composition)
    {
        \Log::info("a1");
        $defaultLayout = 'single';
        if (array_key_exists('defaultLayout', $this->outputSpec)) 
            $defaultLayout = $this->outputSpec['defaultLayout'];
        if (! $this->layoutsLoaded ) $this->loadLayouts();

        if (! array_key_exists($defaultLayout, $this->layouts)) {
            throw new \Exception ("Unknown output layout " . $defaultLayout);
        }
        \Log::info("a2");
        $currentLayout = $this->layouts[$defaultLayout];
        // Let's get all the elements that go on to this page
        $targeted = array();
        $done = false;
        while (! $done ) {
            $next = $this->getNext();
            if (! $next) {
                $done = true;
            }
            else {
                if ($next['use'] == 'pageinfo') {
                    if (array_key_exists('layout', $next)) {
                        if (! array_key_exists($next['layout'], $this->layouts)) {
                            throw new \Exception ("Unknown output layout " . $next['layout']);
                        }
                        $currentLayout = $this->layouts[$next['layout']];
                    }
                }
                if (self::validForLayoutOutput($next)) {
                    $location = $next['location'];
                    if ( ! array_key_exists($location, $targeted)) {
                        $targeted[$location] = array();
                    }
                    $targeted[$location][] = $next;
                }
            }
        }
        \Log::info("a3");
        // Now output this page according to the layout
        $content = $this->runLayout($composition, $currentLayout, $targeted, $topElement, 10);
        \Log::info("a4");
        $this->cleanupAndSave();
    }


    private function runLayout ($composition, $layout, $targeted, $topElement, $spaces = 5) 
    {
        $sections = $layout['content'];
        /*
         */
        foreach ($sections as $section) {
            $props = array();
            if (array_key_exists('class', $section)) {
                $props['class'] = $section['class'];
            }
            Html::startElement($section['type'], $props, $spaces); // Main div for the section
            $spaces += 5;
            // There should be either content or a target
            if (array_key_exists('content', $section)) {
                $this->runLayout($composition, $section, $targeted, $topElement, $spaces);
            }
            elseif (array_key_exists('target', $section)) {
                $target = $section['target'];
                if (array_key_exists($target, $targeted)) {
                    $list = $targeted[$target]; // List of output elements
                    $title = null;
                    // Let's pull out any special ones, like the title of this section
                    $tmpList = $list;
                    $list = array();
                    foreach ($tmpList as $item) {
                        if ($item['use'] == 'title') $title = $item;
                        else if ($item['use'] == 'summary') $summary = $item;
                        else {
                            $list[] = $item;
                        }
                    }
                    if ($title) {
                        $content = null;
                        $source = explode('.', $title['source']);
                        if ($source[0] == 'element') {
                            $den = $this->getElements($title['elementId']);
                            if ($den) {
                                if (sizeof($source) == 2) {
                                    if ($source[1] == 'name') {
                                        $content = $den[0]->getName();
                                    }
                                    else if ($source[1] == 'content') {
                                        $content = $den[0]->getContent();
                                    }
                                    else {
                                        $content = $den[0]->getContent();
                                    }
                                }                                
                            }
                        }
                        else if ($source[0] == 'this') {
                            $content = $title['content'];
                        }
                        if ($content) {
                            Html::createElement('h2', $content, array(), $spaces);
                        }
                    }
                    foreach ($list as $item) {
                        self::createOutput($composition, $topElement, $item, $spaces);
                    }
                }
            }
            echo "\n";
            $spaces -= 5;
            Html::endElement($section['type'], $spaces);
            Html::createSelfClosingElement('br', $spaces);
        }
    }

    public function createOutput($composition, $anchor, $item, $spaces = 5)
    {
        Html::startElement("div", array('class' => 'xxx'), $spaces);
        $spaces += 5;

        if (array_key_exists('header', $item)) {
            Html::createElement("h3", $item['header'], array('id' => $item['id']), $spaces);
        }

        if ($item['use'] == 'title') {
            Html::createElement('p', $anchor->getName(), array('id' => $anchor->id), $spaces);
        }
        else if ($item['use'] == 'element') {
            $elements = $this->getElements($item['elementId']);
            foreach($elements as $den) {
                Html::createElement('p', $den->getContent(), array('id'=>$den->id, 'class' => 'yyy'), $spaces);
            }
        }
        elseif ($item['use'] == 'link') {
            $link = "compositions/".$composition->id."?composer=".$this->composer->id;
            $link .= "&start=" . $item['link'];
            $link = link_to($link, $item['text'], array()); 
            Html::createElement('p', $link, array(), $spaces);
        }
        else {
            Html::createElement('p', "<b>ComposerOutputDriver.createOutput: Unknown use ".$item['use']."</b>", array('class'=>'zzz'), $spaces);
        }

        $spaces -= 5;
        Html::endElement("div", $spaces);
    }
}
