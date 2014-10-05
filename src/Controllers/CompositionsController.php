<?php namespace DemocracyApps\CNP\Controllers;

use \DemocracyApps\CNP\Compositions\Composer;
use \DemocracyApps\CNP\Compositions\Composition;
use \DemocracyApps\CNP\Entities\Element;

class CompositionsController extends ApiController {

    public function show($id)
    {
        $composition = Composition::find($id);
        $composer = Composer::find($composition->input_composer_id);
        if ($composer->output) {
            $ctmp = Composer::find($composer->output);
            if ($ctmp) $composer = $ctmp;
        }

        $topElement = Element::find($composition->top);
        // Get all the elements associated with this composition.
        // We get back a hash by Composer element ID.
        $elements = array();
        Element::getCompositionElements($composition->id, $elements);

        $composer->initializeForOutput(\Input::all(), $elements);
        if ( ! $composer->getDriver()->done()) {
            if (! $composer->getDriver()->usingInputForOutput()) {
                return \View::make('compositions.layoutdriven', array('composer' => $composer,
                                                                      'topElement' => $topElement,
                                                                      'composition' => $composition));
            }
            else {
                return \View::make('compositions.show', array('composer' => $composer,
                                                                      'topElement' => $topElement,
                                                                      'composition' => $composition));
            }
        }
        else {
          return \Redirect::to('/stories?project='.$composer->project);
        }
    }
}
