<?php namespace DemocracyApps\CNP\Http\Controllers;

use DemocracyApps\CNP\Project\Compositions\Composition;
use DemocracyApps\CNP\Http\Requests;

use DemocracyApps\CNP\Project\Compositions\Composer;
use DemocracyApps\CNP\Project\Project;
use DemocracyApps\CNP\Project\ProjectUser;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;

class CompositionsController extends Controller {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index($projectId, Request $request)
	{
		$project = Project::find($projectId);
		$sort = 'date';
		$desc  = true;
		if ($request->has('sort')) {
			$sort = $request->get('sort');
		}
		if ($request->has('desc')) {
			$val = $request->get('desc');
			if ($val == 'false') $desc=false;
		}
		$filter = false;
		$target = null;
		if ($request->has('filter')) {
			$filter = true;
		}

		$owner = false;
		if (!\Auth::guest()) {
			$owner = (ProjectUser::projectAdminAccess($project->id, \Auth::id()));
		}
		$page = $request->get('page', 1);
		$pageLimit=\CNP::getConfigurationValue('pageLimit');

		// Need to:
		// 2. Change output to say it is filtered.
		// 3. Add a "back" button to show that takes you back to current view.

		$filterDescription = null;
		$advancedView = false;
		$advancedViewMessage = null;
		$structureView = false;
		if ($request->has('structureView')) {
			if ($request->get('structureView') == 1) $structureView = true;
		}

		if ($filter) {
			$filterType = $request->get('filter');
			if ($filterType == 'related') {
				$target = $request->get('element');
				$element = Element::find($target);
				// We want all compositions that refer to this target element
				$data = Composition::projectCompositionsByReferentPaged($target, $sort, $desc, $project->id, $page, $pageLimit);
				$filterDescription = "Contributions related to " . $element->getContent();

				if ($request->has('advancedView')) {
					if ($request->get('advancedView') == 1) {
						$advancedView = true;
						$href = "/$project->id/compositions?filter=related&element=$element->id&structureView=1";
						$advancedViewMessage = "This is a list of stories and contributions that refer to " . $element->getContent() . ".";
						$advancedViewMessage .= " Click <a href='" . $href . "'>here</a> to see the internal structure of the references.";

					}
				}
			}
		}
		else {
			$data = Composition::allProjectCompositionsPaged($sort, $desc, $project->id, $page, $pageLimit);
		}

		//$stories = \Paginator::make($data['items'], $data['total'], $pageLimit);
		$stories = new Paginator($data['items'], $data['total'], $pageLimit);

		if ($structureView) {
			return "Structure view not implemented";
		}
		else {
			return \View::make('project.index', array('stories' => $stories, 'project' => $project,
				'owner' => $owner, 'sort' => $sort, 'desc' => $desc,
				'filterDescription' => $filterDescription,
				'advancedView' => $advancedView,
				'advancedViewMessage' => $advancedViewMessage));
		}
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @param $projectId
	 * @param Request $request
	 * @return Response
	 * @throws \Exception
	 */
	public function create($projectId, Request $request)
	{
		if (\Auth::guest()) {
			return redirect()->guest('/login');
		}

		$project = Project::find($projectId);
		$access = $project->postAuthorization(\Auth::id());
		if (! $access->allowed) {
			if ($access->reason == 'authorization') {
				return redirect()->guest('/'.$projectId.'/authorize');
			}
			else {
				return redirect()->guest('user/noconfirm');
			}
		}

		if ( ! $request->has('composer')) throw new \Exception("No composer id specified.");

		$composer = Composer::find($request->get('composer'));
		if ( ! $composer ) throw new \Exception("Composer ".$request->get('composer'). " not found.");

		if ( ! $composer->validForInput()) throw new \Exception("Composer ".$request->get('composer') . " not valid for input.");
		$composer->initializeForInput($request->all());

		if ($request->get('referent')) {
			$composer->setReferentByElementId($request->get('referent'), $request->get('referentRelation'));
		}

		$composition = new Composition;
		$composition->title = "No Title";
		$composition->input_composer_id = $composer->id;
		$composition->userid = \Auth::user()->id;
		$composition->project = $composer->project;
		$composition->save();

		$inputType = $composer->getInputType();
		if ($inputType == 'csv-simple') {
			return view('project.csvUpload', array('composer' => $composer, 'composition' => $composition));
		}
		elseif ($inputType == 'auto-interactive') {
			return view('project.autoinput', array('composer' => $composer, 'composition' => $composition));
		}
		else {
			return "Unknown input type " . $inputType;
		}
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param $projectId
	 * @param Request $request
	 * @return Response
	 */
	public function store($projectId, Request $request)
	{
		if (\Auth::guest()) {
			return redirect()->guest('/login');
		}
		$project = Project::find($projectId);
		$access = $project->postAuthorization(\Auth::id());
		if (! $access->allowed) {
			if ($access->reason == 'authorization') {
				return redirect()->guest('/'.$projectId.'/authorize');
			}
			else {
				return redirect()->guest('user/noconfirm');
			}
		}

		$input = $request->all();
		$composition = Composition::find($request->get('composition'));
		$composer = Composer::find($composition->input_composer_id);
		if ( ! $composer->validateInput($input)) {
			return \Redirect::back()->withInput()->withErrors($composer->messages());
		}
		if ($request->get('referentId')) {
			$composer->setReferentByReferentId($request->get('referentId'), $request->get('referentRelation'));
		}
		$inputType = $composer->getInputType();

		$composer->initializeForInput($input);
		$composer->processInput($input, $composition);
		if ($inputType == 'auto-interactive') {
			if ( ! $composer->getDriver()->done()) {
				return view('project.autoinput', array('composer' => $composer, 'composition' => $composition));
			}
		}
		return redirect('/'.$composer->project.'/compositions');
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		//
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		//
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		//
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
