<?php namespace DemocracyApps\CNP\Analysis;

use \DemocracyApps\CNP\Database\TableBackedObject;

class Perspective extends TableBackedObject {
    static protected $tableName = 'perspectives';
    static protected $tableFields = array('name', 'type', 'project', 'specification', 'description', 'requires_analysis',
                                          'last', 'created_at', 'updated_at');

    public $id;
    public $name;
    public $type;
    public $project;
    public $specification;
    public $description;
    public $requires_analysis;
    public $last;
    public $created_at;
    public $updated_at;

    public function getContent ()
    {
        return "<p><br> <b>This is it!!!</b></p>";

    }

}