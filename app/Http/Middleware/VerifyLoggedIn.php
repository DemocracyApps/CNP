<?php namespace DemocracyApps\CNP\Http\Middleware;

use Closure;

class VerifyLoggedIn {

	/**
	 * Handle an incoming request.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Closure  $next
	 * @return mixed
	 */
	public function handle($request, Closure $next)
	{

		if (\Auth::guest()) {
			redirect('auth/login');
		}
		return $next($request);
	}

}
