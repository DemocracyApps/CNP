<?php

namespace DemocracyApps\CNP\Users;


use DemocracyApps\CNP\Utility\TableBackedObject;

class UserConfirmation extends TableBackedObject {
    static  $tableName = 'user_confirmations';
    static protected $tableFields = array('user', 'type', 'code',
        'expires', 'done',
        'created_at', 'updated_at');

    public  $id = null;
    public  $user = null;
    public  $type = null;
    public  $code = null;
    public  $expires = null;
    public  $done = null;
    public  $created_at = null;
    public  $updated_at = null;

    public function initialize (User $user, $type, $hours) {
        $this->user = $user->id;
        $this->type = $type;
        $this->expires = date('Y-m-d H:i:s', time() + $hours * 60 * 60);
        $this->code = uniqid($type . ".", true);
        $this->done = false;
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