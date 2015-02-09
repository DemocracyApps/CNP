<?php namespace DemocracyApps\CNP\Http\Controllers;

use DemocracyApps\CNP\Http\Requests;

use DemocracyApps\CNP\Project\Project;
use DemocracyApps\CNP\Project\ProjectUser;
use Illuminate\Http\Request;

class ProjectController extends Controller {

	public function index (Request $request)
    {
        return "Hi";
    }

    public function authorize($projectId, Request $request) {
        if (\Auth::guest()) {
            return \Redirect::to('/login');
        }
        $project = Project::find($projectId);
        if (\Request::method() == 'GET') {
            if (!Project::checkPostAuthorized($project->id, \Auth::id())) {
                return \View::make('admin.authorize', array('project' => $project));
            }
            else {
                return \Redirect::intended('/' . $projectId);
            }
        }
        else {
            $authorized = true;
            if ($project->hasProperty('secret')) {
                if ($project->getProperty('secret') != null) {
                    if ($project->getProperty('secret') != \Input::get('secret')) $authorized = false;
                }
            }
            if ($authorized) {
                ProjectUser::authorizePostAccess($project->id, \Auth::id());
                // TODO - we should set a flash message here or something.
                return \View::make('admin.authorize_success', array('project' => $project));
            }
            else {
                return \View::make('admin.authorize_failure', array('project' => $project));
            }
        }
    }


}
