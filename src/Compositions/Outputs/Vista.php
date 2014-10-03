<?php
namespace DemocracyApps\CNP\Compositions\Outputs;
use \DemocracyApps\CNP\Entities as DAEntity;

class Vista extends \Eloquent
{
    protected $table = 'vistas';
    protected static $tableName = 'vistas';
    protected $allowedComposers = null;

    private function initialize()
    {
        $this->allowedComposers = explode(",", $this->input_composers);

    }
    public static function getUserVistas($userId)
    {
        $records = \DB::table(self::$tableName)
                    ->join('elements', 'vistas.scape', '=', 'elements.id')
                    ->where('elements.userid', '=', $userId)
                    ->select('vistas.id', 'vistas.name', 'vistas.scape', 'vistas.description',
                             'vistas.input_composers', 'vistas.output_composer', 'vistas.selector')
                    ->orderBy('vistas.scape', 'vistas.id')
                    ->distinct()
                    ->get();
        $result = array();
        foreach($records as $record)
        {
            $item = new static();
            self::fillData($item, $record);
            $result[] = $item;
        }
        return $result;
    }

    protected static function fillData($instance, $data)
    {
        $instance->{'id'} = $data->id;
        $instance->{'name'} = $data->name;
        $instance->{'scape'} = $data->scape;

        if (property_exists($data, 'description')) {
            $instance->{'description'} = $data->description;
        }

        if (property_exists($data, 'selector')) {
            $instance->{'selector'} = $data->selector;
        }

        if (property_exists($data, 'input_composers')) {
            $instance->{'input_composers'} = $data->input_composers;
        }
        if (property_exists($data, 'output_composer')) {
            $instance->{'output_composer'} = $data->output_composer;
        }
    }

    public function extractCompositions ($element)
    {
        if (! $this->allowedComposers) $this->initialize();
        $result = array();
        $relations = $element->getRelations();
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
     * We get element and we want to figure out what role(s) it plays in the composer
     * spec.
     */
    public function extractComposerRoles($element)
    {
        if (! $this->allowedComposers) $this->initialize();
        $result = array();
        $relations = $element->getRelations();
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

