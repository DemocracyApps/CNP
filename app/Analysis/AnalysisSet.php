<?php


namespace DemocracyApps\CNP\Analysis;

use \DemocracyApps\CNP\Utility\TableBackedObject;

class AnalysisSet extends TableBackedObject {
    static protected $tableName = 'analysis_sets';
    static protected $tableFields = array('id', 'analysis_output', 'description',
                                            'created_at', 'updated_at');

    public $id;
    public $analysis_output;
    public $description;
    public $created_at;
    public $updated_at;


}