<?php

namespace DemocracyApps\CNP\Graph;


use DemocracyApps\CNP\Utility\AppState;
use DemocracyApps\CNP\Utility\TableBackedObject;

class ElementType extends TableBackedObject {
    static  $tableName = 'element_types';
    static protected $tableFields = array('name',
        'created_at', 'updated_at');

    public $name = null;
    public $created_at = null;
    public $updated_at = null;

    protected $fillable = ['name'];
    /**
     * @var array
     */
    public static $validationRules = ['name'=>'required'];
    /**
     * @var string $messages Error messages from validation
     */
    public $messages;

    /**
     * Initialize table with standard element types from config file
     * @param  array $config json_decoded array ElementType names
     */
    public static function initDB($config)
    {
        $elementTypesInitialized = AppState::whereColumnFirst('name', '=', 'element_types');
        if ($elementTypesInitialized != null) return;

        $etArray = $config['elementTypes'];

        foreach ($etArray as $etSpec) {
            $et = new ElementType;
            $et->name = $etSpec['name'];
            $et->save();
        }
        $etInit = new AppState;
        $etInit->name = 'element_types';
        $etInit->value = '1';
        $etInit->save();
    }

    public function getName() {
        return $this->name;
    }

    public function getId()
    {
        return $this->id;
    }

}