<?php
namespace DemocracyApps\CNP\Entities;
use Illuminate\Support\Facades\DB as DB;

class Relation 
{
    use ImplementsProperties;
    static $tableName = 'relations';
    static $relTypesTableName = 'relation_types';
    public $id = null;
    public $fromId = null;
    public $toId = null;
    public $relationId = null;
    public $composerid = null;

    function __construct($from, $to, $type) {
        $this->fromId = $from;
        $this->toId   = $to;
        $this->relationId = $type;
    }

    protected static function fill ($instance, $data) 
    {
        $instance->{'id'} = $data->id;
        $instance->{'fromId'} = $data->fromid;
        $instance->{'toId'} = $data->toid;
        $instance->{'relationId'} = $data->relationid;
        $instance->{'properties'} = (array) json_decode($data->properties);
        $instance->{'composerid'} = $data->composerid;
    }

    public function setComposerId ($id)
    {
        $this->composerid = $id;
    }

    public static function getScapeRelations ($scape)
    {
        $records = DB::table(self::$tableName)
                    ->join('denizens', 'denizens.id', '=', 'relations.fromid')
                    ->where('denizens.scape', '=', $scape)
                    ->select('relations.id', 'relations.fromid', 'relations.toid', 'relations.relationid', 
                             'relations.properties', 'relations.composerid')
                    ->get();
        $relations = array();

        foreach ($records as $rec) {
            $item = new static($rec->fromid, $rec->toid, $rec->relationid);
            self::fill($item,$rec);
            $relations[] = $item;
        }
        return $relations;
    }

    public static function getRelatedDenizens ($fromId, $relationName) 
    {
        $relId = null;
        if ($relationName) {
            $relRecord = DB::table(self::$relTypesTableName)->where('name',$relationName)->first();
            if ($relRecord) $relId = $relRecord->{'id'};
        }
        if ($relId) {
            $d = DB::table(self::$tableName)->where('fromid', $fromId)->where('relationid', $relId)->get();
        }
        else {
            $d = DB::table(self::$tableName)->where('fromid', $fromId)->get();
        }

        $denizens = array();

        foreach ($d as $data) {
            $denizen = Denizen::find($data->toid);
            $denizens[] = $denizen;
        }

        return $denizens;
    }

    public static function getRelations ($fromId) 
    {
        $d = DB::table(self::$tableName)->where('fromid', $fromId)->get();

        $relations = array();

        foreach ($d as $data) {
            $item = new static($data->fromid, $data->toid, $data->relationid);
            self::fill($item,$data);
            $relations[] = $item;
        }

        return $relations;
    }

    public static function createRelationPair($fromId, $toId, $relationName, $props1 = null, $props2=null) 
    {
        $relation = array();
        $relRecord = DB::table(self::$relTypesTableName)->where('name',$relationName)->first();
        $relation[] = new static ($fromId, $toId, $relRecord->{'id'});
        if ($props1) $relation[0]->properties = $props1;
        $inverse = $relRecord->{'inverse'}?$relRecord->{'inverse'}:$relRecord->{'id'};
        $relation[] = new static ($toId, $fromId, $inverse);
        if ($props2) $relation[1]->properties = $props2;
        return $relation;
    }

    public static function getInverseRelationName ($relationName) {
        $relRecord = DB::table(self::$relTypesTableName)->where('name',$relationName)->first();
        $inverse = $relRecord->{'inverse'}?$relRecord->{'inverse'}:$relRecord->{'id'};
        $inverseRecord = DB::table(self::$relTypesTableName)->where('id',$inverse)->first();
        return $inverseRecord->{'name'};
    }

    public function getId() {
        return $this->id;
    }

    public function save() {
        if ($this->id == null) {
            $this->id = DB::table(self::$tableName)->insertGetId(
                array(
                    'fromid'     => $this->fromId,
                    'toid'       => $this->toId,
                    'relationid' => $this->relationId,
                    'properties' => json_encode($this->properties),
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                    'composerid' => $this->composerid
                )
            );
        }
        else {
            DB::table(self::$tableName)
                ->where('id',$id)
                ->update(
                    array(
                        'fromid'     => $this->fromId,
                        'toid'       => $this->toId,
                        'relationid' => $this->relationId,
                        'properties' => json_encode($this->properties),
                        'updated_at' => date('Y-m-d H:i:s'),
                        'composerid' => $this->composerid
                    )
                );
        }
    }
}
