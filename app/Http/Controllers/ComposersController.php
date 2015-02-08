<?php namespace DemocracyApps\CNP\Http\Controllers;

use DemocracyApps\CNP\Http\Requests;
use DemocracyApps\CNP\Http\Controllers\Controller;

use DemocracyApps\CNP\Project\Compositions\Composer;
use DemocracyApps\CNP\Services\JsonProcessor;
use Illuminate\Http\Request;

class ComposersController extends Controller {
	protected $composer;
	protected $jsonProcessor = null;

	function __construct (Composer $composer, JsonProcessor $jp)
	{
		$this->composer = $composer;
		$this->jsonProcessor = $jp;
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$composers = Composer::getUserComposers(\Auth::id());
		return view('composers.index', array('composers' => $composers));
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		return view('composers.create', array('project' => \Input::get('project')));
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store(Request $request)
	{
		$data = \Input::all();
		$rules = ['name'=>'required'];
		$this->validate($request, $rules);

		$this->composer->name = $data['name'];
		$this->composer->project = $data['project'];

		if ($data['output']) {
			$this->composer->output = $data['output'];
		}
		if ($data['description']) $this->composer->description = $data['description'];

		// Now load in the file
		if ($request->hasFile('composer')) {
			$ok = $this->composer->loadSpecification($request->file('composer'), $this->jsonProcessor);
			if ( ! $ok) {
				return \Redirect::back()->withInput()->withErrors(array('fileerror' => 'JSON not well-formed'));
			}
		}
		$this->composer->save();

		return redirect('/admin/composers/'.$this->composer->id);
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		$composer = Composer::find($id);
		return view('composers.show', array('composer' => $composer));
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		$composer = Composer::find($id);
		return view('composers.edit', array('project' => \Input::get('project'),
			'composer' => $composer,
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
		$rules = ['name'=>'required'];
		$this->validate($request, $rules);

		$composer = Composer::find($id);
		$composer->name = $data['name'];
		if ($data['output']) {
			$composer->output = $data['output'];
		}
		if (\Input::has('description')) $composer->description = $data['description'];
		if (\Input::hasFile('composer')) {
			$ok = $this->loadComposerSpecification($composer, \Input::file('composer'));
			if ( ! $ok) {
				return \Redirect::back()->withInput()->withErrors(array('fileerror' => 'JSON not well-formed'));
			}
		}
		$composer->save();
		return redirect('/admin/composers/'.$composer->id);
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		$composer = Composer::find($id);
		$project = $composer->project;
		$composer->delete();
		return redirect('/admin/projects/' . $project);
	}

}
