<?php
namespace DemocracyApps\CNP\Inputs;
/**
 * This class is generated from an 'auto-interactive' input
 */
abstract class InputController {

    abstract static public function extractValue($id, $input, &$currentValue);
    abstract static public function getValue ($currentValue);

}