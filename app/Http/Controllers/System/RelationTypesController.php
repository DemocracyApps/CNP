<?php namespace DemocracyApps\CNP\Http\Controllers\System;

use DemocracyApps\CNP\Graph\RelationType;
use DemocracyApps\CNP\Http\Controllers\Controller;

use Illuminate\Http\Request;

class RelationTypesController extends Controller {

	protected $mainType;
	protected $inverseType;


	public function __construct(RelationType $mainType, RelationType $inverseType)
	{
		$this->mainType    = $mainType;
		$this->inverseType = $inverseType;
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$relationtypes = RelationType::all();
		return view('system.relationtypes.index', array('relationTypes' => $relationtypes));
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		return view('system.relationtypes.create', array());
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store(Request $request)
	{
		$rules = ['name'=>'required'];
		$this->validate($request, $rules);
		$this->mainType->name = $request->get('name');
		$this->mainType->allowedfrom = $request->get('allowedfrom');
		$this->mainType->allowedto = $request->get('allowedto');

		$this->mainType->save();

		if ($request->has('inverseName')) {
			$this->mainType->initializeInverse($this->inverseType, $request->get('inverseName'));
			$this->inverseType->save();
			$this->mainType->inverse = $this->inverseType->id;
			$this->mainType->save();
		}

		return redirect('system/relationtypes');
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
