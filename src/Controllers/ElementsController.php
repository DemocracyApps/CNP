<?php namespace DemocracyApps\CNP\Controllers;

use \DemocracyApps\CNP\Entities as DAEntity;
use \DemocracyApps\CNP\Compositions\Composer;
use \DemocracyApps\CNP\Compositions\Outputs\Vista;

class ElementsController extends ApiController {
    protected $element;

    public function __construct(DAEntity\Story $element)
    {
        $this->element = $element;
    }

    public function show($id)
    {
        $element = DAEntity\Element::find($id);
        // We have to have a composer for this. Hoo boy.
        $composer = \Input::get('composer');
        $composer = Composer::find($composer);
        $vista = \Input::get('vista');
        $vista = Vista::find($vista);
        $compositions = $vista->extractCompositions($element);
        $elements = array();
        foreach ($compositions as $id => $count) {
            DAEntity\Element::getCompositionElements($id, $elements);
        }

        /*
         * Ok, so now we have a way to look up instances of any element ID. Now I guess we look for 
         * the output spec.
         */
        $composer->initializeForOutput(\Input::all(), $elements);
        
        if ( ! $composer->getDriver()->done()) {
            if (! $composer->getDriver()->usingInputForOutput()) {
                return \View::make('compositions.layoutdriven', array('composer' => $composer,
                                                                      'topElement' => $element,
                                                                      'vista' => $vista));
            }
            else {
                return \View::make('compositions.show', array('composer' => $composer,
                                                                      'topElement' => $element,
                                                                      'vista' => $vista->id));
            }
        }
        else {
          return \Redirect::to('/stories?vista='.$vista->id);
        }
/*
        $graph = $composer ->generateGraph();
        $roles = $vista->extractComposerRoles($element);
        $count = 0;
        foreach ($roles as $role => $count) {
            if ($graph->assignPayload($role, $element, $element->id, 'elementId')) ++$count;
        }
        if ($count <= 0) {
            throw new Exception ("Unable to match element to Composer role");
        }

        $graph->propagatePayloads($element->id, 'elementId');
        dd($graph);

        return \View::make('stories.show', array('story' => $element, 'elements' => $elements,
                                                 'relations' => $elementRelations));
 */
    }

}


