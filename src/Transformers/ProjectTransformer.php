<?php namespace DemocracyApps\CNP\Transformers;

 class ProjectTransformer extends Transformer {

 	public function transform ($project) {
		return [
		'id'  			=> $project->getId(),
		'name' 			=> $project->getName(),
		'access' => $project->getProperty('access'),
		'content' => $project->getContent()
		];
	}
}
