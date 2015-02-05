<?php
namespace DemocracyApps\CNP\Utility;

class AppState extends TableBackedObject {
	static  $tableName = 'app_state';
	static protected $tableFields = array('name', 'value',
		'created_at', 'updated_at');
	public $name = null;
	public $value = null;
	public $created_at = null;
	public $updated_at = null;

}
