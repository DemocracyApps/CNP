<?php namespace DemocracyApps\CNP\Transformers;

 class ScapeTransformer extends Transformer {

 	public function transform ($scape) {
		return [
		'id'  			=> $scape->getId(),
		'name' 			=> $scape->getName(),
		'accessibility' => $scape->getProperty('access')
		];
	}
}
