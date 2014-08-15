<?php
namespace DemocracyApps\CNP\Models;

class Story extends Denizen 
{
    static $storyDenizenType = 0;
    protected $author;

    function __construct ($nm) {
        parent::__construct($nm, \CNP::getScapeId('Story'), $storyDenizenType);
    }

}
