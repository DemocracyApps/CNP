<?php
namespace DemocracyApps\CNP\Compositions\Inputs;

abstract class PickElementInputHandler extends InputHandler {

    public static function extractValue($id, $input, &$inputMapItem)
    {
        \Log::info("ID = ".$id);
        //if ($id == 1) dd($input);
        $inputMapItem['value'] = null;
        if (array_key_exists($id, $input)) {
            \Log::info("Ok, the value is ". $input[$id]);
            $inputMapItem['value'] = $input[$id];
        } 
    }
    public static function getValue ($inputMapItem)
    {
        $val = array();
        $val['isRef'] = true;
        if (array_key_exists('value', $inputMapItem))
            $val['value'] = $inputMapItem['value'];
        else 
            $val['value'] = null;
        return $val;
    }

}
