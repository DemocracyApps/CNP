<?php
namespace DemocracyApps\CNP\Compositions\Outputs;
use \DemocracyApps\CNP\Entities as DAEntity;

class Vista extends \Eloquent
{
    protected $table = 'vistas';
    protected $allowedComposers = null;

    private function initialize()
    {
        $this->allowedComposers = explode(",", $this->input_composers);

    }

    public function extractCompositions ($denizen)
    {
        if (! $this->allowedComposers) $this->initialize();
        $result = array();
        $relations = $denizen->getRelations();
        foreach($relations as $relation) {
            if (in_array($relation->composerid, $this->allowedComposers, false)) {
                if ($relation->compositionid) {
                    if (array_key_exists($relation->compositionid, $result)) {
                        ++$result[$relation->compositionid];
                    }
                    else {
                        $result[$relation->compositionid] = 1;
                    }
                }
            }
        }
        return $result;        
    }

    /*
     * We get denizen and we want to figure out what role(s) it plays in the composer
     * spec.
     */
    public function extractComposerRoles($denizen)
    {
        if (! $this->allowedComposers) $this->initialize();
        $result = array();
        $relations = $denizen->getRelations();
        foreach($relations as $relation) {
            if (in_array($relation->composerid, $this->allowedComposers, false)) {
                $roleProp = $relation->getProperty('composerElements');
                if ($roleProp) {
                    $role = explode(',',$roleProp)[0];
                    if ($role) {
                        if (array_key_exists($role, $result)) {
                            ++$result[$role];
                        }
                        else {
                            $result[$role] = 1;
                        }
                    }
                }
            }
        }
        return $result;
    }
}

