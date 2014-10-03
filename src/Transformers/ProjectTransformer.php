<?php namespace DemocracyApps\CNP\Transformers;

 class ProjectTransformer extends Transformer {

 	public function transform ($project) {
		return [
		'id'  			=> $project->id,
		'name' 			=> $project->name,
		'access' => $project->getProperty('access'),
		'content' => $project->description
		];
	}
}
