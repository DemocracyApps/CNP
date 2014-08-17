<?php
namespace DemocracyApps\CNP\Models;

class Person extends Denizen 
{
    static    $classScapeId = -1;
    static $userDenizenType = 0;
    protected $user;

    function __construct ($nm) {
        parent::__construct($nm, static::$classScapeId, 0);
    }

    static public function initialize() 
    {
        if (static::$classScapeId < 0) {
            static::$classScapeId = \CNP::getDenizenTypeId('People');
        }
    }
}
