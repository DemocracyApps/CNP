<?php
namespace DemocracyApps\CNP\Controllers;

use \DemocracyApps\CNP\Entities as DAEntity;

class StoriesController extends BaseController {
    protected $story;

    public function __construct(DAEntity\Story $story)
    {
        $this->story = $story;
    }

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
        $stories = DAEntity\Story::all();
        return \View::make('stories.index')->with('stories', $stories);
	}


	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
        if (\Auth::check()) {
            return \View::make('stories.create');
        }
        else {
            //return "Not logged 
            return \Redirect::to('/login');
        }
	}


	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
        $input = \Input::all();
        $rules = ['name'=>'required', 'content'=>'required'];
        $validator = \Validator::make($input, $rules);
        if ($validator->fails()) {
            return \Redirect::back()->withInput()->withErrors($validator->messages());
        }
        $this->story->setName($input['name']);
        $this->story->setContent($input['content']);
        $this->story->save();
        $user = DAEntity\Eloquent\User::find(\Auth::user()->getId());
        $person = DAEntity\Person::find($user->getDenizenId());
        $relations = DAEntity\Relation::createRelationPair($person->getId(), $this->story->getId(),
                                                          "CreatorOf");
        foreach($relations as $relation) {
            $relation->save();
        }
        return var_dump($relations);

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
