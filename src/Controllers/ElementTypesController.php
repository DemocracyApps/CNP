<?php

namespace DemocracyApps\CNP\Controllers;

use \DemocracyApps\CNP\Entities\Eloquent as DAEloquent;

class ElementTypesController extends BaseController
{
    protected $mainType;
    protected $inverseType;

    public function __construct(DAEloquent\ElementType $mainType)
    {
        $this->mainType    = $mainType;
    }

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
    public function index()
    {
        $elementTypes = \DemocracyApps\CNP\Entities\Eloquent\ElementType::all()->sortBy('id');
        return \View::make('system.elementtypes.index')->with('elementtypes',$elementTypes);
    }

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		return \View::make('system.elementtypes.create');
	}

	public function store()
	{
        $input = \Input::all();
        $this->mainType->fill($input);
        if (! $this->mainType->isValid()) {
            return \Redirect::back()->withInput()->withErrors($this->mainType->messages);
        }
        $this->mainType->save();

        return \Redirect::route('system.elementtypes.index');
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
