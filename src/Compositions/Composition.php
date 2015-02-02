<?php namespace DemocracyApps\CNP\Compositions;

class Composition extends \Eloquent
{

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

    public static function projectCompositionsByReferentPaged($referentId, $sort, $desc, $project, $page = 1, $limit = 10) {

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

        $result = self::where('compositions.project', '=', $project)
            ->join('relations', 'relations.compositionid', '=', 'compositions.id')
            ->join('users', 'users.id', '=', 'compositions.userid')
            ->where('relations.toid', '=', $referentId)
            ->whereNotNull('top')
            ->select('compositions.id', 'compositions.title', 'compositions.created_at', 'users.name')
            ->distinct()
            ->orderBy($sort, $desc?'desc':'asc')
            ->skip(($page-1)*$limit)
            ->take($limit)
            ->get();
        $data = array();
        $data['items'] = $result->all();
        $data['total'] = sizeof($data['items']);
        return $data;
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

    public static function allUserCompositionsPaged ($user, $sort, $desc, $page=1, $limit=10)
    {
        $total = self::where('compositions.userid', '=', $user)->count();
        if ($sort == 'title') {
            $sort = 'compositions.title';
        }
        elseif ($sort == 'date') {
            $sort = 'compositions.created_at';
        }
        elseif ($sort == 'project') {
            $sort = 'compositions.project';
        }
        else {
            $sort = 'compositions.id';
        }

        $result = self::where('compositions.userid', '=', $user)
            ->join('projects', 'projects.id', '=', 'compositions.project')
            ->whereNotNull('top') // Skip the batch compositions
            ->orderBy($sort, $desc?'desc':'asc')
            ->skip(($page-1)*$limit)
            ->take($limit)
            ->select('compositions.id', 'compositions.title', 'compositions.project', 'compositions.created_at', 'projects.name as projectName')
            ->get();

        $data = array();
        $data['total'] = $total;
        $data['items'] = $result->all();
        return $data;
    }

    public static function randomCompositions($project, $composers, $max) {
        if ($composers != null) {
            $composers = implode(", ", $composers);
            $query = "SELECT id FROM compositions WHERE project = $project AND input_composer_id IN (" . $composers . ")";
        }
        else {
            $query = "select id from compositions where project=$project ;";
        }

        $items = \DB::select($query);
        $count = sizeof($items);
        $results = array();

        if ($count <= $max) {
            foreach ($items as $item) {
                $results[] = $item->id;
            }
        }
        else {
            $done = false;
            $n = 0;
            while (! $done) {
                $r = mt_rand (0, $count-1);
                if ($items[$r] != null) {
                    $results[] = $items[$r]->id;
                    $items[$r] = null;
                    ++$n;
                    if ($n >= $max) $done = true;
                }
            }
        }

        return $results;
    }
}
