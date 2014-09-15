<?php
namespace DemocracyApps\CNP\Compositions\Inputs;

abstract class TextareaInputHandler extends InputHandler {

    public static function extractValue($id, $input, &$inputMapItem)
    {
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
