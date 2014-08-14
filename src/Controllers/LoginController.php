<?php

namespace DemocracyApps\CNP\Controllers;

class LoginController extends BaseController {

    public function login() {
        return \View::make('login');
    }

    public function home() {
        return \View::make('home');
    }

    private function loadOrCreateUser ($socialId, $userName, $socialName, $socialNetwork, $accessToken) 
    {
        $socialProfile = \DemocracyApps\CNP\Models\Eloquent\Social::whereSocialid($socialId)->first();
        if (empty($socialProfile)) { // We must create a new user
            $person = new \DemocracyApps\CNP\Models\Person($userName);
            $person->save();
            
            $user = new \DemocracyApps\CNP\Models\Eloquent\User;
            $user->name = $userName;
            $user->denizenid = $person->getId();
            $user->save();

            $socialProfile = new \DemocracyApps\CNP\Models\Eloquent\Social();
            $socialProfile->socialid = $socialId;
            $socialProfile->type=$socialNetwork;
            $socialProfile->username = $socialName;
            $socialProfile->userid = $user->id;
            \Log::info("Created a new user with id ".$user->id);
        }
        else {
            $user = \DemocracyApps\CNP\Models\Eloquent\User::findOrFail($socialProfile->userid)->first();
            \Log::info("Found existing user with id ".$user->id);
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
            return \Redirect::to('/home');
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
            return \Redirect::to('/home');
        }
        // if not ask for permission first
        else {
            // get fb authorization
            $url = $fb->getAuthorizationUri();

            // return to facebook login url
            return \Redirect::to( (string)$url );
        }
    }
}

