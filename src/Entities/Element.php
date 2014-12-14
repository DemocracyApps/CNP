<?php namespace DemocracyApps\CNP\Entities;
use Illuminate\Support\Facades\DB as DB;
use Illuminate\Support\Collection;

class Element
{
    use ImplementsProperties;

    static    $classElementType = -1;
    static $tableName = 'elements';
    protected $elementType = null;
    public $id = null;
    public $projectId = -1;
    public $name = null;
    public $content = null;
    public $userid = null;

    public function __construct($nm, $userid, $dtype=0) {
        $this->name = $nm;
        $this->userid = $userid;
        $this->elementType = $dtype;
    }

    public static function getTableName() {
        return $tableName;
    }

    static public function initialize()
    {
        
    }

    public function getId()
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function setContent($content)
    {
        $this->content = $content;
    }

    public function getContent()
    {
        return $this->content;
    }

    public function setUserId($uid) 
    {
        $this->userid = $uid;
    }
    public function save()
    {
        if ($this->id == null) {
            $this->id = DB::table(self::$tableName)->insertGetId(
                array(
                    'name' => $this->name,
                    'project'=> $this->projectId,
                    'type' => $this->elementType,
                    'content' => $this->content,
                    'properties' => json_encode($this->properties), // How do I move this to ImplementsProperties trait?
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                    'userid'     => $this->userid
                )
            );
        }
        else {
            DB::table(self::$tableName)
                ->where('id',$this->id)
                ->update(
                    array(
                        'name' => $this->name,
                        'project'=> $this->projectId,
                        'type' => $this->elementType,
                        'content' => $this->content,
                        'properties' => json_encode($this->properties),
                        'updated_at' => date('Y-m-d H:i:s'),
                        'userid'     => $this->userid
                    )
                );
        }
    }
    
    protected static function fill ($instance, $data, $projectId = -1) 
    {
        $instance->{'id'} = $data->id;
        $instance->{'type'} = $data->type;
        if (property_exists($data, 'userid')) {
            $instance->{'userid'} = $data->userid;
        }
        $instance->{'name'} = $data->name;
        if (property_exists($data, 'projectId')) {
            $instance->{'projectId'} = $data->project;
        }
        else {
            $instance->{'projectId'} = $projectId;
        }
        if (property_exists($data, 'content')) {
            $instance->{'content'} = $data->content;
        }
        if (property_exists($data, 'properties')) {
            $instance->{'properties'} = (array) json_decode($data->properties);
        }
    }

    /**
     * Find a element by its primary key
     *
     * @param  mixed $id
     * @return static
     */
    public static function find ($id) 
    {
        $data = DB::table(self::$tableName)
                  ->where('id', $id)->first();
        $result = null;
        if ($data != null) {
            $result = new static($data->name, $data->userid);
            self::fill($result, $data);
        }
        return $result;
    }

    static public function findByContent($nm, $ignoreType = false)
    {
        if ($ignoreType) {
            $data = DB::table(self::$tableName)
                      ->where('content', '=', $nm)->first();
        }
        else {
            $data = DB::table(self::$tableName)
                      ->where('content', '=', $nm)
                      ->where('type', '=', static::$classElementType)
                      ->first();            
        }
        $result = null;
        if ($data != null) {
            $result = new static($data->name, $data->userid);
            self::fill($result, $data);
        }
        return $result;
    }

    /**
     * This gets all the elements involved in the given composition in each of their unique 
     * relationships (i.e., a specific element will be returned as many times as it has 
     * relations). We return a hash by composer element ID, ensuring that a element is 
     * only stored once for a given hash index.
     * 
     * @param  Integer $compositionid      ID of the composition that created the relations
     * @param  Array                       Hash of elements by composer element ID
     */
    public static function getCompositionElements ($compositionId, &$resultArray)
    {
        $records = DB::table(self::$tableName)
                    ->join('relations', 'relations.fromid', '=', 'elements.id')
                    ->where('relations.compositionid', '=', $compositionId)
                    ->orderBy('elements.id')
                    ->select('elements.id', 'elements.type', 'elements.project', 'elements.name', 'elements.content',
                             'relations.properties as rprops')
                    ->distinct()
                    ->get();

        foreach ($records as $record) {
            $item = new static($record->name, null);
            self::fill($item,$record);
            if ($record->rprops) {
                $props = (array) json_decode($record->rprops);
                if (array_key_exists('composerElements', $props)) {
                    $elemId = explode(',', $props['composerElements'])[0];
                    if (array_key_exists($elemId, $resultArray)) {
                        if (! in_array($item, $resultArray[$elemId])) $resultArray[$elemId][] = $item;
                    }
                    else {
                        $resultArray[$elemId] = array($item);
                    }
                }
            }
        }
    }

    public static function getProjectElements ($project) {
       $records = DB::table(self::$tableName)
                    ->join('relations', 'relations.fromid', '=', 'elements.id')
                    ->where('relations.project', '=', $project)
                    ->orderBy('elements.id')
                    ->select('elements.id', 'elements.type', 'elements.name', 'elements.content', 'elements.userid')
                    ->distinct()
                    ->get();
        $result = array();

        foreach ($records as $record) {
            $item = new static($record->name, $record->userid);
            self::fill($item,$record, $project);
            $result[] = $item;
        }
        return $result;        
    }

