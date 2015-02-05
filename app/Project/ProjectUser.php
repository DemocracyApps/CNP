<?php namespace DemocracyApps\CNP\Project;

use Illuminate\Support\Facades\DB;
use \DemocracyApps\CNP\Utility\TableBackedObject;


class ProjectUser extends TableBackedObject {
    static  $tableName = 'project_users';
    static protected $tableFields = array('project', 'user', 'access',
                                          'created_at', 'updated_at');

    public  $id = null;
    public  $project = -1;
    public  $user = -1;
    public  $access = -1;

    public static function lookup($project, $user)
    {
        return DB::table(static::$tableName)
            ->where ('project', $project) -> where('user', '=', $user)-> first();
    }

    private static function projectAccess ($project, $user) {
        $access = 0;
        $data = ProjectUser::lookup($project, $user);
        if ($data != null) {
            $access = $data->access;
        }
        return $access;
    }

    public static function projectAdminAccess ($project, $user) {
        $access = self::projectAccess($project, $user);
        return ($access == 3);
    }

    public static function projectPostAccess ($project, $user) {
        $access = self::projectAccess($project, $user);
        return ($access >= 2);
    }

    public static function projectViewAccess ($project, $user) {
        $access = self::projectAccess($project, $user);
        return ($access >= 1);
    }

    public static function authorizePostAccess($project, $user)
    {
        $record = ProjectUser::lookup($project, $user);
        if ($record != null) {
            if ($record->access < 2) $record->access = 2;
        }
        else {
            $record = new ProjectUser();
            $record->project = $project;
            $record->user = $user;
            $record->access = 2;
            $record->save();
        }
    }

}