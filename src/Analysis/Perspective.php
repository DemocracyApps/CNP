<?php namespace DemocracyApps\CNP\Analysis;

use Illuminate\Support\Facades\DB;
use \DemocracyApps\CNP\Database\TableBackedObject;

class Perspective extends TableBackedObject {
    static protected $tableName = 'analyses';
    static protected $tableFields = array('name', 'project', 'specification', 'notes',
                                          'last', 'created_at', 'updated_at');

    public $id;
    public $name;
    public $project;
    public $specification;
    public $notes;
    public $last;
    public $created_at;
    public $updated_at;


}