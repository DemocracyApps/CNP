<?php
namespace DemocracyApps\CNP\Compositions\Inputs;

abstract class PersonInputHandler extends InputHandler {

    public static function extractValue($id, $input, &$inputMapItem)
    {
        $inputMapItem['value'] = null;
        if (array_key_exists($id, $input)) {
            $val = array();
            $val['value'] = $input[$id];
            $val['isRef'] = false;
            $hiddenId = $id."_param";
            if (array_key_exists($hiddenId, $input)) {
                if ($input[$hiddenId]) {
                    $val['isRef'] = true;
                    $val['id'] = $input[$hiddenId];
                }
            }
            $inputMapItem['value'] = $val;
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
        return $inputMapItem['value']; // See above
    }

}
