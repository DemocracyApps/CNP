<?php namespace DemocracyApps\CNP\Http\Controllers;

use DemocracyApps\CNP\Analysis\Perspective;
use DemocracyApps\CNP\Project\Compositions\Composer;
use DemocracyApps\CNP\Project\ProjectUser;
use DemocracyApps\CNP\Users\User;
use DemocracyApps\CNP\Utility\Html;
use Illuminate\Http\Request;
use DemocracyApps\CNP\Http\Requests;
use DemocracyApps\CNP\Project\Project;

class ProjectsController extends Controller {

	protected $project = null;

	function __construct (Project $project)
	{
		$this->project = $project;
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$projects = Project::allUserProjects(\Auth::id());

		//TODO: Could later add projects on which user is authorized
		return view('projects.index', array('projects'=>$projects));
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		\Session::put('CNP_RETURN_URL', \Request::server('HTTP_REFERER'));
		return view('projects.create');
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store(Request $request)
	{
		$data = \Input::all();

		$rules = ['name'=>'required', 'access'=>'required'];
		$this->validate($request, $rules);

		// Validation OK, let's create the project
		$user = User::find(\Auth::user()->id);

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

		$this->project->userid = $user->id;
		$this->project->save();

		$pu = new ProjectUser();
		$pu->project = $this->project->id;
		$pu->user = $this->project->userid;
		$pu->access = 3;
		$pu->save();

		$returnURL = \Session::get('CNP_RETURN_URL');
		\Session::forget('CNP_RETURN_URL');
		if ( ! $returnURL) $returnURL = '/';
		return redirect($returnURL);

	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		if (ProjectUser::projectAdminAccess($id, \Auth::id())) {
			$project = Project::find($id);
			$composers = Composer::whereColumn('project', '=', $id);

			$perspectives = Perspective::whereColumn('project', '=', $id);

			return view('projects.show', array('project' => $project, 'composers' => $composers, 'perspectives' => $perspectives));
		}
		else {
			return redirect('/');
		}
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		$project = Project::find($id);
		return view('projects.edit', array('project' => $project,
			'fileerror' => null));
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id, Request $request)
	{
		$data = \Input::all();

		$rules = ['name'=>'required', 'access'=>'required'];
		$this->validate($request, $rules);

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

		return redirect('/admin/projects/'.$id);
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		//
	}

}
