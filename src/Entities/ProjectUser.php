<?php namespace DemocracyApps\CNP\Entities;

use Illuminate\Support\Facades\DB;


class ProjectUser {
    static  $tableName = 'project_users';

    public  $id = null;
    public  $project = -1;
    public  $user = -1;
    public  $access = -1;

    public static function getTableName() {
        return self::$tableName;
    }

    public function save()
    {
        if ($this->id == null) {
            $this->id = DB::table(self::$tableName)->insertGetId(
                array(
                    'project' => $this->project,
                    'user' => $this->user,
                    'access' => $this->access,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                )
            );
        }
        else {
            DB::table(self::$tableName)
                ->where('id',$this->id)
                ->update(
                    array(
                        'project' => $this->project,
                        'user' => $this->user,
                        'access' => $this->access,
                        'updated_at' => date('Y-m-d H:i:s')
                    )
                );
        }
    }

    protected static function fill ($instance, $data)
    {
        $instance->{'id'} = $data->id;
        $instance->{'project'} = $data->project;
        $instance->{'user'} = $data->user;
        $instance->{'access'} = $data->level;
    }

    /**
     * Find a element by its primary key
     */
    public static function find ($id)
    {
        $data = DB::table(self::$tableName)
            ->where('id', $id)->first();
        $result = null;
        if ($data != null) {
            $result = new static();
            self::fill($result, $data);
        }
        return $result;
    }

    public static function lookup($project, $user)
    {
        return DB::table(self::$tableName)
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