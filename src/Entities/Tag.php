<?php
namespace DemocracyApps\CNP\Entities;
use Illuminate\Support\Facades\DB as DB;

class Tag extends Denizen 
{
    static  $classDenizenType = -1;
    protected $author;

    function __construct ($nm = null, $userid = null) {
        parent::__construct($nm, $userid, static::$classDenizenType);
    }

    static public function initialize() 
    {
        if (static::$classDenizenType < 0) {
            static::$classDenizenType = \CNP::getDenizenTypeId('Tag');
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
