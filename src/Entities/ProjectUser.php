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

    public static function projectAccess ($project, $user) {
        $access = 0;
        $data = DB::table(self::$tableName) -> where ('project', $project) -> where ('user', '=', $user) -> first();
        if ($data != null) {
            $access = $data->access;
        }
        return $access;
    }

}