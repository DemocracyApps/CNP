<?php namespace DemocracyApps\CNP\Controllers;

use \DemocracyApps\CNP\Compositions\Composer;
use \DemocracyApps\CNP\Compositions\Composition;
use \DemocracyApps\CNP\Entities\Element;
use \DemocracyApps\CNP\Entities\Relation;
use \DemocracyApps\CNP\Entities\Project;

class CompositionsController extends ApiController {

    public function index()
    {
        if (\Input::has('project')) {
            $project = Project::find(\Input::get('project'));
            $page = \Input::get('page', 1);
            $pageLimit=\CNP::getConfigurationValue('pageLimit');
            $data = Composition::allProjectCompositionsPaged($project->id, $page, $pageLimit);
            $stories = \Paginator::make($data['items'], $data['total'], $pageLimit);
            return \View::make('stories.index', array('stories' => $stories, 'project' => $project));
        }
        else {
            return \Redirect::to('/projects');
        }
    }

    public function show($id)
    {
        $viewMode='normal';
        if (\Input::has('view')) {
            $viewMode=\Input::get('view');
        }
        $composition = Composition::find($id);
        $composer = Composer::find($composition->input_composer_id);
        if ($composer->output) {
            $ctmp = Composer::find($composer->output);
            if ($ctmp) $composer = $ctmp;
        }

        $topElement = Element::find($composition->top);

        if ($viewMode == 'normal') {
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
        else if ($viewMode == 'structure') {
            $elements = array();
            $elements[$topElement->id] = $topElement;
            $elementRelations = array();
           
            $elements = Relation::getRelatedElements($topElement->id, null);
            array_unshift($elements, $topElement);
            foreach ($elements as $element) { // Get the relations
                if ( ! array_key_exists($element->id, $elements)) $elements[$element->id] = $element;
                $relations = Relation::getRelations($element->id);
                //if ($element->type != 3) dd($relations);
                $elementRelations[$element->id] = array();
                foreach ($relations as $relation) {
                    $to = $relation->toId;
                    $relType = \DemocracyApps\CNP\Entities\Eloquent\RelationType::find($relation->relationId);
                    $relationName = $relType->name;
                    if ( ! array_key_exists($to, $elements)) {
                        $elements[$to] = Element::find($to);
                    }
                    $elementRelations[$element->id][] = array($relationName, 
                                                              $elements[$to]->name . " (".$elements[$to]->id.")");
                }
            }

            return \View::make('compositions.show_structure', array('story' => $topElement, 'elements' => $elements,
                                                     'relations' => $elementRelations,
                                                     'composition' => $composition));
        }

    }
}
