<?php namespace DemocracyApps\CNP\Compositions;

class Composition extends \Eloquent {

    public function createChildComposition($title)
    {
        $child = new Composition;
        $child->parent = $this->id;
        $child->input_composer_id = $this->input_composer_id;
        $child->project = $this->project;
        $child->userid = $this->userid;
        $child->title = $title;
        $child->save();
        return $child;
    }

    public static function allProjectCompositionsPaged ($project, $page=1, $limit=10) 
    {
        $total = self::where('project', '=', $project)->count();
        $result = self::where('project', '=', $project)
                        ->whereNotNull('top') // Skip the batch compositions
                        ->orderBy('id')
                        ->skip(($page-1)*$limit)
                        ->take($limit)
                        ->get();
        $data = array();
        $data['total'] = $total;
        $data['items'] = $result->all();
        return $data;    
    }

}
