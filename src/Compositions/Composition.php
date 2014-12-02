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

    public static function allProjectCompositionsPaged ($sort, $desc, $project, $page=1, $limit=10) 
    {
        $total = self::where('project', '=', $project)->count();
        if ($sort == 'title') {
            $sort = 'compositions.title';
        }
        elseif ($sort == 'date') {
            $sort = 'compositions.created_at';
        }
        elseif ($sort == 'user') {
            $sort = 'users.name';
        }
        else {
            $sort = 'compositions.id';
        }

        $result = self::where('project', '=', $project)
                    ->join('users', 'users.id', '=', 'compositions.userid')
                    ->whereNotNull('top') // Skip the batch compositions
                    ->orderBy($sort, $desc?'desc':'asc')
                    ->skip(($page-1)*$limit)
                    ->take($limit)
                    ->select('compositions.id', 'compositions.title', 'compositions.created_at', 'users.name')
                    ->get();

        $data = array();
        $data['total'] = $total;
        $data['items'] = $result->all();
        return $data;    
    }

}
