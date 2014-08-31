<?php
namespace DemocracyApps\CNP\Entities;

class Story extends Denizen 
{
    static    $classDenizenType = -1;
    static $storyDenizenType = 0;
    protected $author;

    function __construct ($nm=null) {
        parent::__construct($nm, static::$classDenizenType);
    }

    static public function initialize() 
    {
        if (static::$classDenizenType < 0) {
            static::$classDenizenType = \CNP::getDenizenTypeId('Story');
        }
    }

}
