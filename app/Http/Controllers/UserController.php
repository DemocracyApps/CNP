<?php namespace DemocracyApps\CNP\Http\Controllers;

use DemocracyApps\CNP\Graph\Element;
use DemocracyApps\CNP\Http\Requests;
use DemocracyApps\CNP\Http\Controllers\Controller;

use DemocracyApps\CNP\Project\Project;
use DemocracyApps\CNP\Users\User;
use DemocracyApps\CNP\Utility\Mailers\UserMailer;
use Illuminate\Http\Request;

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
		return view('user.profile', array('user' => $user, 'person' => $person, 'projects' => $projects));
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
		return view('user.edit', array('user' => $user, 'putUrl'=>'account.update', 'system' => false));
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

}
