<?php

namespace DemocracyApps\CNP\Users;


class UserConfirmation extends \Eloquent {

    public function initialize (User $user, $type, $hours) {
        $this->user = $user->getId();
        $this->type = $type;
        $this->expires = date('Y-m-d H:i:s', time() + $hours * 60 * 60);
        $this->code = uniqid($type . ".", true);
        $this->save();
    }

    public function getCode () {
        return $this->code;
    }

    public function checkCode($code) {
        $ok = false;
        if (time() < strtotime($this->expires)) {
            if (!$this->done && $code == $this->code) $ok = true;
        }
        return $ok;
    }

    public static function remove($id) {
        \DB::table('user_confirmations')->where('id','=',$id)->delete();
    }
}