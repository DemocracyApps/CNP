<?php
namespace DemocracyApps\CNP\Graph;
use Illuminate\Support\Facades\DB;

class Relation 
{
    use ImplementsProperties;
    static $tableName = 'relations';
    static $relTypesTableName = 'relation_types';
    public $id = null;
    public $fromId = null;
    public $toId = null;
    public $relationId = null;
    public $compositionid = null;
    public $content = null;
    public $modifier = null;
    public $project = null;

    function __construct($from, $to, $type, $project) {
        $this->fromId = $from;
        $this->toId   = $to;
        $this->relationId = $type;
        $this->project = $project;
    }

    public static function getTableName()
    {
        return $tableName;
    }

    public function setContent($content)
    {
        $this->content = $content;
    }

    public function getContent()
    {
        return $this->content;
    }


    public function save() {
        if ($this->id == null) {
            $this->id = DB::table(self::$tableName)->insertGetId(
                array(
                    'fromid'     => $this->fromId,
                    'toid'       => $this->toId,
                    'relationid' => $this->relationId,
                    'project'    => $this->project,
                    'properties' => json_encode($this->properties),
                    'content' => $this->content,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                    'compositionid' => $this->compositionid
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
                        'project'    => $this->project,
                        'properties' => json_encode($this->properties),
                        'content' => $this->content,
                        'updated_at' => date('Y-m-d H:i:s'),
                        'compositionid' => $this->compositionid
                    )
                );
        }
    }

    protected static function fill ($instance, $data)
    {
        $instance->{'id'} = $data->id;
        $instance->{'fromId'} = $data->fromid;
        $instance->{'toId'} = $data->toid;
        $instance->{'relationId'} = $data->relationid;
        if (property_exists($data, 'project')) {
            $instance->{'project'} = $data->project;
        }
        if (property_exists($data, 'properties'))
            $instance->{'properties'} = (array) json_decode($data->properties);
        if (property_exists($data, 'content')) {
            $instance->{'content'} = $data->content;
        }
        if (property_exists($data, 'compositionid'))
            $instance->{'compositionid'} = $data->compositionid;
    }

    public function setCompositionId ($id)
    {
        $this->compositionid = $id;
    }

    public static function getProjectRelations ($project)
    {
        $records = DB::table(self::$tableName)
                    ->where('project', '=', $project)
                    ->select('relations.id', 'relations.fromid', 'relations.toid', 'relations.relationid', 
                             'relations.properties', 'relations.compositionid')
                    ->get();
        $relations = array();

        foreach ($records as $rec) {
            $item = new static($rec->fromid, $rec->toid, $rec->relationid, $project);
            self::fill($item,$rec);
            $relations[] = $item;
        }
        return $relations;
    }
    public static function getProjectRelationsPaged ($project, $start, $count)
    {
        $records = DB::table(self::$tableName)
                    ->where('project', '=', $project)
                    ->select('relations.id', 'relations.fromid', 'relations.toid', 'relations.relationid', 
                             'relations.properties', 'relations.compositionid')
                    ->skip($start)
                    ->take($count)
                    ->get();
        $relations = array();

        foreach ($records as $rec) {
            $item = new static($rec->fromid, $rec->toid, $rec->relationid, $project);
            self::fill($item,$rec);
            $relations[] = $item;
        }
        return $relations;
    }
    public static function countProjectRelations ($project)
    {
        $count = DB::table(self::$tableName)
                    ->where('project', '=', $project)
                    ->select('relations.id', 'relations.fromid', 'relations.toid', 'relations.relationid', 
                             'relations.properties', 'relations.compositionid')
                    ->count();
        return $count;
    }

    public static function getRelations ($fromId) 
    {
        $d = DB::table(self::$tableName)->where('fromid', $fromId)->orderBy('id')->get();

        $relations = array();

        foreach ($d as $data) {
            $item = new static($data->fromid, $data->toid, $data->relationid, $data->project);
            self::fill($item,$data);
            $relations[] = $item;
        }

        return $relations;
    }

    public static function createRelationPair($fromId, $toId, $relationName, $projectId, $props1 = null, $props2=null) 
    {
        $relation = array();
        $relRecord = DB::table(self::$relTypesTableName)->where('name',$relationName)->first();
        $relation[] = new static ($fromId, $toId, $relRecord->{'id'}, $projectId);
        if ($props1) $relation[0]->properties = $props1;
        $inverse = $relRecord->{'inverse'}?$relRecord->{'inverse'}:$relRecord->{'id'};
        $relation[] = new static ($toId, $fromId, $inverse, $projectId);
        if ($props2) $relation[1]->properties = $props2;
        return $relation;
    }

    public static function getInverseRelationName ($relationName) {
        $relRecord = DB::table(self::$relTypesTableName)->where('name',$relationName)->first();
        $inverse = $relRecord->{'inverse'}?$relRecord->{'inverse'}:$relRecord->{'id'};
        $inverseRecord = DB::table(self::$relTypesTableName)->where('id',$inverse)->first();
        return $inverseRecord->{'name'};
    }

    public static function getRelationType($relationName) {
        $relId = null;
        $relRecord = DB::table(self::$relTypesTableName)->where('name',$relationName)->first();
        if ($relRecord) $relId = $relRecord->{'id'};
        return $relId;
    }

    public function getId() {
        return $this->id;
    }
}
