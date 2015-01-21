<?php

namespace DemocracyApps\CNP\Mailers;


class UserMailer extends Mailer {

    public function confirmEmail (\DemocracyApps\CNP\Entities\Eloquent\User $user) {

        $confirmation = new \DemocracyApps\CNP\Entities\Eloquent\UserConfirmation();
        $confirmation->initialize($user, 'em', 24);
        $data = array ('code' => $confirmation->getCode());
        $this->sendTo($user, "Confirm your email at Community Narratives Platform", 'emails.confirmEmail', $data);
    }

}