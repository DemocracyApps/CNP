<?php
namespace DemocracyApps\CNP\Models;
use Illuminate\Support\Facades\DB as DB;

class Denizen extends ModelBase 
{
    static $tableName = 'denizens';
    protected $id = null;
    protected $scapeId = null;
    protected $denizenType = null;
    protected $name = null;
    protected $content = null;

    function __construct($nm, $sId, $dtype=0) {
        $this->name = $nm;
        $this->denizenType = $dtype;
        $this->scapeId = $sId;
    }

    public function getId() {
        return $this->id;
    }

    public function save() {
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
}
