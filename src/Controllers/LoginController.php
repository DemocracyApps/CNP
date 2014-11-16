<?php

namespace DemocracyApps\CNP\Controllers;

use \DemocracyApps\CNP\Entities\Eloquent\AppState;


class LoginController extends BaseController {

    public function login() {
        return \View::make('login');
    }

    public function home() {
        return \View::make('home');
    }

    private function loadOrCreateUser ($socialId, $userName, $socialName, $socialNetwork, $accessToken) 
    {
        $socialProfile = \DemocracyApps\CNP\Entities\Eloquent\Social::whereSocialid($socialId)->first();
        if (empty($socialProfile)) { // We must create a new user
            $superuserInitialized = AppState::where('name','=','superuserInitialized')->get()->first();

            $user = new \DemocracyApps\CNP\Entities\Eloquent\User;
            $user->name = $userName;
            $user->superuser = false;
            $user->projectcreator=false;
            if (! $superuserInitialized) {
                $user->superuser = true;
                $user->projectcreator=true;
                $suInit = new \DemocracyApps\CNP\Entities\Eloquent\AppState;
                $suInit->name = 'superuserInitialized';
                $suInit->value = '1';
                $suInit->save();
            }
            $user->save();
            \Log::info("Got a user id of " . $user->getId());
            $person = new \DemocracyApps\CNP\Entities\Person($userName, $user->getId());
            $person->setContent($userName);
            $person->save();
            \Log::info("Got a person id of " . $person->getId());

            $user->elementid = $person->getId();
            $user->save();

            $socialProfile = new \DemocracyApps\CNP\Entities\Eloquent\Social();
            $socialProfile->socialid = $socialId;
            $socialProfile->type=$socialNetwork;
            $socialProfile->username = $socialName;
            $socialProfile->userid = $user->id;
        }
        else {
            $user = \DemocracyApps\CNP\Entities\Eloquent\User::findOrFail($socialProfile->userid)->first();
        }
        $socialProfile->access_token = $accessToken;
        $socialProfile->save();
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
            $user = $this->loadOrCreateUser($result['id'], $result['name'], $result['screen_name'],
                                            "twitter", $token->getAccessToken());
            \Auth::login($user);
            return \Redirect::to('projects');
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

            $user = $this->loadOrCreateUser($result['id'], $result['name'], $result['name'],
                                            "facebook", $token->getAccessToken());
            \Auth::login($user);
            return \Redirect::intended('projects');
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
        $user = \DemocracyApps\CNP\Entities\Eloquent\User::findOrFail(1)->first();
        \Auth::login($user);
        return \Redirect::intended('projects');
    }
}

