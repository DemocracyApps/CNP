<?php
namespace DemocracyApps\CNP\Controllers;

use \DemocracyApps\CNP\Entities as DAEntity;
use \DemocracyApps\CNP\Compositions\Composer;
use \DemocracyApps\CNP\Compositions\Outputs\Vista;

class DenizensController extends ApiController {
    protected $denizen;

    public function __construct(DAEntity\Story $denizen)
    {
        $this->denizen = $denizen;
    }

    public function show($id)
    {
        $denizen = DAEntity\Denizen::find($id);

        // We have to have a composer for this. Hoo boy.
        $composer = \Input::get('composer');
        $composer = Composer::find($composer);
        $vista = \Input::get('vista');
        $vista = Vista::find($vista);
        $compositions = $vista->extractCompositions($denizen);
        $denizens = array();
        foreach ($compositions as $id => $count) {
            DAEntity\Denizen::getCompositionDenizens($id, $denizens);
        }
        /*
         * Ok, so now we have a way to look up instances of any element ID. Now I guess we look for 
         * the output spec.
         */
        $composer->initializeForOutput(\Input::all(), $denizens);
        
        if ( ! $composer->getDriver()->done()) {
          return \View::make('compositions.show', array('composer' => $composer, 'topDenizen' => $denizen,
                                                        'vista' => $vista->id));
        }
        else {
          return \Redirect::to('/vistas?vista='.$vista->id);
        }
/*
        $graph = $composer ->generateGraph();
        $roles = $vista->extractComposerRoles($denizen);
        $count = 0;
        foreach ($roles as $role => $count) {
            if ($graph->assignPayload($role, $denizen, $denizen->id, 'denizenId')) ++$count;
        }
        if ($count <= 0) {
            throw new Exception ("Unable to match denizen to Composer role");
        }

        $graph->propagatePayloads($denizen->id, 'denizenId');
        dd($graph);

        return \View::make('stories.show', array('story' => $denizen, 'elements' => $elements,
                                                 'relations' => $elementRelations));
 */
    }

}


