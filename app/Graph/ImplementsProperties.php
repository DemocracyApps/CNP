<?php
namespace DemocracyApps\CNP\Graph;

trait ImplementsProperties
{
    public $properties = null;

    public function setProperty ($propName, $propValue)
    {
        if (! $this->properties) $this->properties = [];
        $this->properties[$propName] = $propValue;
    }

    public function hasProperty ($propName) 
    {
        $hasProperty = false;
        if ($this->properties) {
            if (array_key_exists($propName, $this->properties)) {
                $hasProperty = true;
            }
        }
        return $hasProperty;
    }

    public function getProperty ($propName)
    {
        $propValue = null;
        if ($this->properties) {
            $propValue = $this->properties[$propName];
        }
        return $propValue;
    }    
}