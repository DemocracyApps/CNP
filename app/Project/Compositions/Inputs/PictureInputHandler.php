<?php
namespace DemocracyApps\CNP\Compositions\Inputs;
use GrahamCampbell\Flysystem\Facades\Flysystem;
use DemocracyApps\CNP\Entities\Eloquent\AppState;

abstract class PictureInputHandler extends InputHandler {

    public static function extractValue($id, $input, &$inputMapItem)
    {
        $name =  null;
        if (\Input::hasFile($id)) {
            $file = \Input::file($id);
            $mtype = $file->getMimeType();
            $ext = '.jpg';
            if ($mtype == 'image/png') {
                $ext = '.png';
            }
            $picture = \File::get($file->getRealPath());
            $name = uniqid('pic') . $ext;
            Flysystem::put($name, $picture);
        }

        /*
         * We actually should be doing this lookup above, not just using Flysystem.
         */
        $picStorage = AppState::where('name', '=', 'pictureStorage')->first();

        $inputMapItem['value'] = $picStorage->value . '&' . $name;
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
