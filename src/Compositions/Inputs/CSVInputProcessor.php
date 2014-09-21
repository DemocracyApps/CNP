<?php namespace DemocracyApps\CNP\Compositions\Inputs;
use DemocracyApps\CNP\Compositions\Composer;
use DemocracyApps\CNP\Compositions\Composition;
use \DemocracyApps\CNP\Entities as DAEntity;

class CSVInputProcessor 
{

    public function fire($queueJob, $data)
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
        $job = \DemocracyApps\CNP\Utility\Job::find($data['jobId']);
        $job->messages = $composer->processCsvInput($filePath, $composition);
        $job->messages = $job->messages;
        $job->status = 'Completed';
        $job->completed_at = date('Y-m-d H:i:s');
        $job->save();
        \Log::info("Completed processing of job " . $job->id . " for " . $filePath);
        
        unlink($filePath);
        $queueJob->delete();
    }

}