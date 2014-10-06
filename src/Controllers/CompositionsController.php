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
            return \View::make('compositions.index', array('stories' => $stories, 'project' => $project));
        }
        else {
            return \Redirect::to('/projects');
        }
    }

    public function explore()
    {
        if (\Input::has('project')) {
            $project = \Input::get('project');
            $compositions = Composition::where('project', '=', $project)->get();
            $elements = Element::getProjectElements($project);
            $types = array();
            foreach ($elements as $element) {
                if (array_key_exists($element->name, $types)) {
                    ++$types[$element->name];
                }
                else {
                    $types[$element->name] = 1;
                }
            }
            return \View::make('compositions.explore', array('compositions' => $compositions, 
                                                             'count' => count($compositions),
                                                             'types' => $types
                                                             ));
        }
        else {
            \Redirect::to('/projects');
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
              return \Redirect::to('/compositions?project='.$composer->project);
            }
        }
        else if ($viewMode == 'structure') {
            $elementRelations = array(); 
            $elementsById = array();
            $elements = Relation::getRelatedElements($topElement->id, null);
            array_unshift($elements, $topElement);

            foreach ($elements as $element) { // Get the relations
                if ( ! array_key_exists($element->id, $elementsById)) $elementsById[$element->id] = $element;
                $relations = Relation::getRelations($element->id);

                $elementRelations[$element->id] = array();
                \Log::info("Dealing with element " . $element->id);
                foreach ($relations as $relation) {
                    $to = $relation->toId;
                    $relType = \DemocracyApps\CNP\Entities\Eloquent\RelationType::find($relation->relationId);
                    $relationName = $relType->name;
                    if ( ! array_key_exists($to, $elementsById)) {
                        $elementsById[$to] = Element::find($to);
                    }
                    $elementRelations[$element->id][] = array($relationName, 
                                                              $elementsById[$to]->name . " (".$elementsById[$to]->id.")");
                }
            }
            return \View::make('compositions.show_structure', array('story' => $topElement, 
                                                     'elements' => $elements,
                                                     'elementsById' => $elementsById,
                                                     'relations' => $elementRelations,
                                                     'composition' => $composition));
        }

    }

    public function create()
    {
        if (\Auth::guest()) {
            return \Redirect::to('/login');         
        }

        if ( ! \Input::has('composer')) throw new \Exception("No composer id specified.");

        $composer = Composer::find(\Input::get('composer'));
        if ( ! $composer ) throw new \Exception("Composer ".\Input::get('composer'). " not found.");

        if ( ! $composer->validForInput()) throw new \Exception("Composer ".\Input::get('composer') . " not valid for input.");
        $composer->initializeForInput(\Input::all());

        if (\Input::get('referent')) {
            $composer->setReferentByElementId(\Input::get('referent'));
        }

        $composition = new \DemocracyApps\CNP\Compositions\Composition;
        $composition->input_composer_id = $composer->id;
        $composition->userid = \Auth::user()->getId();
        $composition->title = "No title";
        $composition->project = $composer->project;
        $composition->save();

        $inputType = $composer->getInputType();
        if ($inputType == 'csv-simple') {
            return \View::make('compositions.csvUpload', array('composer' => $composer, 'composition' => $composition));
        }
        elseif ($inputType == 'auto-interactive') {
            return \View::make('compositions.autoinput', array('composer' => $composer, 'composition' => $composition));
        }
        else {
            return "Unknown input type " . $inputType;
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store()
    {
        \Log::info("In store");
        if (\Auth::guest()) {
            return \Redirect::to('/login');         
        }
        $input = \Input::all();
        $composition = Composition::find(\Input::get('composition'));
        $composer = Composer::find($composition->input_composer_id);
        if ( ! $composer->validateInput($input)) {
            return \Redirect::back()->withInput()->withErrors($composer->messages());
        }
        if (\Input::get('referentId')) {
            $composer->setReferentByReferentId(\Input::get('referentId'));
        }
        $inputType = $composer->getInputType();

        $composer->initializeForInput($input);
        $composer->processInput($input, $composition);
        if ($inputType == 'auto-interactive') {
            if ( ! $composer->getDriver()->done()) {
                return \View::make('compositions.autoinput', array('composer' => $composer, 'composition' => $composition));
            }
        }
        return \Redirect::to('/compositions?project='.$composer->project);
    }

}
