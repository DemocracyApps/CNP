<?php
namespace DemocracyApps\CNP\Project\Compositions\Inputs;

abstract class PickElementInputHandler extends InputHandler {

    public static function extractValue($id, $input, &$inputMapItem)
    {
        //if ($id == 1) dd($input);
        $inputMapItem['value'] = null;
        if (array_key_exists($id, $input)) {
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
