<?php
/**
 * Created by PhpStorm.
 * User: ericjackson
 * Date: 2/10/15
 * Time: 3:37 PM
 */

namespace DemocracyApps\CNP\Ajax\Project;


class PersonReference {

    public $label = null;
    public $value = null;
    public function __construct($label, $value) {
        $this->label = $label;
        $this->value = $value;
    }
}