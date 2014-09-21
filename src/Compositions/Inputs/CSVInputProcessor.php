<?php namespace DemocracyApps\CNP\Compositions\Inputs;
use DemocracyApps\CNP\Compositions\Composer;
use DemocracyApps\CNP\Compositions\Composition;
use \DemocracyApps\CNP\Entities as DAEntity;

class CSVInputProcessor 
{

    public function fire($job, $data)
    {
        $userId = $data['userId'];
        $user = \DemocracyApps\CNP\Entities\Eloquent\User::findOrFail($userId)->first();
        \Auth::login($user);

        $filePath = $data['filePath'];
        $compositionId = $data['compositionId'];
        $composerId = $data['composerId'];
        $composition = Composition::find($compositionId);
        $composer = Composer::find($composerId);
        $composer->initializeForInput(null);
        \Log::info("Starting processing of " . $filePath);
        $composer->processCsvInput($filePath, $composition);
        \Log::info("Completed processing of " . $filePath);
        $job->delete();
    }

}