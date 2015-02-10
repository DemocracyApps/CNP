<?php

namespace DemocracyApps\CNP\Analysis\Presentation;

use \DemocracyApps\CNP\Analysis\Perspective;

abstract class PerspectivePresentation {

    abstract public static function getContent($perspective, $specification, $last);
}