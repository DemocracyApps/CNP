<?php
namespace DemocracyApps\CNP\Inputs;

abstract class InputHandler {

    abstract static public function extractValue($id, $input, &$currentValue);
    abstract static public function getValue ($currentValue);

}