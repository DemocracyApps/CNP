<?php
namespace DemocracyApps\CNP\Utility;
use DemocracyApps\CNP\Entities as Cnpm;

use Illuminate\Container\Container;    

class Cnp {
    private $configuration = null;

    protected $denizenTypesByName = array();
    protected $denizenTypesById = array();

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

        // Load information on DenizenTypes
        $arr = $cfg['denizenTypes'];
        foreach ($arr as $denizenTypeSpec) {
            $denizenType = new Cnpm\DenizenType ($denizenTypeSpec['id'], $denizenTypeSpec['name']);
            $this->denizenTypesByName[strtolower($denizenType->getName())] = $denizenType;
            $this->denizenTypesById[$denizenType->getId()] = $denizenType;
        }
    }

    /**
     *
     * DenizenType-related functions
     *
     */
    public function getDenizenTypeId ($name) {
        $nm = strtolower($name);
        if (array_key_exists($nm, $this->denizenTypesByName)) {
            return $this->denizenTypesByName[$nm]->getId();
        }
        throw new \OutOfBoundsException('Unknown Denizen Type ' . $name);
    }

    public function getDenizenTypeName ($id) {
        if (array_key_exists($id, $this->denizenTypesById)) {
            return $this->denizenTypesById[$id]->getName();
        }
        throw new \OutOfBoundsException('Unknown Denizen Type ' . $name);
    }
}
