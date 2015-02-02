<?php

namespace DemocracyApps\CNP\Analysis\Presentation;

use DemocracyApps\CNP\Analysis\Perspective;
use DemocracyApps\CNP\Compositions\Composition;

class RandomPresentation extends PerspectivePresentation {

    public static function getContent($perspective, $specification, $last)
    {
        $str = json_minify($specification);
        $cfig = json_decode($str, true);
        if (! array_key_exists('count',$cfig)) throw new \Exception("Random perspective requires count parameter");

        $composers = null;
        if (array_key_exists('composers', $cfig)) {
            $composers = $cfig['composers'];
        }
        $compositions = Composition::randomCompositions($perspective->project, $composers, $cfig['count']);

        $output = "<ul class='perspective-composition-list'>";
        foreach ($compositions as $compositionId) {
            $composition = Composition::find($compositionId);

            if ($composition != null) {
                $href = "/$perspective->project/compositions/$compositionId";
                $output .= "<li class='perspective-composition-list-item'>";
                $output .= "<a href='".$href."'>";
                $output .= $composition->title;
                $output .= "</a>";
                $output .= "</li>";
            }
        }
        $output .= "</ul>";
        return $output;
    }
}