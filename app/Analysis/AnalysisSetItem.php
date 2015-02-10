<?php

namespace DemocracyApps\CNP\Analysis;

use \DemocracyApps\CNP\Utility\TableBackedObject;

class AnalysisSetItem extends TableBackedObject {
    static protected $tableName = 'analysis_set_items';
    static protected $tableFields = array('id', 'analysis_set', 'item', 'created_at', 'updated_at');

    public $id;
    public $analysis_set;
    public $item;
    public $created_at;
    public $updated_at;

}