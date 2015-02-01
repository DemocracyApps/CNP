<?php namespace DemocracyApps\CNP\Analysis;

use Illuminate\Support\Facades\DB;
use \DemocracyApps\CNP\Database\TableBackedObject;

class Perspective extends TableBackedObject {
    static protected $tableName = 'perspectives';
    static protected $tableFields = array('name', 'type', 'project', 'specification', 'notes', 'requires_analysis',
                                          'last', 'created_at', 'updated_at');

    public $id;
    public $name;
    public $type;
    public $project;
    public $specification;
    public $notes;
    public $requires_analysis;
    public $last;
    public $created_at;
    public $updated_at;


}