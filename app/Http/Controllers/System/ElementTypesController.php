<?php namespace DemocracyApps\CNP\Http\Controllers\System;

use DemocracyApps\CNP\Graph\ElementType;
use DemocracyApps\CNP\Http\Controllers\Controller;

use Illuminate\Http\Request;

class ElementTypesController extends Controller {
	protected $mainType;

	public function __construct (ElementType $type)
	{
		$this->mainType = $type;
	}
	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$elementTypes = ElementType::all();
		return view('system.elementtypes.index', array('elementTypes' => $elementTypes));
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		return view('system.elementtypes.create', array());
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param Request $request
	 * @return Response
	 */
	public function store(Request $request)
	{
		$this->mainType->name = $request->get('name');

		$rules = ['name'=>'required'];
		$this->validate($request, $rules);

		$this->mainType->save();

		return redirect('system/elementtypes');

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
