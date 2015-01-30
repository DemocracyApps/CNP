<?php
/**
 * Created by PhpStorm.
 * User: ericjackson
 * Date: 1/30/15
 * Time: 2:33 PM
 */

namespace Analysis;


class AnalysisOutput extends TableBackedObject {
    static protected $tableName = 'analysis_outputs';
    static protected $tableFields = array('id', 'analysis', 'project', 'output', 'created_at', 'updated_at');

    public $id;
    public $analysis;
    public $project;
    public $output;
    public $created_at;
    public $updated_at;


}