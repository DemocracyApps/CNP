<?php namespace DemocracyApps\CNP\Utility\Mailers;


use DemocracyApps\CNP\Users\User;
use DemocracyApps\CNP\Users\UserConfirmation;

class UserMailer extends Mailer {

    public function confirmEmail (User $user) {

        $confirmation = new UserConfirmation();
        $confirmation->initialize($user, 'em', 24);
        $data = array ('code' => $confirmation->getCode());
        $this->sendTo($user, "Confirm your email at Community Narratives Platform", 'emails.confirmEmail', $data);
    }

}