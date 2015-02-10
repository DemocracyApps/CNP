<?php namespace DemocracyApps\CNP\Http\Controllers;

use DemocracyApps\CNP\Analysis\AnalysisOutput;
use DemocracyApps\CNP\Analysis\AnalysisSet;
use DemocracyApps\CNP\Analysis\AnalysisSetItem;
use DemocracyApps\CNP\Analysis\Perspective;
use Illuminate\Http\Request;

class PerspectiveAdminController extends Controller {

	protected $perspective;

	function __construct (Perspective $perspective)
	{
		$this->perspective	= $perspective;
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		//
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @param Request $request
	 * @return Response
	 */
	public function create(Request $request)
	{
		return view('admin.perspectives.create', array('project' => $request->get('project')));
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store(Request $request)
	{
		$rules = ['name'=>'required'];
		$validator = \Validator::make($request->all(), $rules);
		if ($validator->fails()) {
			return \Redirect::back()->withInput()->withErrors($validator->messages());
		}

		$this->perspective->name       = $request->get('name');
		$this->perspective->project    = $request->get('project');
		$this->perspective->type       = "Unknown";
		$this->perspective->requires_analysis = false;

		if ($request->has('description')) $this->perspective->description = $request->get('description');

		// Now load in the file
		if ($request->hasFile('specification')) {
			$file = $request->file('specification');
			$this->perspective->specification = \File::get($file->getRealPath());
			$jp = \CNP::getJsonProcessor();

			$str = $jp->minifyJson($this->perspective->specification);
			$cfig = $jp->decodeJson($str, true);
			if ( ! $cfig) {
				return \Redirect::back()->withInput()->withErrors(array('fileerror' => 'JSON not well-formed'));
			}

			/*
             * Now configure the type
             */
			$this->configurePerspectiveType($cfig['type'], $this->perspective);
		}
		$this->perspective->last = date('Y-m-d H:i:s', time() - 24 * 60 * 60); // We only care that it's strictly before updated time.
		$this->perspective->save();

		return redirect('/admin/perspectives/'.$this->perspective->id);

	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id, Request $request)
	{
		$perspective = Perspective::find($id);
		return view('admin.perspectives.show', array('perspective' => $perspective));
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id, Request $request)
	{
		$perspective = Perspective::find($id);
		return view('admin.perspectives.edit', array('perspective' => $perspective, 'fileerror' => null));
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id, Request $request)
	{
		$rules = ['name'=>'required'];
		$validator = \Validator::make($request->all(), $rules);
		if ($validator->fails()) {
			return \Redirect::back()->withInput()->withErrors($validator->messages());
		}

		$this->perspective = Perspective::find($id);
		$this->perspective->name       = $request->get('name');
		$this->perspective->project    = $request->get('project');

		if ($request->has('description')) $this->perspective->description = $request->get('description');

		// Now load in the file
		if ($request->hasFile('specification')) {
			$file = $request->file('specification');
			$this->perspective->specification = \File::get($file->getRealPath());
			$jp = \CNP::getJsonProcessor();

			$str = $jp->minifyJson($this->perspective->specification);
			$cfig = $jp->decodeJson($str, true);
			if ( ! $cfig) {
				return \Redirect::back()->withInput()->withErrors(array('fileerror' => 'JSON not well-formed'));
			}
			/*
             * Now configure the type
             */
			$this->configurePerspectiveType($cfig['type'], $this->perspective);

		}
		$this->perspective->save();

		return redirect('/admin/perspectives/'.$this->perspective->id);

	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		$perspective = Perspective::find($id);
		$projectId = $perspective->project;

		$analysisOutputs = AnalysisOutput::whereColumn('perspective', '=', $id);
		foreach ($analysisOutputs as $output) {
			$analysisSets = AnalysisSet::whereColumn('analysis_output', '=', $output->id);
			foreach($analysisSets as $set) {
				$items = AnalysisSetItem::whereColumn('analysis_set', '=', $set->id);
				foreach($items as $item) {
					AnalysisSetItem::deleteById($item->id);
				}
				AnalysisSet::deleteById($set->id);
			}
			AnalysisOutput::deleteById($output->id);
		}

		Perspective::deleteById($id);
		return redirect('/admin/projects/'.$projectId);

	}

	private function configurePerspectiveType ($type, $perspective) {

		$perspectives = \CNP::getConfigurationValue('perspectives');
		$found = false;
		for ($i = 0; $i < sizeof($perspectives) && !$found; ++$i) {
			$p = $perspectives[$i];
			if ($p['name'] == $type) {
				$found = true;
				$perspective->type = $type;
				$perspective->requires_analysis = $p['requiresAnalysis'];
			}
		}
		if (! $found) {
			$perspective->type = "Unknown";
			$perspective->requires_analysis = false;
		}
	}

}
