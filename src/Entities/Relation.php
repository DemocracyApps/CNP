<?php
namespace DemocracyApps\CNP\Entities;
use Illuminate\Support\Facades\DB as DB;

class Relation 
{
    static $tableName = 'relations';
    static $relTypesTableName = 'relation_types';
    public $id = null;
    public $fromId = null;
    public $toId = null;
    public $relationId = null;

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
    }

    public static function getRelatedDenizens ($fromId, $relationName) 
    {
        $relId = null;
        $relRecord = DB::table(self::$relTypesTableName)->where('name',$relationName)->first();
        if ($relRecord) $relId = $relRecord->{'id'};
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

    public static function createRelationPair($fromId, $toId, $relationName) 
    {
        $relation = array();
        $relRecord = DB::table(self::$relTypesTableName)->where('name',$relationName)->first();
        $relation[] = new static ($fromId, $toId, $relRecord->{'id'});
        $inverse = $relRecord->{'inverse'}?$relRecord->{'inverse'}:$relRecord->{'id'};
        $relation[] = new static ($toId, $fromId, $inverse);
        return $relation;
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
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
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
                        'updated_at' => date('Y-m-d H:i:s')
                    )
                );
        }
    }
}
