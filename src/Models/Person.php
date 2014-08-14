<?php
namespace DemocracyApps\CNP\Models;

class Person extends Denizen 
{
    static $userDenizenType = 0;
    protected $user;

    function __construct ($nm) {
        parent::__construct($nm, \CNP::getScapeId('People'), 0);
    }

}
