<?php namespace DemocracyApps\CNP\Ajax\Project;
use DemocracyApps\CNP\Graph\Element;
use DemocracyApps\CNP\Project\Compositions\Composer;
use Illuminate\Http\Request;

/*
 * Currently not used but will be.
 *
 */
class AutoinputHandler extends \DemocracyApps\CNP\Ajax\BaseAjaxHandler {

    static function handle($func, Request $request)
    {
        if ($func == "personLookup") {
            return self::getPersonList($request);
        }
        else {
            return self::notFoundResponse("Ajax function " . $func . " not found in Project.AutoinputHandler");
        }

        return null;
    }

    private static function getPersonList(Request $request)
    {
        $term = $request->get('term');
        $composer = Composer::find($request->get('composer'));
        $list = Element::getElementsLike(\CNP::getElementTypeId('Person'), $term);
        $ret = array();
        foreach ($list as $item) {
            $ret[] = new PersonReference($item->name, $item->id);
        }
        //return self::oKResponse('Successfully set default input composer', json_encode($ret));
        return json_encode($ret);
    }
}