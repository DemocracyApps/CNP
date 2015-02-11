<?php namespace DemocracyApps\CNP\Http\Controllers;

use DemocracyApps\CNP\Analysis\Perspective;
use DemocracyApps\CNP\Http\Requests;
use DemocracyApps\CNP\Project\Project;
use DemocracyApps\CNP\Project\ProjectUser;

class PerspectivesController extends Controller {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index($projectId)
	{
		$project = Project::find($projectId);
		$owner = (ProjectUser::projectAdminAccess($project->id, \Auth::id()));

		$perspectives = Perspective::whereColumn('project', '=', $projectId);

		return view('project.perspectives.index', array('project' => $project, 'perspectives' => $perspectives, 'owner' => $owner));
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		//
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		//
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($projectId, $id)
	{
		$project = Project::find($projectId);
		$perspective = Perspective::find($id);
		return view('project.perspectives.show', array('project' => $project, 'perspective' => $perspective));
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
