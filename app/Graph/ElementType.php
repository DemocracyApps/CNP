<?php

namespace DemocracyApps\CNP\Graph;


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
        $etArray = $config['elementTypes'];
        foreach ($etArray as $etSpec) {
            $et = new ElementType;
            $et->name = $etSpec['name'];
            $et->save();
        }
    }

    public function getName() {
        return $this->name;
    }

    public function getId()
    {
        return $this->id;
    }


    public function isValid()
    {
        $validation=\Validator::make($this->attributes, static::$validationRules);
        if ($validation->passes()) {
            return true;
        }
        $this->messages = $validation->messages();
    }

}