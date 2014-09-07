<?php
namespace DemocracyApps\CNP\Inputs;

class Collector extends \Eloquent {
    protected $fullSpecification = null;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'collectors';

    protected function resolveFullSpecification ()
    {
        $spec = json_minify($this->specification);
        $spec = json_decode($spec, true);
        if (array_key_exists('baseSpecificationId', $spec)) {
            $nextCollector = Collector::find($spec['baseSpecificationId']);
            $tmpspec = $nextCollector->resolveFullSpecification($spec['baseSpecificationId']);
            if ( ! array_key_exists('input', $spec) && array_key_exists('input', $tmpspec)) {
                $spec['input'] = $tmpspec['input'];
            }
            if ( ! array_key_exists('elements', $spec) && array_key_exists('elements', $tmpspec)) {
                $spec['elements'] = $tmpspec['elements'];
            }
            if ( ! array_key_exists('relations', $spec) && array_key_exists('relations', $tmpspec)) {
                $spec['relations'] = $tmpspec['relations'];
            }
        }
        return $spec;
    }

	public function getFullSpecification ()
	{
        if ( ! $this->fullSpecification) {
            $this->fullSpecification = $this->resolveFullSpecification();
        }
    	return $this->fullSpecification;
	}

}
