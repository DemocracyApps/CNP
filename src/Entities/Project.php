<?php

namespace DemocracyApps\CNP\Entities;

/**
 * 
 */
class Project extends \Eloquent
{
    protected $table = 'projects';

    protected $properties = null;

    public function save(array $options = array()) 
    {
        if ($this->properties) {
            $this->json_properties = json_encode($this->properties); // How do I move this to ImplementsProperties trait?
        }
        else {
            $this->json_properties = null;
        }
        parent::save($options);
    }

    protected function checkProperties() 
    {
        if ($this->json_properties && ! $this->properties) {
            $this->properties = (array) json_decode($this->json_properties);
        }
    }

    public function setProperty ($propName, $propValue)
    {
        $this->checkProperties();
        if (! $this->properties) $this->properties = [];
        $this->properties[$propName] = $propValue;
    }

    public function hasProperty ($propName) 
    {
        $this->checkProperties();
        $hasProperty = false;
        if ($this->properties) {
            if (array_key_exists($propName, $this->properties)) {
                $hasProperty = true;
            }
        }
        return $hasProperty;
    }

    public function deleteProperty ($propName) 
    {
        if ($this->hasProperty($propName)) {
            unset($this->properties[$propName]);
        }
    }

    public function getProperty ($propName)
    {
        $this->checkProperties();
        $propValue = null;
        if ($this->properties) {
            $propValue = $this->properties[$propName];
        }
        return $propValue;
    }    

    public static function allUserProjects($user)
    {
        return self::where('userid', '=', $user)->get();
    }
}


