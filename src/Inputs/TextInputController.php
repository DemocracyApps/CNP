<?php
namespace DemocracyApps\CNP\Inputs;

abstract class TextInputController extends InputController {

    public static function extractValue($id, $input, &$inputMapItem)
    {
        $inputMapItem['value'] = null;
        if (array_key_exists($id, $input)) {
            $inputMapItem['value'] = $input[$id];
        } 
    }
    public static function getValue ($inputMapItem)
    {
        return $inputMapItem['value'];
    }

}