    public static function getRelatedElements ($fromId, $relationName) 
    {
        $relId = null;
        if ($relationName) {
            $relId = Relation::getRelationType($relationName);
        }
        if ($relId) {
            $records = DB::table(self::$tableName)
                        ->join('relations', 'relations.toid', '=', 'elements.id')
                        ->where('relations.fromid', $fromId)
                        ->andWhere('relationid', '=', $relId)
                        ->orderBy('elements.id')
                        ->select('elements.id', 'elements.type', 'elements.name', 'elements.content', 'elements.userid', 'elements.project')
                        ->distinct()
                        ->get();
        }
        else {
            $records = DB::table(self::$tableName)
                        ->join('relations', 'relations.toid', '=', 'elements.id')
                        ->where('relations.fromid', $fromId)
                        ->orderBy('elements.id')
                        ->select('elements.id', 'elements.type', 'elements.name', 'elements.content', 'elements.userid', 'elements.project')
                        ->distinct()
                        ->get();
        }

        $result = array();

        foreach ($records as $record) {
            $item = new static($record->name, $record->userid);
            self::fill($item,$record, $record->project);
            $result[] = $item;
        }
        return $result;        
    }

    public static function countRelatedCompositions ($compositionId, $fromId) 
    {
        $count = DB::table('relations')
                    ->join('elements', 'relations.toid', '=', 'elements.id')
                    ->where('relations.fromid', '=', $fromId)
                    ->where('relations.compositionid', '<>', $compositionId)
                    ->where('elements.type', '=', '0')
                    ->select('relations.compositionid')
                    ->distinct()
                    ->count();
        return $count;     
    }

    public static function getRelatedCompositions ($compositionId, $fromId) 
    {
        $records = DB::table('relations')
                    ->join('elements', 'relations.toid', '=', 'elements.id')
                    ->where('relations.fromid', '=', $fromId)
                    ->where('relations.compositionid', '<>', $compositionId)
                    ->where('elements.type', '=', '0')
                    ->orderBy('relations.compositionid')
                    ->select('relations.compositionid')
                    ->distinct()
                    ->get();
        return sizeof($records);     
    }

    public static function allProjectElements ($id, $types = null)
    {
        if ($types) {
            $d = DB::table(self::$tableName)->where('project', '=', $id)
                                            ->whereIn('type', $types)
                                            ->orderBy('id')
                                            ->get();
        }
        else {
            if (static::$classElementType <= 0) { // All Elements
                $d = DB::table(self::$tableName)->where('project', '=', $id)->orderBy('id')->get();
            }
            else { // Specific Element Type
                $d = DB::table(self::$tableName)->where('project', '=', $id)->orderBy('id')->get();
            }
        }
        $result = array();

        foreach ($d as $data) {
            $item = new static($data->name, $data->userid);
            self::fill($item,$data);
            $result[] = $item;
        }
        return $result;        
    }

    public static function getElementsLike ($projectId, $like)
    {
         if (static::$classElementType <= 0) { // All Elements
            $d = DB::table(self::$tableName)->where('project', '=', $projectId)
                                            ->where('content', 'ILIKE', "%".$like."%")
                                            ->orderBy('id')
                                            ->get();
        }
        else { // Specific Element Type
            $d = DB::table(self::$tableName)->where('project', '=', $projectId)
                                            ->where('type', '=', static::$classElementType)
                                            ->where('content', 'ILIKE', "%".$like."%")
                                            ->orderBy('id')
                                            ->get();
        }

        $result = array();

        foreach ($d as $data) {
            $item = new static($data->name, $data->userid);
            self::fill($item,$data);
            $result[] = $item;
        }
        return $result;        
    }

    public static function allUserElements ($id, $type = null) 
    {
        if (static::$classElementType <= 0 && $type == null) { // All Elements
            $d = DB::table(self::$tableName)->where('userid', '=', $id)->orderBy('id')->get();
        }
        else { // Specific Element Type
            if ($type == null) $type = static::$classElementType;
            $d = DB::table(self::$tableName)->where('userid', '=', $id)
                                            ->where('type', '=', $type)
                                            ->orderBy('id')
                                            ->get();
        }

        $result = array();

        foreach ($d as $data) {
            $item = new static($data->name, $data->userid);
            self::fill($item,$data);
            $result[] = $item;
        }
        return $result;        
    }

    /**
     * Find all elements or all by type
     *
     * @param  mixed $id
     * @return array of static
     */
    public static function all () 
    {
        if (static::$classElementType <= 0) { // All Elements
            $d = DB::table(self::$tableName)->orderBy('id')->get();
        }
        else { // Specific Element Type
            $d = DB::table(self::$tableName)->where('type', '=', static::$classElementType)->orderBy('id')->get();
        }

        $result = array();

        foreach ($d as $data) {
            $item = new static($data->name, $data->userid);
            self::fill($item,$data);
            $result[] = $item;
        }
        return $result;
    }

    public static function allPaged ($page=1, $limit=10) 
    {
        if (static::$classElementType <= 0) { // All Elements
            $total = DB::table(self::$tableName)->orderBy('id')->count();
            $d = DB::table(self::$tableName)->orderBy('id')->skip(($page-1)*$limit)->take($limit)->get();
        }
        else { // Specific Element Type
            $total = DB::table(self::$tableName)->where('type', '=', static::$classElementType)->count();
            $d = DB::table(self::$tableName)->where('type', '=', static::$classElementType)->orderBy('id')->skip(($page-1)*$limit)->take($limit)->get();
        }

        $result = array();

        foreach ($d as $data) {
            $item = new static($data->name, $data->userid);
            self::fill($item,$data);
            $result[] = $item;
        }
        $data = array();
        $data['total'] = $total;
        $data['items'] = $result;
        return $data;
    }

    public function getRelations()
    {
        return Relation::getRelations($this->id);
    }

}
 