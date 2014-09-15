<?php
namespace DemocracyApps\CNP\Outputs;

use \DemocracyApps\CNP\Inputs\Composer;

class ComposerOutputDriver extends \Eloquent {
    protected $table = 'composer_output_drivers';
    protected $composer = null;
    protected $outputSpec = null;
    protected $inputSpec = null;

    protected $denizensMap = null;

    public function initialize(Composer $composer, $denizensMap) 
    {
        $this->composer = $composer;
        $this->userid = \Auth::user()->getId();
        $this->expires = date('Y-m-d H:i:s', time() + 24 * 60 * 60);

        $this->outputSpec = $composer->getOutputSpec();

        $this->denizensMap = $denizensMap;
    }

}