<?php namespace DemocracyApps\CNP\Http\Controllers\System;

use DemocracyApps\CNP\Http\Controllers\Controller;
use DemocracyApps\CNP\Project\Project;
use DemocracyApps\CNP\Users\User;
use Illuminate\Http\Request;

class SystemController extends Controller
{

	public function settings(Request $request)
	{
		return view('system.settings', array());
	}

	public function users(Request $request)
	{
		$users = User::orderBy('id')->get();
		return view('system.users', array('users' => $users));
	}

	public function userEdit($userId, Request $request)
	{
		if ($request->method() == 'GET') {
			$user = User::find($userId);
			return view('user.edit', array('user' => $user, 'updateUrl' => url('/system/users') . '/' . $userId, 'system' => true));
		}
		else if ($request->method() == 'PUT') {
			$user = User::find($userId);
			$user->name = $request->get('name');
			$user->superuser = ($request->get('superuser')=='1')?true:false;
			$user->projectcreator = ($request->get('projectcreator')=='1')?true:false;
			$user->save();
			return redirect('/system/users');

		}
	}

	public function projects (Request $request)
	{
		$projects = Project::all();
		return view('system.projects', array('projects' => $projects));
	}
}
