<?php namespace DemocracyApps\CNP\Transformers;

abstract class Transformer {

	/**
	 * Apply a transform to a collection of objects
	 * @param  array  $items array of objects to be transformed
	 * @return array         array of transformed objects
	 */
	public function transformCollection (array $items) 
	{
		return array_map([$this, 'transform'], $items);
	}

	abstract public function transform ($item);
}