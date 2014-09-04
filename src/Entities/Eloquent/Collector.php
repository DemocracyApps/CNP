<?php
namespace DemocracyApps\CNP\Entities\Eloquent;

class Collector extends \Eloquent {


	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'collectors';

	/**
	 * Recursively read in and merge specs
	 * @param  integer $id  ID of the end of the spec chain
	 * @return StdClass    Merged specification object
	 */
	public static function getFullSpecification ($id)
	{
    	$collector = Collector::find($id);
    	if ($collector) $spec = json_decode($collector->specification, true);
    	if (array_key_exists('baseSpecificationId', $spec)) {
    		$tmpspec = self::getFullSpecification($spec['baseSpecificationId']);
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

}
