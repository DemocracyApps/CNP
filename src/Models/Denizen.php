<?php
namespace DemocracyApps\CNP\Models;
use Illuminate\Support\Facades\DB as DB;
use Illuminate\Support\Collection;

class Denizen extends ModelBase
{
    static    $classScapeId = -1;
    static $tableName = 'denizens';
    protected $id = null;
    protected $scapeId = null;
    protected $denizenType = null;
    protected $name = null;
    protected $content = null;

    public function __construct($nm, $sId, $dtype=0) {
        $this->name = $nm;
        $this->denizenType = $dtype;
        $this->scapeId = $sId;
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

    public function save()
    {
        if ($this->id == null) {
            $this->id = DB::table(self::$tableName)->insertGetId(
                array(
                    'name' => $this->name,
                    'scape'=> $this->scapeId,
                    'type' => $this->denizenType,
                    'content' => $this->content,
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
                        'name' => $this->name,
                        'scape'=> $this->scapeId,
                        'type' => $this->denizenType,
                        'content' => $this->content,
                        'updated_at' => date('Y-m-d H:i:s')
                    )
                );
        }
    }
    
    protected static function fill ($instance, $data) 
    {
        $instance->{'id'} = $data->id;
        $instance->{'content'} = $data->content;
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
            $result = new static($data->name);
            self::fill($result, $data);
        }
        return $result;
    }
    /**
     * Find a denizen by its primary key
     *
     * @param  mixed $id
     * @return array of static
     */
    public static function all () 
    {
        if (static::$classScapeId <= 0) { // All Denizens
            $d = DB::table(self::$tableName)->get();
        }
        else { // Specific Denizen Type
            $d = DB::table(self::$tableName)->where('scape', '=', static::$classScapeId)->get();
        }

        $result = array();

        foreach ($d as $data) {
            $item = new static($data->name);
            self::fill($item,$data);
            $result[] = $item;
        }
        return $result;
    }
}
 