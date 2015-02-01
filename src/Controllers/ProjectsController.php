<?php namespace DemocracyApps\CNP\Controllers;

use \DemocracyApps\CNP\Entities as DAEntity;
use \DemocracyApps\CNP\Entities\Project;
use \DemocracyApps\CNP\Entities\ProjectUser;
use \DemocracyApps\CNP\Utility\Api as Api;
use \DemocracyApps\CNP\Compositions\Composer as Composer;
use \DemocracyApps\CNP\Utility\Html;
use \DemocracyApps\CNP\Analysis\Perspective;
use \DemocracyApps\CNP\Mailers\Mailer;

class ProjectsController extends ApiController {
	protected $project;
	protected $projectTransformer;

	function __construct (DAEntity\Project $project, 
						  \DemocracyApps\CNP\Transformers\ProjectTransformer $projectTransformer)
	{
		$this->project 			= $project;
		$this->projectTransformer = $projectTransformer;
	}

	public function authorize($id)
	{
		if (\Auth::guest()) {
			return \Redirect::to('/login');
		}
		$project = Project::find($id);
		if (\Request::method() == 'GET') {
			if (!Project::checkPostAuthorized($project->id, \Auth::id())) {
				return \View::make('projects.authorize', array('project' => $project));
			}
			else {
				return \Redirect::intended('/' . $id);
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
				$dest = '/'.$id.'/authorized';
				return \View::make('projects.authorize_success', array('project' => $project));
			}
			else {
				return \View::make('projects.authorize_failure', array('project' => $project));
			}
		}
	}

	/**
	 * List all projects
	 * @return [] [description]
	 */
	public function index()
	{
		$isAPI = Api::isApiCall(\Request::server('REQUEST_URI'));
    	$projects = DAEntity\Project::allUserProjects(\Auth::id());

    	if ($isAPI) {
	    	$data = $this->projectTransformer->transformCollection($projects);
			return $this->respondIndex('List of API user projects', $data);
		}
		else {
			//TODO: Could later add projects on which user is authorized
			return \View::make('projects.index', array('projects'=>$projects));
		}
	}

	public function show ($id)
	{
		if (ProjectUser::projectAdminAccess($id, \Auth::id())) {
			$project = DAEntity\Project::find($id);
			$composers = Composer::where('project', '=', $id)->get();

			$perspectives = Perspective::whereColumn('project', '=', $id);
			$isAPI = Api::isApiCall(\Request::server('REQUEST_URI'));
			if ($isAPI) {
				if (!$project) {
					return $this->respondNotFound('Project ' . $id . ' does not exist');
				} else {
					$data = $this->projectTransformer->transform($project);
					return $this->respondIndex('Requested project', $data);
				}
			} else {
				return \View::make('projects.show', array('project' => $project, 'composers' => $composers, 'perspectives' => $perspectives));
			}
		}
		else {
			return \Redirect::to('/');
		}
	}

	public function create() 
	{
    	\Session::put('CNP_RETURN_URL', \Request::server('HTTP_REFERER'));
    	return \View::make('projects.create');
	}

	public function edit($id)
	{
		$project = Project::find($id);
		return \View::make('projects.edit', array('project' => $project,
			'fileerror' => null));
	}

