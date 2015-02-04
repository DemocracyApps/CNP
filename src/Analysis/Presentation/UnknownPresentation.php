<?php

namespace DemocracyApps\CNP\Analysis\Presentation;

use DemocracyApps\CNP\Compositions\Composition;

class UnknownPresentation extends PerspectivePresentation {

    public static function getContent($perspective, $specification, $last)
    {

        $output = "<ul class='perspective-composition-list'>";
        for ($i=0; $i<mt_rand(2,5); ++$i) {
            $href = "#";
            $output .= "<li class='perspective-composition-list-item'>";
            $output .= "<a href='".$href."'>";
            $val = $i + 1;
            $output .= "Set " . $val . " of interest.";
            $output .= "</a>";
            $output .= "</li>";
        }
        $output .= "</ul>";
        return $output;
    }
}