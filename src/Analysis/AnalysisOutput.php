<?php

namespace DemocracyApps\CNP\Analysis;

use \DemocracyApps\CNP\Database\TableBackedObject;

class AnalysisOutput extends TableBackedObject {
    static protected $tableName = 'analysis_outputs';
    static protected $tableFields = array('id', 'perspective', 'project', 'output', 'created_at', 'updated_at');

    public $id;
    public $perspective;
    public $project;
    public $output;
    public $created_at;
    public $updated_at;


}