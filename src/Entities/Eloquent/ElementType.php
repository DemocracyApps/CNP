<?php

namespace DemocracyApps\CNP\Entities\Eloquent;


class ElementType extends \Eloquent {

    protected $table = 'element_types';
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