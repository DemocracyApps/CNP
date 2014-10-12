<?php
namespace DemocracyApps\CNP\Compositions\Inputs;

abstract class TextInputHandler extends InputHandler {

    public static function extractValue($id, $input, &$inputMapItem)
    {
        \Log::info("In text input handler with id = ". $id);
        $inputMapItem['value'] = null;
        if (array_key_exists($id, $input)) {
            $inputMapItem['value'] = $input[$id];
        } 
    }
    public static function getValue ($inputMapItem)
    {
        $val = array();
        $val['isRef'] = false;
        if (array_key_exists('value', $inputMapItem))
            $val['value'] = $inputMapItem['value'];
        else 
            $val['value'] = null;

        return $val;
    }

}
