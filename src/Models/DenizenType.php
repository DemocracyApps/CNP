<?php

namespace DemocracyApps\CNP\Models;

class DenizenType extends ModelBase
{
    protected $id = null;
    protected $name = null;

    function __construct ($id, $nm) 
    {
        $this->id = $id;
        $this->name = $nm;
    }

    public function getId() {
        return strval($this->id);
    }

    public function getName() {
        return $this->name;
    }
}

