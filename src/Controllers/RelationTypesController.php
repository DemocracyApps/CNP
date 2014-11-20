<?php

namespace DemocracyApps\CNP\Controllers;

use \DemocracyApps\CNP\Entities\Eloquent as DAEloquent;

class RelationTypesController extends BaseController
{
    protected $mainType;
    protected $inverseType;

    public function __construct(DAEloquent\RelationType $mainType, DAEloquent\RelationType $inverseType)
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
        $relationtypes = \DemocracyApps\CNP\Entities\Eloquent\RelationType::all()->sortBy('id');
        return \View::make('system.relationtypes.index')->with('relationtypes',$relationtypes);
    }

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		return \View::make('system.relationtypes.create');
	}

	public function store()
	{
        $input = \Input::all();
        $this->mainType->fill($input);
        if (! $this->mainType->isValid()) {
            return \Redirect::back()->withInput()->withErrors($this->mainType->messages);
        }
        $this->mainType->save();
        if ($input['inverseName']) {
            $firstId = $this->mainType->getKey();
            $this->mainType->initializeInverse($this->inverseType, $input['inverseName']);
            $this->inverseType->save();
            $this->mainType->inverse = $this->inverseType->id;
            $this->mainType->save();
        }

        return \Redirect::route('system.relationtypes.index');
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
