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
        $composer->initialize(null);
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

        dd($composer->getOutputDriver());
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

        /*
         *  So maybe read all the properties and use them to label the denizens?
         *  The question is - how far along the relations do we go?
         *
         *  So I think it's critical to know the base spec (elements + relations). Probably
         *  a good idea to also know the input, but not critical ... is it? In any case,
         *  I think we need to record the input spec id? or the base spec id? probably not
         *  base because we need to know that actual resolved version of everything.
         *
         *  Then we could go thru things and look for everything that we know might be there.
         */



        $denizens = array();
        $denizens[$denizen->id] = $denizen;
        $elementRelations = array();
       
        $elements = DAEntity\Relation::getRelatedDenizens($denizen->id, null);
        array_unshift($elements, $denizen);
        foreach ($elements as $element) { // Get the relations
            if ( ! array_key_exists($element->id, $denizens)) $denizens[$element->id] = $element;
            $relations = DAEntity\Relation::getRelations($element->id);
            //if ($element->type != 3) dd($relations);
            $elementRelations[$element->id] = array();
            foreach ($relations as $relation) {
                $to = $relation->toId;
                $relType = DAEntity\Eloquent\RelationType::find($relation->relationId);
                $relationName = $relType->name;
                if ( ! array_key_exists($to, $denizens)) {
                    $denizens[$to] = DAEntity\Denizen::find($to);
                }
                $elementRelations[$element->id][] = array($relationName, 
                                                          $denizens[$to]->name . " (".$denizens[$to]->id.")");
            }
        }

        return \View::make('stories.show', array('story' => $denizen, 'elements' => $elements,
                                                 'relations' => $elementRelations));
    }

}


