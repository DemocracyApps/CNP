<?php
namespace DemocracyApps\CNP\Compositions\Inputs;

abstract class SelectInputHandler extends InputHandler {

    public static function extractValue($id, $input, &$inputMapItem)
    {
        \Log::info("ID = ".$id);
        $inputMapItem['value'] = null;
        if (array_key_exists($id, $input)) {
            $inputMapItem['value'] = $input[$id];
        } 
    }
    public static function getValue ($inputMapItem)
    {
        $val = array();
        $val['isRef'] = false;
        $val['value'] = $inputMapItem['value'];
        return $val;
    }

}
