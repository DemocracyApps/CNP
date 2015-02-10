<?php namespace DemocracyApps\CNP\Utility;


use DemocracyApps\CNP\Graph\ElementType;
use DemocracyApps\CNP\Graph\RelationType;
use DemocracyApps\CNP\Services\JsonProcessor;

class CnpConfiguration {
    private $initialized = false;

    protected $configuration = null;
    protected $elementTypesByName = null;
    protected $elementTypesById = null;

    private $jsonProcessor = null;

    public function __construct (JsonProcessor $jp) {
        $this->jsonProcessor = $jp;
        \Log::info("Constructing cnpconfig");
    }

    /**
     * @return bool
     */
    private function initialize ()
    {
        \Log::info("In initialize");
        if (! $this->initialized) {
            $fileName = base_path() . "/app/cnp.json";
            $str = file_get_contents($fileName);
            //$str = json_minify($str);
            $str = $this->jsonProcessor->minifyJson($str);
            $this->configuration = $this->jsonProcessor->decodeJson($str, true);
            $this->initialized = true;

            $picStorage = AppState::whereColumnFirst('name', '=', 'pictureStorage');
            if (! $picStorage) {
                \Log::info("Initializing picture storage");
                $picStorage = new AppState;
                $picStorage->name = 'pictureStorage';
                $picStorage->value = 'S3&cnptest';
                $picStorage->save();
            }
        }
        return $this->initialized;
    }

    public function getConfiguration() {
        if ($this->initialize()) {
            return $this->configuration;
        }
    }

    public function getJsonProcessor() {
        return $this->jsonProcessor;
    }

    public function getConfigurationValue ($name)
    {
        $value = null;
        if ($this->initialize()) {
            if (array_key_exists($name, $this->configuration)) {
                $value = $this->configuration[$name];
            }
        }
        return $value;
    }

    /**
     *
     * ElementType-related functions
     *
     */

    private function initializeElementTypes() {
        $this->initialize();
        ElementType::initDB($this->configuration);
        $this->elementTypesById = array();
        $this->elementTypesByName = array();
        $et = ElementType::all();
        foreach ($et as $eItem) {
            $this->elementTypesByName[$eItem->name] = $eItem;
            $this->elementTypesById[$eItem->id] = $eItem;
        }
        //dd($this->elementTypesByName);
        RelationType::initDB($this->configuration);

    }
    public function getElementTypeId ($name) {
        if ($this->elementTypesByName === null) {
            $this->initializeElementTypes();
        }
        $nm= $name;

        if (array_key_exists($nm, $this->elementTypesByName)) {
            return $this->elementTypesByName[$nm]->getId();
        }
        throw new \OutOfBoundsException('Unknown Element Type ' . $name);
    }

    public function getElementTypeName ($id) {
        if ($this->elementTypesByName === null) {
            $this->initializeElementTypes();
        }

        if (array_key_exists($id, $this->elementTypesById)) {
            return $this->elementTypesById[$id]->getName();
        }
        throw new \OutOfBoundsException('Unknown Element Type ' . $name);
    }

    public function getElementTypes ($asHash)
    {
        if ($this->elementTypesByName === null) {
            $this->initializeElementTypes();
        }
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


}