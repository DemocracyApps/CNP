<?php namespace DemocracyApps\CNP\Transformers;

 class ScapeTransformer extends Transformer {

 	public function transform ($scape) {
		return [
		'id'  			=> $scape->getId(),
		'name' 			=> $scape->getName(),
		'access' => $scape->getProperty('access'),
		'content' => $scape->getContent()
		];
	}
}
