<?php namespace DemocracyApps\CNP\Controllers;

use \DemocracyApps\CNP\Entities as DAEntity;
use \DemocracyApps\CNP\Utility\Api as Api;
use \DemocracyApps\CNP\Compositions\Composer as Composer;
use \DemocracyApps\CNP\Compositions\Outputs\Vista as Vista;

class ProjectsController extends ApiController {
	protected $project;
	protected $projectTransformer;

	function __construct (DAEntity\Project $project, 
						  \DemocracyApps\CNP\Transformers\ProjectTransformer $projectTransformer) 
	{
		$this->project 			= $project;
		$this->projectTransformer = $projectTransformer;
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
			return \View::make('projects.index', array('projects'=>$projects));
		}
	}

	public function show ($id)
	{
		$project = DAEntity\Project::find($id);
		$composers = Composer::where('project', '=', $id)->get();
		$vistas = Vista::where('project', '=', $id)->get();
		$isAPI = Api::isApiCall(\Request::server('REQUEST_URI'));
		if ($isAPI) {
			if (!$project) {
				return $this->respondNotFound('Project '.$id.' does not exist');
			}
			else {
				$data = $this->projectTransformer->transform($project);
				return $this->respondIndex('Requested project', $data);
			}
		}
		else {
			return \View::make('projects.show', array('project' => $project, 'composers' => $composers, 'vistas' => $vistas));
		}
	}

	public function create() 
	{
    	\Session::put('CNP_RETURN_URL', \Request::server('HTTP_REFERER'));
    	return \View::make('projects.create');
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

	        $this->project->name = $data['name'];
	        $this->project->setProperty('access', $data['access']);
	        if ($data['content']) $this->project->description = $data['content'];
	        $this->project->userid = $user->getId();
	        $this->project->save();

	        // Now let's create the relations with the creator Person
	        // $person = DAEntity\Person::find($user->getElementId());
	        // $relations = DAEntity\Relation::createRelationPair($person->getId(), $this->project->getId(),
	        //                                                   "is-creator-of");
	        // foreach($relations as $relation) {
	        //     $relation->save();
	        // }

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

}
