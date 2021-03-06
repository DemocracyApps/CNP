<?php namespace DemocracyApps\CNP\Http\Controllers;

use DemocracyApps\CNP\Graph\Element;
use DemocracyApps\CNP\Http\Controllers\Controller;

use DemocracyApps\CNP\Project\Compositions\Composition;
use DemocracyApps\CNP\Project\Project;
use DemocracyApps\CNP\Users\User;
use DemocracyApps\CNP\Utility\Mailers\UserMailer;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;

class UserController extends Controller {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		// Later, for system admins
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
	public function show($id)
	{
		$user = User::find($id);
		$person = Element::find($user->elementid);
		$projects = Project::whereColumn('userid', '=', $id);
		return view('user.profile', array('user' => $user, 'person' => $person, 'admin' => $projects));
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		$user = User::find($id);
		return view('user.edit', array('user' => $user, 'updateUrl'=>url('user'.'/'.$id), 'system' => false));
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int $id
	 * @param Request $request
	 * @return Response
	 */
	public function update($id, Request $request)
	{
		$rules = ['name' => 'required', 'email' => 'required|email'];
		$this->validate($request, $rules);
		$user = User::find($id);
		$user->name = $request->get('name');
		$email = $request->get('email');
		if ($email != $user->email) {
			//Check that nobody else has the email.
			$others = User::where('email', '=', $email)->first();
			if ($others != null) {
				return \Redirect::back()->withInput()->withErrors(array('email' => 'Another account with this email already exists'));
			}
			$user->email = $email;
			$user->verified = false;
			$user->save();
			$mailer = new UserMailer();
			$mailer->confirmEmail($user);

			\Session::put('url.intended', '/user/profile');
			return redirect('user/email_changed');
		}
		else {
			$user->save();
		}

		return redirect('/user/profile');
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

	public function contributions (Request $request)
	{
		$id = \Auth::user()->id;
		$user = User::find($id);
		$person = Element::find($user->elementid);

		$sort = 'date';
		$desc  = true;
		if ($request->has('sort')) {
			$sort = $request->get('sort');
		}
		if ($request->has('desc')) {
			$val = $request->get('desc');
			if ($val == 'false') $desc=false;
		}
		$page = $request->get('page', 1);
		$pageLimit=\CNP::getConfigurationValue('pageLimit');

		$data = Composition::allUserCompositionsPaged($user->id, $sort, $desc, $page, $pageLimit);

		//$stories = \Paginator::make($data['items'], $data['total'], $pageLimit);
		$stories = new Paginator($data['items'], $data['total'], $pageLimit);

		return view('user.contributions', array('stories' => $stories, 'sort' => $sort, 'desc' => $desc, 'user' => $user, 'person' => $person));
	}

}
