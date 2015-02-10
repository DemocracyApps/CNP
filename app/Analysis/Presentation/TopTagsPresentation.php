<?php

namespace DemocracyApps\CNP\Analysis\Presentation;

use DemocracyApps\CNP\Analysis\AnalysisSet;
use DemocracyApps\CNP\Analysis\AnalysisSetItem;
use DemocracyApps\CNP\Graph\Element;

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
                    $advancedView = false;
                    if (array_key_exists('advancedView', $specification)) {
                        $advancedView = $specification['advancedView'];
                    }
                    $output = "<ul class='perspective-tag-list'>";
                    foreach ($tags as $tag) {
                        $element = Element::find($tag->item);
                        if ($element != null) {
                            $href = "/$perspective->project/compositions?filter=related&element=$element->id";
                            if ($advancedView) $href .= "&advancedView=1";
                            $output .= "<li class='perspective-tag-list-item'>";
                            $output .= "<a href='".$href."'>";
                            $output .= $element->content;
                            $output .= "</a>";
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