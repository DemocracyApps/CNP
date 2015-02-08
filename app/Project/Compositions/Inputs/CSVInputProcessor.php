<?php namespace DemocracyApps\CNP\Compositions\Inputs;
use DemocracyApps\CNP\Compositions\Composer;
use DemocracyApps\CNP\Compositions\Composition;
use \DemocracyApps\CNP\Entities as DAEntity;

class CSVInputProcessor 
{

    public function fire($queueJob, $data)
    {
        $userId = $data['userId'];
        $user = \DemocracyApps\CNP\Entities\Eloquent\User::findOrFail($userId);
        \Auth::login($user);

        $filePath = $data['filePath'];
        $compositionId = $data['compositionId'];
        $composerId = $data['composerId'];
        $composition = Composition::find($compositionId);
        $composer = Composer::find($composerId);
        $composer->initializeForInput(null);
        \Log::info("Starting processing of " . $filePath);
        $notification = \DemocracyApps\CNP\Utility\Notification::find($data['notificationId']);
        $notification->messages = $composer->processCsvInput($filePath, $composition);
        $notification->messages = $notification->messages;
        $notification->status = 'Completed';
        $notification->completed_at = date('Y-m-d H:i:s');
        $notification->save();
        \Log::info("Completed processing of job " . $notification->id . " for " . $filePath);
        
        unlink($filePath);
        $queueJob->delete();
    }

}