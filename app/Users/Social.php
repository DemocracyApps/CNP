<?php

namespace DemocracyApps\CNP\Users;

use \DemocracyApps\CNP\Utility\TableBackedObject;


class Social extends TableBackedObject {
    static  $tableName = 'socials';
    static protected $tableFields = array('type', 'userid', 'socialid',
                                            'username', 'access_token',
                                            'created_at', 'updated_at');

    public  $id = null;
    public  $type = null;// Network type: facebook, twitter, linkedin, etc.
    public  $userid = -1;
    public  $socialid = null;
    public  $username = null;
    public  $access_token = null;
    public  $created_at = null;
    public  $updated_at = null;

}