	public function store()
	{
		$isAPI = Api::isApiCall(\Request::server('REQUEST_URI'));
		$params = [];
		if ($isAPI) {
			if (\Input::json() && sizeof(\Input::json()->all()) > 0) {
				$data = \Input::json()->get('data');
				$params = \Input::json()->get('params');
			}
			else {
				return $this->respondFormatError('Empty or invalid JSON body');
			}
		}
		else {
			$data = \Input::all();
		}
		$multi = array_key_exists('multi', $params)?$params['multi']:false;

		if (! $multi) {
	        $rules = ['name'=>'required', 'access'=>'required'];
	        $validator = \Validator::make($data, $rules);
	        if ($validator->fails()) {
	        	if ($isAPI) {
	        		return $this->respondFailedValidation(Api::compactMessages($validator->messages()));
	        	}
	        	else {
	            	return \Redirect::back()->withInput()->withErrors($validator->messages());
	            }
	        }
	        // Validation OK, let's create the project
	        $user = DAEntity\Eloquent\User::find(\Auth::user()->getId());

	        $this->project->name = Html::cleanInput($data['name']);
	        $this->project->setProperty('access', Html::cleanInput($data['access']));
	        if ($data['content']) $this->project->description = Html::cleanInput($data['content']);
			if ($data['access'] != 'Open') {
				if ($data['secret']) {
					$this->project->setProperty('secret', Html::cleanInput($data['secret']));
				}
				if (\Input::hasFile('terms')) {
					$file = \Input::file('terms');
					$terms = \File::get($file->getRealPath());
					$this->project->terms = Html::cleanInput($terms);
					if ($this->project->terms == null) {
						return \Redirect::back()->withInput()->withErrors(array('fileerror' => 'Error reading terms file'));
					}
				}
			}

	        $this->project->userid = $user->getId();
	        $this->project->save();

			$pu = new \DemocracyApps\CNP\Entities\ProjectUser();
			$pu->project = $this->project->id;
			$pu->user = $this->project->userid;
			$pu->access = 3;
			$pu->save();

	        if ($isAPI) {
				$data = $this->projectTransformer->transform($this->project);
				return $this->respondCreated('Project was successfully created', $data);	        	
	        }
	        else {
    			$returnURL = \Session::get('CNP_RETURN_URL');
    			\Session::forget('CNP_RETURN_URL');
    			if ( ! $returnURL) $returnURL = '/';
    			return \Redirect::to($returnURL);
	        }
		}
		else {
			throw new \Exception("Projects multi store not yet implemented");
		}
	}

	public function update($id)
	{
		$data = \Input::all();

		$rules = ['name'=>'required', 'access'=>'required'];
		$validator = \Validator::make($data, $rules);
		if ($validator->fails()) {
			return \Redirect::back()->withInput()->withErrors($validator->messages());
		}
		// Validation OK, let's create the project
		$user = DAEntity\Eloquent\User::find(\Auth::user()->getId());

		$project = Project::find($id);

		$project->name = Html::cleanInput($data['name']);
		$project->setProperty('access', Html::cleanInput($data['access']));
		if ($data['content']) $project->description = Html::cleanInput($data['content']);
		if ($data['access'] != 'Open') {
			if ($data['secret']) {
				$project->setProperty('secret', Html::cleanInput($data['secret']));
			}
			if (\Input::hasFile('terms')) {
				$file = \Input::file('terms');
				$terms = \File::get($file->getRealPath());
				$project->terms = Html::cleanInput($terms);
				if ($project->terms == null) {
					return \Redirect::back()->withInput()->withErrors(array('fileerror' => 'Error reading terms file'));
				}
			}
		}
		else {
			if ($project->hasProperty('secret')) $project->deleteProperty('secret');
			$project->terms = "";
		}

		$project->save();

		return \Redirect::to('/admin/projects/'.$id);

	}

	/*
	 * AJAX
	 */
	
	public function setDefaultInputComposer()
	{
		if (!\Input::has('project')) return $this->respondFormatError('No project specified');
		if (!\Input::has('defaultInputComposer')) return $this->respondFormatError('No default composer specified');
		$project = Project::find(\Input::get('project')); 
		if (!$project) return $this->respondNotFound('Project with ID '.\Input::get('project').' not found');
		if (\Input::get('defaultInputComposer') < 0) {
			$project->deleteProperty('defaultInputComposer');
		}
		else {
			$composer = Composer::find(\Input::get('defaultInputComposer'));
			if (!$composer) {
				return $this->respondNotFound('Composer with ID '.\Input::get('defaultInputComposer').' not found');
			}
			$project->setProperty('defaultInputComposer', \Input::get('defaultInputComposer'));
		}
		$project->save();
		return $this->respondOK('Successfully set default input composer', null);
	}

	public function setDefaultOutputComposer()
	{
		if (!\Input::has('project')) return $this->respondFormatError('No project specified');
		if (!\Input::has('defaultOutputComposer')) return $this->respondFormatError('No default composer specified');
		$project = Project::find(\Input::get('project')); 
		if (!$project) return $this->respondNotFound('Project with ID '.\Input::get('project').' not found');
		if (\Input::get('defaultOutputComposer') < 0) {
			$project->deleteProperty('defaultOutputComposer');
		}
		else {
			$composer = Composer::find(\Input::get('defaultOutputComposer'));
			if (!$composer) {
				return $this->respondNotFound('Composer with ID '.\Input::get('defaultOutputComposer').' not found');
			}
			$project->setProperty('defaultOutputComposer', \Input::get('defaultOutputComposer'));
		}
		$project->save();
		return $this->respondOK('Successfully set default output composer', null);
	}
}
