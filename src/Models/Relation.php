<?php
namespace DemocracyApps\CNP\Models;
use Illuminate\Support\Facades\DB as DB;

class Relation extends ModelBase 
{
    static $tableName = 'relations';
    static $relTypesTableName = 'relation_types';
    protected $id = null;
    protected $fromId = null;
    protected $toId = null;
    protected $relationId = null;

    function __construct($from, $to, $type) {
        $this->fromId = $from;
        $this->toId   = $to;
        $this->relationId = $type;
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
