<?php
namespace DemocracyApps\CNP\Compositions\Inputs;

abstract class MultiselectInputHandler extends InputHandler {

    public static function extractValue($id, $input, &$inputMapItem)
    {
        //if ($id == 1) dd($input);
        $inputMapItem['value'] = null;
        if (array_key_exists($id, $input)) {
            $value = null;
            if ($input[$id]) {
                foreach ($input[$id] as $item) {
                    if (strlen($item) > 0) {
                        if ($value == null)
                            $value = $item;
                        else 
                            $value .= "," . $item;
                    }
                }
            }
            $inputMapItem['value'] = $value;
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
