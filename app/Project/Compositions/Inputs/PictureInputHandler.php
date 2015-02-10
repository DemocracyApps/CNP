<?php
namespace DemocracyApps\CNP\Project\Compositions\Inputs;
use DemocracyApps\CNP\Utility\AppState;

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
            $disk = \Storage::disk('s3');
            $disk->put($name, $picture);
        }

        /*
         * We actually should be doing this lookup above, not just using Flysystem.
         */
        $picStorage = AppState::whereColumnFirst('name', '=', 'pictureStorage');

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
