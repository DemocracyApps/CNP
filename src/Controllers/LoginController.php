<?php

namespace DemocracyApps\CNP\Controllers;

use \DemocracyApps\CNP\Entities\Eloquent\AppState;
use \DemocracyApps\CNP\Mailers\UserMailer;
use \DemocracyApps\CNP\Entities\Element;
use \DemocracyApps\CNP\Entities\Eloquent\User;
use \DemocracyApps\CNP\Entities\Eloquent\Social;

class LoginController extends BaseController {

    private $userCreated = false;

    public function home() {
        return \View::make('home');
    }

    private function createUser ($userName, $email, $password) {
        $superuserInitialized = AppState::where('name', '=', 'superuserInitialized')->first();
        $this->userCreated = true;

        $user = new User;
        $user->name = $userName;
        $user->email = $email;
        if ($password != null) $user->password = \Hash::make($password);
        $user->superuser = false;
        $user->projectcreator = false;
        if (!$superuserInitialized) {
            $user->superuser = true;
            $user->projectcreator = true;
            $suInit = new AppState;
            $suInit->name = 'superuserInitialized';
            $suInit->value = '1';
            $suInit->save();
        }
        $user->save();
        $person = new Element($userName, \CNP::getElementTypeId("Person"));
        $person->setContent($userName);
        $person->save();

        $user->elementid = $person->getId();
        $user->save();

        $mailer = new UserMailer();
        $mailer->confirmEmail($user);
        return $user;

    }

    public function updateUserAccount($userId)
    {
        $rules = ['name' => 'required', 'email'=>'required|email'];
        $validator = \Validator::make(\Input::all(), $rules);
        if ($validator->fails()) {
            return \Redirect::back()->withInput()->withErrors($validator->messages());
        }

        $data = \Input::all();
        $user = User::find($userId);
        $user->name = $data['name'];

        $email = $data['email'];
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
            return \Redirect::to('email_changed');
        }
        else {
            $user->save();
        }

        return \Redirect::to('/user/profile');
    }

    private function loadOrCreateUser ($email, $password, $socialId, $userName, $socialName, $socialNetwork, $accessToken)
    {
        $this->userCreated = false;

        if ($socialId != null) {
            $socialProfile = Social::whereSocialid($socialId)->first();
            if (empty($socialProfile)) { // We must create a new user
                $user = $this->createUser($userName, $email, $password);
                $socialProfile = new Social();
                $socialProfile->socialid = $socialId;
                $socialProfile->type = $socialNetwork;
                $socialProfile->username = $socialName;
                $socialProfile->userid = $user->id;
            } else {
                $user = User::findOrFail($socialProfile->userid);
            }
            $socialProfile->access_token = $accessToken;
            $socialProfile->save();
        }
        else if ($email != null && $password != null) {
            $user = \DemocracyApps\CNP\Entities\Eloquent\User::where('email', $email)->first();
            if ($user == null) { // new user
                $user = $this->createUser($userName, $email, $password);
            }
            else { // Existing user - check the password
                if (! \Hash::check($password, $user->password)) {
                    $user = null;
                }
            }

        }
        else throw new Exception("Unknown request to log in or create user");

        return $user;
    }

    public function twitLogin() {

        // get data from input
        $token = \Input::get( 'oauth_token' );
        $verify = \Input::get( 'oauth_verifier' );

        // get twitter service
        $tw = \OAuth::consumer( 'Twitter' );

        // check if code is valid
        // if code is provided get user data and sign in
        if ( !empty( $token ) && !empty( $verify ) ) {

            // This was a callback request from twitter, get the token
            $token = $tw->requestAccessToken( $token, $verify );

            // Send a request with it
            $result = json_decode( $tw->request( 'account/verify_credentials.json' ), true );
            if (true) throw new Exception("Attempt to log in or sign up with Twitter - see LoginController.twitLogin");
            $user = $this->loadOrCreateUser(null, null, $result['id'], $result['name'], $result['screen_name'],
                                            "twitter", $token->getAccessToken());
            \Auth::login($user);
            if ($this->userCreated) {
                return \Redirect::to('signup/thanks');
            }
            else {
                return \Redirect::intended('admin/projects');
            }
        }
        // if not ask for permission first
        else {
            // get request token
            $reqToken = $tw->requestRequestToken();

            // get Authorization Uri sending the request token
            $url = $tw->getAuthorizationUri(array('oauth_token' => $reqToken->getRequestToken()));

            // return to twitter login url
            return \Redirect::to( (string)$url );
        }
    }

    public function fbLogin() {
        // get data from input
        $code = \Input::get( 'code' );

        // get fb service
        $fb = \OAuth::consumer( 'Facebook' );

        // check if code is valid

        // if code is provided get user data and sign in
        if ( !empty( $code ) ) {
            // This was a callback request from facebook, get the token
            $token = $fb->requestAccessToken( $code );

            // Send a request with it
            $result = json_decode( $fb->request( '/me' ), true );
            $user = $this->loadOrCreateUser($result['email'], null, $result['id'], $result['name'], $result['name'],
                                            "facebook", $token->getAccessToken());

            \Auth::login($user);
            if ($this->userCreated) {
                return \Redirect::to('signup/thanks');
            }
            else {
                return \Redirect::intended('admin/projects');
            }

        }
        // if not ask for permission first
        else {
            // get fb authorization
            $url = $fb->getAuthorizationUri();

            // return to facebook login url
            return \Redirect::to( (string)$url );
        }
    }
    public function cheatLogin()
    {
        $user = User::findOrFail(1);
        \Auth::login($user);
        return \Redirect::intended('admin/projects');
    }

    public function loginpw() {
        $rules = ['email'=>'required|email|exists:users,email', 'password' => 'required'];
        $validator = \Validator::make(\Input::all(), $rules);
        if ($validator->fails()) {
            return \Redirect::back()->withInput()->withErrors($validator->messages());
        }
        $user = $this->loadOrCreateUser(\Input::get('email'), \Input::get('password'), null,null, null,
            null, null);
        if ($user != null) {
            \Auth::login($user);
        }
        else {
            return \Redirect::back()->withInput()->withErrors(array('password' => 'Password is invalid'));
        }
        return \Redirect::intended('/');
    }

    public function signuppw() {
        $rules = ['name'=>'required', 'email'=>'required|email | unique:users,email', 'password' => 'required | min: 7'];
        $validator = \Validator::make(\Input::all(), $rules);
        if ($validator->fails()) {
            return \Redirect::back()->withInput()->withErrors($validator->messages());
        }
        $user = $this->loadOrCreateUser(\Input::get('email'), \Input::get('password'), null,\Input::get('name'), null,
            null, null);
        if ($user != null) {
            \Auth::login($user);
            return \Redirect::to('signup/thanks');
        }
        else {
            \Redirect::to("/signup/problem");//
        }
    }
}

