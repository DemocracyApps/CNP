<?php
namespace DemocracyApps\CNP\Entities;

class Person extends Denizen 
{
    static    $classDenizenType = -1;
    protected $user;

    function __construct ($nm, $userid) {
        parent::__construct($nm, $userid, static::$classDenizenType);
    }

    static public function initialize() 
    {
        if (static::$classDenizenType < 0) {
            static::$classDenizenType = \CNP::getDenizenTypeId('Person');
        }
    }
}
