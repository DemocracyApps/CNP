<?php

namespace DemocracyApps\CNP\Entities;

class ElementType
{
    public $id = null;
    public $name = null;

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

