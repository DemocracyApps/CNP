<?php
namespace DemocracyApps\CNP\Controllers;

use \DemocracyApps\CNP\Entities as DAEntity;
use \DemocracyApps\CNP\Compositions\Composer as Composer;
use \DemocracyApps\CNP\Compositions\Composition;
use \DemocracyApps\CNP\Entities\Element;
use \DemocracyApps\CNP\Entities\Project;

class StoriesController extends BaseController {
    protected $story;

    public function __construct(DAEntity\Story $story)
    {
        $this->story = $story;
    }

    public function curate()
    {
        if (\Input::has('project')) {
            $project = \Input::get('project');
            $composers = \DemocracyApps\CNP\Compositions\Composer::where('project', '=', $project)->get();
            $ctmp = array();
            foreach($composers as $c) {
                if (strstr($c->contains, 'input')) $ctmp[] = $c;
                \Log::info("Composer contains: " . $c->contains);
            }
            $selectedComposers = null;
            if (\Input::has('templates')) {
                $selectedComposers = \Input::get('templates');
            }
            return \View::make('stories.curate', 
                           array('project' => $project,
                                 'composers' => $ctmp,
                                 'selectedComposers' => $selectedComposers));
        }
        else {
            return \View::make('stories.curate');
        }
    }


	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
}
