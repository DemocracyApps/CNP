<?php
namespace DemocracyApps\CNP\Entities;
use Illuminate\Support\Facades\DB as DB;

class Tag extends Element 
{
    static  $classElementType = -1;
    protected $author;

    function __construct ($nm = null, $userid = null) {
        parent::__construct($nm, $userid, static::$classElementType);
    }

    static public function initialize() 
    {
        if (static::$classElementType < 0) {
            static::$classElementType = \CNP::getElementTypeId('Tag');
        }
    }

    static public function findByName($nm)
    {
        $data = DB::table(self::$tableName)
                  ->where('name', $nm)->first();
        $result = null;
        if ($data != null) {
            $result = new static($data->name, $data->userid);
            self::fill($result, $data);
        }
        return $result;
    }

}
