<?php namespace DemocracyApps\CNP\Entities;
use Illuminate\Support\Facades\DB as DB;
use Illuminate\Support\Collection;

class Denizen
{
    static    $classDenizenType = -1;
    static $tableName = 'denizens';
    protected $denizenType = null;
    public $id = null;
    public $scapeId = -1;
    public $name = null;
    public $content = null;
    public $properties = null;
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

    public function setProperty ($propName, $propValue)
    {
        if (! $this->properties) $this->properties = [];
        $this->properties[$propName] = $propValue;
    }

    public function getProperty ($propName)
    {
        $propValue = null;
        if ($this->properties) {
            $propValue = $this->properties[$propName];
        }
        return $propValue;
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
                    'properties' => json_encode($this->properties),
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
        $instance->{'userid'} = $data->userid;
        $instance->{'name'} = $data->name;
        $instance->{'scapeId'} = $data->scape;
        $instance->{'content'} = $data->content;
        $instance->{'properties'} = (array) json_decode($data->properties);
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

    public static function allScapeDenizens ($id)
    {
         if (static::$classDenizenType <= 0) { // All Denizens
            $d = DB::table(self::$tableName)->where('scape', '=', $id)->get();
        }
        else { // Specific Denizen Type
            $d = DB::table(self::$tableName)->where('scape', '=', $id)
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

    public static function getDenizensLike ($scapeId, $like)
    {
         if (static::$classDenizenType <= 0) { // All Denizens
            $d = DB::table(self::$tableName)->where('scape', '=', $scapeId)
                                            ->where('name', 'LIKE', "%".$like."%")
                                            ->get();
        }
        else { // Specific Denizen Type
            $d = DB::table(self::$tableName)->where('scape', '=', $scapeId)
                                            ->where('type', '=', static::$classDenizenType)
                                            ->where('name', 'LIKE', "%".$like."%")
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
            $d = DB::table(self::$tableName)->where('userid', '=', $id)->get();
        }
        else { // Specific Denizen Type
            $d = DB::table(self::$tableName)->where('userid', '=', $id)
                                            ->where('type', '=', static::$classDenizenType)
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
            $d = DB::table(self::$tableName)->get();
        }
        else { // Specific Denizen Type
            $d = DB::table(self::$tableName)->where('type', '=', static::$classDenizenType)->get();
        }

        $result = array();

        foreach ($d as $data) {
            $item = new static($data->name, $data->userid);
            self::fill($item,$data);
            $result[] = $item;
        }
        return $result;
    }
}
 