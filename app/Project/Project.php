<?php

namespace DemocracyApps\CNP\Project;

use DemocracyApps\CNP\Utility\TableBackedObject;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use \DemocracyApps\CNP\User;

/**
 * 
 */
class Project extends TableBackedObject
{
    static  $tableName = 'projects';
    static protected $tableFields = array('name', 'description', 'json_properties',
        'userid', 'terms',
        'created_at', 'updated_at');
    public $name = null;
    public $description = null;
    public $json_properties = null;
    public $userid = null;
    public $terms = null;
    public $created_at = null;
    public $updated_at = null;


    protected $properties = null;

    public function save()
    {
        if ($this->properties) {
            $this->json_properties = json_encode($this->properties); // How do I move this to ImplementsProperties trait?
        }
        else {
            $this->json_properties = null;
        }
        parent::save($options);
    }

    protected function checkProperties() 
    {
        if ($this->json_properties && ! $this->properties) {
            $this->properties = (array) json_decode($this->json_properties);
        }
    }

    public function setProperty ($propName, $propValue)
    {
        $this->checkProperties();
        if (! $this->properties) $this->properties = [];
        $this->properties[$propName] = $propValue;
    }

    public function hasProperty ($propName) 
    {
        $this->checkProperties();
        $hasProperty = false;
        if ($this->properties) {
            if (array_key_exists($propName, $this->properties)) {
                $hasProperty = true;
            }
        }
        return $hasProperty;
    }

    public function deleteProperty ($propName) 
    {
        if ($this->hasProperty($propName)) {
            unset($this->properties[$propName]);
        }
    }

    public function getProperty ($propName)
    {
        $this->checkProperties();
        $propValue = null;
        if ($this->properties) {
            $propValue = $this->properties[$propName];
        }
        return $propValue;
    }

    public function viewAuthorization ($user) {
        $access = new \stdClass();
        $access->allowed = true;
        $access->reason = "";
        $projectAccess = $this->getProperty("access");
        if ( $projectAccess == 'Private') {
            if (! User::checkVerified($user)) {
                $access->allowed = false;
                $access->reason = "verification";
            }
            else {
                $access->allowed = ProjectUser::projectViewAccess($this->id, $user);
                if (! $access->allowed) {
                    $access->reason = "authorization";
                }
            }
        }
        return $access;

    }

    public function postAuthorization ($user) {
        $access = new \stdClass();
        $access->allowed = false;
        $access->reason = "";
        if ( ! User::checkVerified($user)) { // All projects require at least this
            $access->reason = "verification";
        }
        else {
            if ($this->getProperty("access") != "Open") { // Just requires a verified user
                $access->allowed = ProjectUser::projectPostAccess($this->id, $user);
                if (!$access->allowed) {
                    $access->reason = "authorization";
                }
            }
            else {
                $access->allowed = true;
            }
        }
        return $access;
    }

    public function adminAuthorization ($user) {
        $access = new \stdClass();
        $access->allowed = false;
        $access->reason = "";
        if ( ! User::checkVerified($user)) { // All projects require at least this
            $access->reason = "verification";
        }
        else {
            if ($this->getProperty("access") != "Open") { // Just requires a verified user
                $access->allowed = ProjectUser::projectAdminAccess($this->id, $user);
                if (!$access->allowed) {
                    $access->reason = "administration";
                }
            }
        }
        return $access;
    }

    public function isViewAuthorized ($user) {
        $access = $this->viewAuthorization($user);
        return $access->allowed;
    }

    public function isPostAuthorized ($user) {
        $access = $this->postAuthorization($user);
        return $access->allowed;
    }

    public function isAdminAuthorized ($user) {
        $access = $this->adminAuthorization($user);
        return $access->allowed;
    }

    public static function checkViewAuthorized ($projectId, $user) {
        $project = Project::find($projectId);
        if ($project != null) {
            return $project->isViewAuthorized($user);
        }
        else {
            throw new NotFoundHttpException("Unknown project");
        }
    }
    public static function checkPostAuthorized ($projectId, $user) {
        $project = Project::find($projectId);
        if ($project != null) {
            return $project->isPostAuthorized($user);
        }
        else {
            throw new NotFoundHttpException("Unknown project");
        }
    }
    public static function checkAdminAuthorized ($projectId, $user) {
        $project = Project::find($projectId);
        if ($project != null) {
            return $project->isAdminAuthorized($user);
        }
        else {
            throw new NotFoundHttpException("Unknown project");
        }
    }

    public static function allUserProjects ($user) {
        $data = self::join('project_users', 'project_users.project', '=', 'projects.id')
            ->where ('project_users.user', '=', $user)
            ->where ('project_users.access', '=', 3)
            ->select('projects.*')
            ->get();

        return $data;
    }

}


