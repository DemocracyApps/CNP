<?php 
use \DemocracyApps\CNP\Entities as DAEntity;
use \DemocracyApps\CNP\Entities\Eloquent\User;
use \DemocracyApps\CNP\Entities\Eloquent\UserConfirmation;
use \DemocracyApps\CNP\Compositions\Composition;


Route::get('user/profile', array('as' => 'user.profile', 'before' => 'cnp.auth', function()
{
    $user = DAEntity\Eloquent\User::find(\Auth::user()->getId());
    $person = DAEntity\Element::find($user->getElementId());
    $projects = DAEntity\Project::where('userid', '=', $user->getId())->get();
    return View::make('user.account', array('user' => $user, 'person' => $person,
        'projects' => $projects));
}));

Route::get('user/contributions', array('as' => 'user.contributions', 'before' => 'cnp.auth', function()
{
    $user = DAEntity\Eloquent\User::find(\Auth::user()->getId());
    $person = DAEntity\Element::find($user->getElementId());
    $projects = DAEntity\Project::where('userid', '=', $user->getId())->get();

    $sort = 'date';
    $desc  = true;
    if (\Input::has('sort')) {
        $sort = \Input::get('sort');
    }
    if (\Input::has('desc')) {
        $val = \Input::get('desc');
        if ($val == 'false') $desc=false;
    }
    $page = \Input::get('page', 1);
    $pageLimit=\CNP::getConfigurationValue('pageLimit');

    $data = Composition::allUserCompositionsPaged($user->id, $sort, $desc, $page, $pageLimit);

    $stories = \Paginator::make($data['items'], $data['total'], $pageLimit);

    return View::make('user.contributions', array('stories' => $stories, 'sort' => $sort, 'desc' => $desc, 'user' => $user, 'person' => $person,
        'projects' => $projects));
}));

Route::get('user/{userId}/edit', array('as' => 'system.user.edit', function($userId)
{
    $user = User::find($userId);
    return View::make('user.edit', array('user' => $user, 'putUrl'=>'account.update',
                                         'system' => false));
}));

Route::put('account/update/{userId}', array('as' => 'account.update', function($userId)
{
    $data = \Input::all();
    $user = User::find($userId);
    $user->name = $data['name'];
    $user->save();
    return \Redirect::to('/user/profile');
}));

/********************************
 ** Login/Logout
 ********************************/
Route::get('/login', array('as' => 'login', function() {
    return View::make('user.login');
}));

Route::get('/logout', function() {
    Auth::logout();
    return Redirect::to('/');
});

Route::get('/loginfb', 'DemocracyApps\CNP\Controllers\LoginController@fbLogin');
Route::get('/logintw', 'DemocracyApps\CNP\Controllers\LoginController@twitLogin');
Route::get('/logincheat', 'DemocracyApps\CNP\Controllers\LoginController@cheatLogin');

Route::get('/signup', array('as' => 'signup', function () {
    return View::make('user.signup', array());
}));

Route::get('signup/thanks', array ('as' => 'signup.thanks', function () {
    return View::make('user.signup_thanks', array());
}));

Route::post('signup/thanks', array ('as' => 'signup.thanks', function () {
    return \Redirect::intended('/');
}));

Route::post('/login', array(function () {
    \Log::info("In post login");

    $app = app();
    $controller = $app->make('DemocracyApps\CNP\Controllers\LoginController');
    if (\Input::get('PW')) {
        return $controller->callAction('loginpw', $parameters = array());
    }
    else if (\Input::get('FB')) {
        \Log::info("In /login - redirecting to /loginfb");
        return \Redirect::to('/loginfb');
    }
    else if (\Input::get('TW')) {
        return \Redirect::to('/logintw');
    }
    else {
        \Redirect::to('/');
    }
}));

Route::get('confirm/ok', array ('as' => 'confirm.ok', function() {
    return \View::make('user.confirm_ok', array());
}));

Route::get('confirm/failed', array ('as' => 'confirm.failed', function() {
    return \View::make('user.confirm_failed', array());
}));

Route::get('/user/noconfirm', array(function() {
    return \View::make('user.noconfirm', array());
}));
Route::get('/confirm/resend', array(function() {
    return "Need to implement confirmation email resend.";
}));

Route::get('confirm', array(function() {
    $failed = true;
    if (\Input::has('code')) {
        $code = \Input::get('code');
        $uconfirm = UserConfirmation::where('code', '=', $code)->first();
        if ($uconfirm != null) {
            if ($uconfirm->checkCode($code)) {
                if ($uconfirm->type == 'em') {
                    $user = User::find($uconfirm->user);
                    $user->verified = true;
                    $user->save();
                    $uconfirm->done = true;
                    $uconfirm->save();
//                    UserConfirmation::remove($uconfirm->id);
                    $failed = false;
                }
            }
        }
    }
    if ($failed) {
        return \Redirect::to('confirm/failed');
    }
    else {
        return \Redirect::to('confirm/ok');
    }
}));

Route::post('/signup', array(function () {

    $app = app();
    $controller = $app->make('DemocracyApps\CNP\Controllers\LoginController');
    if (\Input::get('PW')) {
        return $controller->callAction('signuppw', $parameters = array());
    }
    else if (\Input::get('FB')) {
        return \Redirect::to('/loginfb');
    }
    else if (\Input::get('TW')) {
        return \Redirect::to('/logintw');
    }
    else {
        \Redirect::to('/');
    }
}));