<?php namespace DemocracyApps\CNP\Entities;
use Illuminate\Support\Facades\DB as DB;
use Illuminate\Support\Collection;

class Denizen
{
    use ImplementsProperties;

    static    $classDenizenType = -1;
    static $tableName = 'denizens';
    protected $denizenType = null;
    public $id = null;
    public $scapeId = -1;
    public $name = null;
    public $content = null;
    public $userid = null;

    public function __construct($nm, $userid, $dtype=0) {
        $this->name = $nm;
        $this->userid = $userid;
        $this->denizenType = $dtype;
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
                    'scape'=> $this->scapeId,
                    'type' => $this->denizenType,
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
                        'scape'=> $this->scapeId,
                        'type' => $this->denizenType,
                        'content' => $this->content,
                        'properties' => json_encode($this->properties),
                        'updated_at' => date('Y-m-d H:i:s'),
                        'userid'     => $this->userid
                    )
                );
        }
    }
    
    protected static function fill ($instance, $data) 
    {
        $instance->{'id'} = $data->id;
        $instance->{'type'} = $data->type;
        if (property_exists($data, 'userid')) {
            $instance->{'userid'} = $data->userid;
        }
        $instance->{'name'} = $data->name;
        $instance->{'scapeId'} = $data->scape;
        if (property_exists($data, 'content')) {
            $instance->{'content'} = $data->content;
        }
        if (property_exists($data, 'properties')) {
            $instance->{'properties'} = (array) json_decode($data->properties);
        }
    }

    /**
     * Find a denizen by its primary key
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

    /*
     *  I know this absolutely does not belong in the Denizen class. Give me 
     *  some time to get this all figured out. Sigh.
     */
    public static function getVistaDenizens ($scape, $allowedComposers, $types, $page=1, $limit=3)
    {
        if ($types) {
            $total = DB::table(self::$tableName)
                        ->join('relations', 'relations.fromid', '=', 'denizens.id')
                        ->where('denizens.scape', '=', $scape)
                        ->whereIn('denizens.type', $types)
                        ->whereIn('relations.composerid', $allowedComposers)
                        ->select('denizens.id', 'denizens.type', 'denizens.scape', 'denizens.name')
                        ->distinct()
                        ->count('denizens.id');
            $records = DB::table(self::$tableName)
                        ->join('relations', 'relations.fromid', '=', 'denizens.id')
                        ->where('denizens.scape', '=', $scape)
                        ->whereIn('denizens.type', $types)
                        ->whereIn('relations.composerid', $allowedComposers)
                        ->select('denizens.id', 'denizens.type', 'denizens.scape', 'denizens.name')
                        ->orderBy('denizens.id')
                        ->distinct()
                        ->skip(($page-1)*$limit)
                        ->take($limit)
                        ->get();
           // dd(\DB::getQueryLog());
        }
        else {
            $total = DB::table(self::$tableName)
                        ->join('relations', 'relations.from', '=', 'denizens.id')
                        ->where('denizens.scape', '=', $scape)
                        ->whereIn('relations.composerid', $allowedComposers)
                        ->select('denizens.id', 'denizens.type', 'denizens.scape', 'denizens.name')
                        ->orderBy('denizens.id')
                        ->distinct()
                        ->count('denizens.id');
            $records = DB::table(self::$tableName)
                        ->join('relations', 'relations.from', '=', 'denizens.id')
                        ->where('denizens.scape', '=', $scape)
                        ->whereIn('relations.composerid', $allowedComposers)
                        ->select('denizens.id', 'denizens.type', 'denizens.scape', 'denizens.name')
                        ->orderBy('denizens.id')
                        ->distinct()
                        ->skip(($page-1)*$limit)
                        ->take($limit)
                        ->get();
        }
        $result = array();

        foreach ($records as $record) {
            $item = new static($record->name, null);
            self::fill($item,$record);
            $result[] = $item;
        }
        $data = array();
        $data['total'] = $total;
        $data['items'] = $result;
        return $data;        
    }

    /**
     * This gets all the denizens involved in the given composition in each of their unique 
     * relationships (i.e., a specific denizen will be returned as many times as it has 
     * relations). We return a hash by composer element ID, ensuring that a denizen is 
     * only stored once for a given hash index.
     * 
     * @param  Integer $compositionid      ID of the composition that created the relations
     * @param  Array                       Hash of denizens by composer element ID
     */
    public static function getCompositionDenizens ($compositionId, &$resultArray)
    {
        $records = DB::table(self::$tableName)
                    ->join('relations', 'relations.fromid', '=', 'denizens.id')
                    ->where('relations.compositionid', '=', $compositionId)
                    ->orderBy('denizens.id')
                    ->select('denizens.id', 'denizens.type', 'denizens.scape', 'denizens.name', 'denizens.content',
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

    public static function allScapeDenizens ($id, $types = null)
    {
        if ($types) {
            $d = DB::table(self::$tableName)->where('scape', '=', $id)
                                            ->whereIn('type', $types)
                                            ->orderBy('id')
                                            ->get();
        }
        else {
            if (static::$classDenizenType <= 0) { // All Denizens
                $d = DB::table(self::$tableName)->where('scape', '=', $id)->orderBy('id')->get();
            }
            else { // Specific Denizen Type
                $d = DB::table(self::$tableName)->where('scape', '=', $id)->orderBy('id')->get();
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

    public static function getDenizensLike ($scapeId, $like)
    {
         if (static::$classDenizenType <= 0) { // All Denizens
            $d = DB::table(self::$tableName)->where('scape', '=', $scapeId)
                                            ->where('name', 'ILIKE', "%".$like."%")
                                            ->orderBy('id')
                                            ->get();
        }
        else { // Specific Denizen Type
            $d = DB::table(self::$tableName)->where('scape', '=', $scapeId)
                                            ->where('type', '=', static::$classDenizenType)
                                            ->where('name', 'ILIKE', "%".$like."%")
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

    public static function allUserDenizens ($id) 
    {
        if (static::$classDenizenType <= 0) { // All Denizens
            $d = DB::table(self::$tableName)->where('userid', '=', $id)->orderBy('id')->get();
        }
        else { // Specific Denizen Type
            $d = DB::table(self::$tableName)->where('userid', '=', $id)
                                            ->where('type', '=', static::$classDenizenType)
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
     * Find all denizens or all by type
     *
     * @param  mixed $id
     * @return array of static
     */
    public static function all () 
    {
        if (static::$classDenizenType <= 0) { // All Denizens
            $d = DB::table(self::$tableName)->orderBy('id')->get();
        }
        else { // Specific Denizen Type
            $d = DB::table(self::$tableName)->where('type', '=', static::$classDenizenType)->orderBy('id')->get();
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
        if (static::$classDenizenType <= 0) { // All Denizens
            $total = DB::table(self::$tableName)->orderBy('id')->count();
            $d = DB::table(self::$tableName)->orderBy('id')->skip(($page-1)*$limit)->take($limit)->get();
        }
        else { // Specific Denizen Type
            $total = DB::table(self::$tableName)->where('type', '=', static::$classDenizenType)->count();
            $d = DB::table(self::$tableName)->where('type', '=', static::$classDenizenType)->orderBy('id')->skip(($page-1)*$limit)->take($limit)->get();
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
 