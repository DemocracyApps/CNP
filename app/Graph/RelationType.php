<?php
namespace DemocracyApps\CNP\Graph;

use DemocracyApps\CNP\Utility\AppState;
use DemocracyApps\CNP\Utility\TableBackedObject;

class RelationType extends TableBackedObject {

    /**
     * name           REQUIRED, must be unique
     * @property string
     *
     * allowedfrom    See note below, blank if any allowed
     * @property string
     *
     * allowedto      See note below, blank if any allowed
     * @property string
     *
     * inverse        If blank, relation is its own inverse,
     *                  otherwise id of inverse relation
     * @property integer
     *
     */

    static protected $tableName = 'relation_types';
    static protected $tableFields = array('name', 'allowedfrom', 'allowedto', 'inverse',
        'created_at', 'updated_at');
    public $name = null;
    public $allowedfrom = null;
    public $allowedto = null;
    public $inverse = null;
    public $created_at = null;
    public $updated_at = null;

    /**
     * @var array
     */
    public static $validationRules = ['name'=>'required'];
    /**
     * @var string $messages Error messages from validation
     */
    public $messages;

    /**
     * Initialize relation_types table with standard relation types from config file
     * @param  array $config json_decoded array of array of RelationType specs
     */
    public static function initDB($config) 
    {
        $relationTypesInitialized = AppState::whereColumnFirst('name', '=', 'relation_types');
        if ($relationTypesInitialized != null) return;

        $allRelationTypes = array();
        $rtArray = $config['relationTypes'];
        foreach ($rtArray as $rtSpec) {
            $rt = new RelationType;
            $rt->name = $rtSpec['name'];
            if (array_key_exists('allowedTo', $rtSpec)) {
                $list = explode(",", $rtSpec['allowedTo']);
                $to = null;
                foreach ($list as $item) {
                    $id = \CNP::getElementTypeId($item);
                    $to = ($to)?$to.",".$id:$id;
                }
                $rt->allowedto = $to;
            }            
            if (array_key_exists('allowedFrom', $rtSpec)) {
                $list = explode(",", $rtSpec['allowedFrom']);
                $from = null;
                foreach ($list as $item) {
                    $id = \CNP::getElementTypeId($item);
                    $from = ($from)?$from.",".$id:$id;
                }
                $rt->allowedto = $from;
            }

            $rt->save();
            $inverse = null;
            if (array_key_exists('inverse', $rtSpec)) {
                $inverse = $rtSpec['inverse'];
            }
            $allRelationTypes[$rt->name] = array('object' => $rt, 'inverse' => $inverse);
        }

        foreach ($allRelationTypes as $item) {
            if ($item['inverse']) {
                $rt = $item['object'];
                $inverse = $item['inverse'];
                $inv = $allRelationTypes[$inverse]['object'];
                $rt->inverse = $inv->id;
                $inv->inverse = $rt->id;
                $rt->save();
            }
        }
        $rtInit = new AppState;
        $rtInit->name = 'relation_types';
        $rtInit->value = '1';
        $rtInit->save();
    }

    public function getName() {
        return $this->name;
    }

    public function isValid()
    {
        $validation=\Validator::make($this->attributes, static::$validationRules);
        if ($validation->passes()) {
            return true;
        }
        $this->messages = $validation->messages();
    }

    public function initializeInverse (RelationType $obj, $nm)
    {
        $obj->name              = $nm;
        $obj->allowedfrom       = $this->allowedto;
        $obj->allowedto         = $this->allowedfrom;
        $obj->inverse           = $this->id;
    }

    public static function getRelationTypesMap ()
    {
        $map = array();
        $list = self::all();
        foreach ($list as $item) {
            $map[$item->id] = $item->name;
        }
        return $map;
    }

}
