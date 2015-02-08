<?php namespace DemocracyApps\CNP\Utility;


class Html {
    static public function cleanInput ($input)
    {
        return strip_tags($input);
    }

}