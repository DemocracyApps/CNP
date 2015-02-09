<?php namespace DemocracyApps\CNP\Project\Compositions\Inputs;
use DemocracyApps\CNP\Project\Compositions\Composer;
use DemocracyApps\CNP\Project\Compositions\Composition;
use DemocracyApps\CNP\Utility\Notification;

class CSVInputProcessor 
{

    public function fire($queueJob, $data)
    {
        $userId = $data['userId'];
        $user = \DemocracyApps\CNP\Users\User::findOrFail($userId);
        \Auth::login($user);

        $filePath = $data['filePath'];
        $compositionId = $data['compositionId'];
        $composerId = $data['composerId'];
        $composition = Composition::find($compositionId);
        $composer = Composer::find($composerId);
        $composer->initializeForInput(null);
        \Log::info("Starting processing of " . $filePath);
        $notification = Notification::find($data['notificationId']);
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