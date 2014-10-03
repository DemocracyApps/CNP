<?php
namespace DemocracyApps\CNP\Utility;
use DemocracyApps\CNP\Entities as Cnpm;

use Illuminate\Container\Container;    

class Cnp {
    private $configuration = null;

    protected $elementTypesByName = array();
    protected $elementTypesById = array();

    /**
     * Container instance used to resolve classes.
     *
     * @var Illuminate\Container\Container
     */
    protected $app;

    /**
     * Allow a container instance to be set via constructor.
     *
     * @param mixed $app
     */
    public function __construct($app = null)
    {
        // If the container isn't provided...
        if (!$app instanceof Container) {
            // ... use an instance of the illuminate container.
            $app = new Container;
        }
        // Set the app property.
        $this->app = $app;
    }

    /**
     * Load values from the cnp.json configuration file
     * 
     */
    public function loadConfiguration ($cfg) {
        $this->configuration = $cfg;

        // Load information on ElementTypes
        $arr = $cfg['elementTypes'];
        foreach ($arr as $elementTypeSpec) {
            $elementType = new Cnpm\ElementType ($elementTypeSpec['id'], $elementTypeSpec['name']);
            $this->elementTypesByName[strtolower($elementType->getName())] = $elementType;
            $this->elementTypesById[$elementType->getId()] = $elementType;
        }
    }

    /**
     *
     * ElementType-related functions
     *
     */
    public function getElementTypeId ($name) {
        $nm = strtolower($name);
        if (array_key_exists($nm, $this->elementTypesByName)) {
            return $this->elementTypesByName[$nm]->getId();
        }
        throw new \OutOfBoundsException('Unknown Element Type ' . $name);
    }

    public function getElementTypeName ($id) {
        if (array_key_exists($id, $this->elementTypesById)) {
            return $this->elementTypesById[$id]->getName();
        }
        throw new \OutOfBoundsException('Unknown Element Type ' . $name);
    }

    public function getElementTypes ($asHash)
    {
        $result = array();
        foreach ($this->elementTypesById as $type) {
            if ($asHash) {
                $result[$type->id] = $type->name;
            }
            else {
                $result[] = $type->name;
            }
        }
        return $result;
    }

    /**
     * @string $name [Parameter name]
     *
     * @return  string or array
     */
    public function getConfigurationValue ($name) 
    {
        return $this->configuration[$name];
    }
}
