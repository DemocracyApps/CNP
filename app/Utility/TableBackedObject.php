<?php namespace DemocracyApps\CNP\Utility;

use Illuminate\Support\Facades\DB;

abstract class TableBackedObject {
    public $id = null;
    public static function getTableName() {
        return self::$tableName;
    }

    public static function deleteById ($id) {
        DB::table(static::$tableName)->where('id', '=', $id)->delete();
    }

    public function delete()
    {
        DB::table(static::$tableName)->where('id', '=', $this->id)->delete();
    }
    /**
     * Find a element by its primary key
     */
    public static function find ($id)
    {
        $data = DB::table(static::$tableName)->where('id', $id)->first();
        $result = null;
        if ($data != null && sizeof($data) > 0) {
            $result = new static();
            self::fill($result, $data);
        }
        return $result;
    }

    public static function all()
    {
        $records = DB::table(static::$tableName)->orderBy('id')->get();
        $result = array();

        foreach ($records as $record) {
            $item = new static();
            self::fill($item,$record);
            $result[] = $item;
        }
        return $result;
    }

    public function save()
    {
        if ($this->id == null) {
            $this->created_at = date('Y-m-d H:i:s');
            $this->updated_at = date('Y-m-d H:i:s');
        }
        else {
            $this->updated_at = date('Y-m-d H:i:s');
        }

        $a = array();
        foreach (static::$tableFields as $field) {
            $a[$field] = $this->{$field};
        }

        if ($this->id == null) {
            $this->id = DB::table(static::$tableName)->insertGetId($a);
        }
        else {
            DB::table(static::$tableName)
                ->where('id',$this->id)
                ->update($a);
        }
    }

    protected static function fill ($instance, $data)
    {
        $instance->{'id'} = $data->id;
        foreach(static::$tableFields as $field) {
            $instance->{$field} = $data->{$field};
        }
    }

    public static function whereColumn($columnName, $compare, $value)
    {
        $records =  DB::table(static::$tableName)
            ->where ($columnName, $compare, $value)-> orderBy ('id') -> get();
        $result = array();

        foreach ($records as $record) {
            $item = new static();
            self::fill($item,$record);
            $result[] = $item;
        }
        return $result;
    }

    public static function whereColumnFirst($columnName, $compare, $value)
    {
        $records =  DB::table(static::$tableName)
            ->where ($columnName, $compare, $value)-> orderBy ('id') -> get();
        $item = null;
        if ($records != null && sizeof($records > 0)) {
            $record = $records[0];
            $item = new static();
            self::fill($item, $record);
        }
        return $item;
    }

    protected static function fillArray ($records)
    {
        $result = array();
        foreach ($records as $record) {
            $item = new static();
            self::fill($item,$record);
            $result[] = $item;
        }
        return $result;
    }
}