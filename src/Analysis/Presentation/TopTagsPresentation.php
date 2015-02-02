<?php

namespace DemocracyApps\CNP\Analysis\Presentation;

use DemocracyApps\CNP\Analysis\AnalysisSet;
use DemocracyApps\CNP\Analysis\AnalysisSetItem;
use DemocracyApps\CNP\Analysis\Perspective;
use DemocracyApps\CNP\Entities\Element;

class TopTagsPresentation extends PerspectivePresentation {

    public static function getContent($perspective, $specification, $last)
    {
        $output = "<p>Analysis has not been performed.</p>";
        $analysis = $perspective->getAnalysis();

        if ($analysis != null) {
            $analysis = $analysis[0];
            // We are looking for a single set here.
            $sets = AnalysisSet::whereColumn('analysis_output', '=', $analysis->id);
            if ($sets != null) {
                $set = $sets[0];
                $tags = AnalysisSetItem::whereColumn('analysis_set', '=', $set->id);
                if ($tags != null) {
                    $output = "<ul class='perspective-tag-list'>";
                    foreach ($tags as $tag) {
                        $element = Element::find($tag->item);
                        if ($element != null) {
                            $output .= "<li class='perspective-tag-list-item'>";
                            $output .= $element->content;
                            $output .= "</li>";
                        }
                    }
                    $output .= "</ul>";
                }
            }

        }
        return $output;
    }
}