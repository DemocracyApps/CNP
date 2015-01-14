<?php

namespace DemocracyApps\CNP\Entities;

use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * 
 */
class Project extends \Eloquent
{
    protected $table = 'projects';

    protected $properties = null;

    public function save(array $options = array()) 
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

    public function isViewAuthorized ($user) {
        $access = true;
        $projectAccess = $this->getProperty("access");
        if ( $projectAccess == 'Private') {
            $access = ProjectUser::projectViewAccess($this->id, $user);
        }
        return $access;
    }

    public function isPostAuthorized ($user) {
        $access = false;
        if ($this->getProperty("access") == "Open") { // Just requires a verified user

        }
        else {
            $access = ProjectUser::projectViewAccess($this->id, $user);
        }
        return $access;
    }

    public function isAdminAuthorized ($user) {
        $access = true;
        if ($this->getProperty("access") != "Open") {
            $access = ProjectUser::projectViewAccess($this->id, $user);
        }
        return $access;
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


