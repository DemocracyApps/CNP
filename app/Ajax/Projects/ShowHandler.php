<?php

namespace DemocracyApps\CNP\Ajax\Projects;

use DemocracyApps\CNP\Project\Compositions\Composer;
use DemocracyApps\CNP\Project\Project;
use Illuminate\Http\Request;


class ShowHandler extends \DemocracyApps\CNP\Ajax\BaseAjaxHandler {

    static function handle($func, Request $request)
    {
        if ($func == "setDefaultInputComposer") {
            return self::setDefaultInputComposer($request);
        }
        else if ($func == "setDefaultOutputComposer") {
            return self::setDefaultOutputComposer($request);
        }
        else {
            return self::notFoundResponse("Ajax function " . $func . " not found in Project.ShowHandler");
        }

        return null;
    }


    private static function setDefaultInputComposer(Request $request)
    {
        if (!$request->has('project')) return self::formatErrorResponse('No project specified');

        if (!$request->has('composer')) return self::formatErrorResponse('No default composer specified');
        $project = Project::find($request->get('project'));
        if (!$project) return self::notFoundResponse('Project with ID '.$request->get('project').' not found');
        if ($request->get('composer') < 0) {
            \Log::info("Deleting default input composer");
            $project->deleteProperty('defaultInputComposer');
        }
        else {
            $composer = Composer::find($request->get('composer'));
            if (!$composer) {
                return self::notFoundResponse('Composer with ID '.$request->get('composer').' not found');
            }
            $project->setProperty('defaultInputComposer', $composer->id);
        }
        $project->save();

        return self::oKResponse('Successfully set default input composer', null);
    }

    public static function setDefaultOutputComposer(Request $request)
    {
        if (!$request->has('project')) return self::formatErrorResponse('No project specified');
        if (!$request->has('composer')) return self::formatErrorResponse('No default composer specified');
        $project = Project::find($request->get('project'));
        if (!$project) return self::notFoundResponse('Project with ID '.$request->get('project').' not found');
        if ($request->get('composer') < 0) {
            $project->deleteProperty('defaultOutputComposer');
        }
        else {
            $composer = Composer::find($request->get('composer'));
            if (!$composer) {
                return self::notFoundResponse('Composer with ID '.$request->get('composer').' not found');
            }
            $project->setProperty('defaultOutputComposer', $composer->id);
        }
        $project->save();
        return self::okResponse('Successfully set default output composer', null);
    }

}