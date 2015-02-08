<?php namespace DemocracyApps\CNP\Http\Middleware;

use Closure;

class VerifyAdminAccess {

	/**
	 * Handle an incoming request.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Closure  $next
	 * @return mixed
	 */
	public function handle($request, Closure $next)
	{
		if (\Auth::guest() || ! \Auth::user()->projectcreator) {
			return redirect('/');
		}
		return $next($request);
	}

}
