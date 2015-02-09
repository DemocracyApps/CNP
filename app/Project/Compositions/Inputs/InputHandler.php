<?php
namespace DemocracyApps\CNP\Project\Compositions\Inputs;

abstract class InputHandler {

    abstract static public function extractValue($id, $input, &$currentValue);
    abstract static public function getValue ($currentValue);

}