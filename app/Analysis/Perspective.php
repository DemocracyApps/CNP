<?php
namespace DemocracyApps\CNP\Analysis;

use DemocracyApps\CNP\Utility\TableBackedObject;


class Perspective extends TableBackedObject {
    static protected $tableName = 'perspectives';
    static protected $tableFields = array('name', 'type', 'project', 'specification', 'description', 'requires_analysis',
        'last', 'created_at', 'updated_at');

    public $id;
    public $name;
    public $type;
    public $project;
    public $specification;
    public $description;
    public $requires_analysis;
    public $last;
    public $created_at;
    public $updated_at;

    public function getContent ()
    {
        $output = "<div class='perspective-main-div'>";
        $presentationClassName = '\DemocracyApps\CNP\Analysis\Presentation\\' . $this->type . "Presentation";

        if (class_exists($presentationClassName)) {
            $cfig = null;
            if ($this->specification != null) {
                $jp = \CNP::getJsonProcessor();

                $str = $jp->minifyJson($this->specification);
                $cfig = $jp->decodeJson($str, true);
            }
            $reflectionMethod = new \ReflectionMethod($presentationClassName, 'getContent');
            $output .= $reflectionMethod->invokeArgs(null, array($this, $cfig, $this->last));
        }
        else {
            throw new \Exception("No presentation class defined for perspective " . $this->type);
        }

        $output .= "</div>";
        //dd($output);
        return $output;

    }

    public function getAnalysis() {
        return AnalysisOutput::whereColumn('perspective', '=', $this->id);
    }
}